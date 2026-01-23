<?php

namespace App\Http\Controllers\Portal;

use App\Domain\Customers\Models\Customer;
use App\Domain\Portal\Models\CustomerPortalToken;
use App\Domain\Portal\Models\PortalSsoToken;
use App\Support\Tokens\TokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly TokenService $tokenService)
    {
    }

    public function showLogin(): View
    {
        return view('portal.login');
    }

    public function sendLink(Request $request): View
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $customer = Customer::where('email', $validated['email'])->first();
        if (!$customer) {
            return view('portal.login', ['sent' => true]);
        }

        $token = $this->tokenService->generate();
        $hash = $this->tokenService->hash($token);

        CustomerPortalToken::create([
            'customer_id' => $customer->id,
            'token_hash' => $hash,
            'expires_at' => now()->addMinutes(config('ccdd.portal_token_ttl_minutes', 30)),
        ]);

        $link = url('/portal/auth/' . $token);

        Mail::send('emails.portal_magic_link', [
            'customer' => $customer,
            'link' => $link,
        ], function ($message) use ($customer) {
            $message->to($customer->email)->subject('Your customer portal link');
        });

        return view('portal.login', ['sent' => true]);
    }

    public function consumeToken(string $token): RedirectResponse
    {
        $hash = $this->tokenService->hash($token);
        $record = CustomerPortalToken::where('token_hash', $hash)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return redirect()->route('portal.login');
        }

        $record->used_at = now();
        $record->save();

        session(['portal_customer_id' => $record->customer_id]);

        return redirect()->route('portal.dashboard');
    }

    public function consumeSso(string $token): RedirectResponse
    {
        $hash = $this->tokenService->hash($token);
        $record = PortalSsoToken::where('token_hash', $hash)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return redirect()->route('portal.login');
        }

        $record->used_at = now();
        $record->save();

        session(['portal_customer_id' => $record->customer_id]);

        return redirect()->route('portal.dashboard');
    }

    public function logout(): RedirectResponse
    {
        session()->forget('portal_customer_id');
        return redirect()->route('portal.login');
    }
}
