<?php

namespace App\Http\Controllers\Admin\Ops;

use App\Domain\Bacs\Models\BacsReport;
use App\Domain\Submission\Models\SubmissionBatch;
use App\Domain\Webhooks\Models\WebhookDelivery;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(): View
    {
        $failedWebhooks = WebhookDelivery::where('status', 'failed')->orderByDesc('id')->limit(50)->get();
        $failedBatches = SubmissionBatch::where('status', 'failed')->orderByDesc('id')->limit(50)->get();
        $failedReports = BacsReport::where('status', 'failed')->orderByDesc('id')->limit(50)->get();

        $failedJobs = [];
        if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
            $failedJobs = DB::table('failed_jobs')->orderByDesc('failed_at')->limit(50)->get();
        }

        return view('admin.ops.index', [
            'failedWebhooks' => $failedWebhooks,
            'failedBatches' => $failedBatches,
            'failedReports' => $failedReports,
            'failedJobs' => $failedJobs,
        ]);
    }
}
