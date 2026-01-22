<?php

namespace App\Console\Commands;

use App\Domain\Bacs\Jobs\ProcessBacsReportJob;
use App\Domain\Bacs\Models\BacsReport;
use App\Domain\Bureau\Services\BureauService;
use App\Domain\Merchants\Models\MerchantSite;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FetchBureauReports extends Command
{
    protected $signature = 'ccdd:fetch-bureau-reports {--from=} {--to=}';
    protected $description = 'Fetch bureau reports via configured connectors';

    public function __construct(private readonly BureauService $bureauService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $from = $this->option('from') ? Carbon::parse($this->option('from')) : now()->subDays(7);
        $to = $this->option('to') ? Carbon::parse($this->option('to')) : now();

        $sites = MerchantSite::whereNotNull('settings_json')->get();
        $imported = 0;

        foreach ($sites as $site) {
            $connector = $this->bureauService->connectorFor($site);
            $reports = $connector->fetchReports($from, $to);
            foreach ($reports as $reportFile) {
                $contents = $connector->downloadReport($reportFile->remoteId);
                $hash = hash('sha256', $contents);
                $existingByHash = BacsReport::where('file_hash', $hash)
                    ->where('merchant_id', $site->merchant_id)
                    ->first();
                $existingByRemote = BacsReport::where('remote_id', $reportFile->remoteId)
                    ->where('merchant_id', $site->merchant_id)
                    ->first();
                if ($existingByRemote || $existingByHash) {
                    continue;
                }
                $storedName = now()->format('Ymd_His') . '_' . $reportFile->filename;
                $path = config('ccdd.bacs_storage_path', 'bacs') . '/' . $storedName;
                Storage::disk('local')->put($path, $contents);

                $report = BacsReport::create([
                    'merchant_id' => $site->merchant_id,
                    'type' => $reportFile->type ?? 'ARUDD',
                    'source' => 'bureau',
                    'original_filename' => $reportFile->filename,
                    'file_path' => $path,
                    'status' => 'pending',
                    'remote_id' => $reportFile->remoteId,
                    'file_hash' => $hash,
                ]);

                ProcessBacsReportJob::dispatch($report->id);
                $imported++;
            }
        }

        $this->info('Imported reports: ' . $imported);

        return self::SUCCESS;
    }
}
