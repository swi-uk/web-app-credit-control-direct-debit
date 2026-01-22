<?php

namespace App\Console\Commands;

use App\Domain\Audit\Models\AuditEvent;
use App\Domain\Merchants\Models\Merchant;
use App\Domain\Payments\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAdvanceNotices extends Command
{
    protected $signature = 'ccdd:send-advance-notices';
    protected $description = 'Send advance notice emails for upcoming payments';

    public function handle(): int
    {
        $merchants = Merchant::all();
        $sentCount = 0;

        foreach ($merchants as $merchant) {
            $days = $merchant->settings_json['advance_notice_days']
                ?? config('ccdd.advance_notice_days', 3);
            $targetDate = now()->addDays((int) $days)->toDateString();

            $payments = Payment::with('customer')
                ->where('merchant_id', $merchant->id)
                ->where('status', 'scheduled')
                ->whereNull('advance_notice_sent_at')
                ->whereDate('due_date', $targetDate)
                ->get();

            foreach ($payments as $payment) {
                $email = $payment->customer?->email;
                if (!$email) {
                    continue;
                }

                Mail::send('emails.advance_notice', [
                    'payment' => $payment,
                    'customer' => $payment->customer,
                    'merchant' => $merchant,
                ], function ($message) use ($email) {
                    $message->to($email)->subject('Direct Debit advance notice');
                });

                $payment->advance_notice_sent_at = now();
                $payment->advance_notice_channel = 'email';
                $payment->save();

                AuditEvent::create([
                    'merchant_id' => $payment->merchant_id,
                    'customer_id' => $payment->customer_id,
                    'event_type' => 'payment.advance_notice_sent',
                    'message' => null,
                    'payload_json' => [
                        'payment_id' => $payment->id,
                        'channel' => 'email',
                    ],
                    'created_at' => now(),
                ]);

                $sentCount++;
            }
        }

        $this->info('Advance notices sent: ' . $sentCount);

        return self::SUCCESS;
    }
}
