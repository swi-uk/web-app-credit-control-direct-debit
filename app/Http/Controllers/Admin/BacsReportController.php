<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Bacs\Jobs\ProcessBacsReportJob;
use App\Domain\Bacs\Models\BacsReport;
use App\Domain\Merchants\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BacsReportController extends Controller
{
    public function create(): View
    {
        $merchants = Merchant::orderBy('name')->get();

        return view('admin.bacs.upload', [
            'merchants' => $merchants,
            'uploaded' => false,
        ]);
    }

    public function store(Request $request): View
    {
        $validated = $request->validate([
            'merchant_id' => ['required', 'exists:merchants,id'],
            'type' => ['required', 'in:ARUDD,ADDACS'],
            'report_file' => ['required', 'file'],
        ]);

        $file = $request->file('report_file');
        $storedName = now()->format('Ymd_His') . '_' . $file->getClientOriginalName();
        $path = Storage::disk('local')->putFileAs(config('ccdd.bacs_storage_path', 'bacs'), $file, $storedName);

        $report = BacsReport::create([
            'merchant_id' => $validated['merchant_id'],
            'type' => $validated['type'],
            'source' => 'upload',
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending',
        ]);

        ProcessBacsReportJob::dispatch($report->id);

        $merchants = Merchant::orderBy('name')->get();

        return view('admin.bacs.upload', [
            'merchants' => $merchants,
            'uploaded' => true,
            'reportId' => $report->id,
        ]);
    }
}
