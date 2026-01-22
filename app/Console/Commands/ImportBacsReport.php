<?php

namespace App\Console\Commands;

use App\Domain\Bacs\Jobs\ProcessBacsReportJob;
use App\Domain\Bacs\Models\BacsReport;
use App\Domain\Merchants\Models\Merchant;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class ImportBacsReport extends Command
{
    protected $signature = 'ccdd:import-bacs-report {type} {file} {merchant_id}';
    protected $description = 'Import a Bacs report file from local storage';

    public function handle(): int
    {
        $type = strtoupper($this->argument('type'));
        $path = $this->argument('file');
        $merchantId = (int) $this->argument('merchant_id');

        if (!in_array($type, ['ARUDD', 'ADDACS'], true)) {
            $this->error('Type must be ARUDD or ADDACS.');
            return self::FAILURE;
        }

        if (!is_file($path)) {
            $this->error('File not found: ' . $path);
            return self::FAILURE;
        }

        $merchant = Merchant::find($merchantId);
        if (!$merchant) {
            $this->error('Merchant not found: ' . $merchantId);
            return self::FAILURE;
        }

        $filename = basename($path);
        $storedName = now()->format('Ymd_His') . '_' . $filename;
        $storedPath = Storage::disk('local')->putFileAs(
            config('ccdd.bacs_storage_path', 'bacs'),
            new File($path),
            $storedName
        );

        $report = BacsReport::create([
            'merchant_id' => $merchant->id,
            'type' => $type,
            'source' => 'local',
            'original_filename' => $filename,
            'file_path' => $storedPath,
            'status' => 'pending',
        ]);

        ProcessBacsReportJob::dispatch($report->id);

        $this->info('Queued report ' . $report->id . ' for processing.');

        return self::SUCCESS;
    }
}
