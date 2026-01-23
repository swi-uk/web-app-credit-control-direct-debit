<?php

namespace App\Http\Controllers\Admin\Portfolio;

use App\Domain\Customers\Models\Customer;
use App\Domain\Payments\Models\Payment;
use App\Domain\Customers\Models\CreditProfile;
use App\Domain\Bacs\Models\BacsReportItem;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $next7 = Payment::whereIn('status', ['scheduled', 'submitted'])
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->sum('amount');
        $next30 = Payment::whereIn('status', ['scheduled', 'submitted'])
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->sum('amount');

        $delinquency = Payment::whereIn('status', ['unpaid_returned', 'failed_final'])->count();
        $totalPayments = Payment::count();
        $delinquencyRate = $totalPayments > 0 ? round(($delinquency / $totalPayments) * 100, 2) : 0;

        $aruddReasons = BacsReportItem::where('record_type', 'ARUDD')
            ->selectRaw('code, count(*) as total')
            ->groupBy('code')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $tierDistribution = CreditProfile::selectRaw('credit_tier_id, count(*) as total')
            ->groupBy('credit_tier_id')
            ->get();

        $topExposure = Customer::with('creditProfile')
            ->join('credit_profiles', 'credit_profiles.customer_id', '=', 'customers.id')
            ->orderByDesc('credit_profiles.current_exposure_amount')
            ->limit(5)
            ->get(['customers.*']);

        return view('admin.portfolio.index', [
            'next7' => $next7,
            'next30' => $next30,
            'delinquencyRate' => $delinquencyRate,
            'aruddReasons' => $aruddReasons,
            'tierDistribution' => $tierDistribution,
            'topExposure' => $topExposure,
        ]);
    }
}
