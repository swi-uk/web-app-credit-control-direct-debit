<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payments\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter');
        $query = Payment::with(['customer', 'sourceSite'])->orderByDesc('id');

        if ($filter === 'today') {
            $query->whereDate('due_date', now()->toDateString());
        }

        if ($filter === 'bounced') {
            $query->whereIn('status', ['unpaid_returned', 'retry_scheduled', 'failed_final']);
        }

        $payments = $query->get();

        return view('admin.payments.index', [
            'payments' => $payments,
            'filter' => $filter,
        ]);
    }
}
