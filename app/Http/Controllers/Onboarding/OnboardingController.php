<?php

namespace App\Http\Controllers\Onboarding;

use App\Domain\Merchants\Models\Merchant;
use App\Domain\Onboarding\Models\OnboardingStep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    private array $defaultSteps = [
        'connect_site',
        'configure_bureau',
        'test_webhook',
        'test_ddi',
        'go_live',
    ];

    public function index(Request $request): View
    {
        $merchantId = (int) $request->query('merchant_id');
        $merchant = Merchant::find($merchantId);

        if ($merchant) {
            foreach ($this->defaultSteps as $step) {
                OnboardingStep::firstOrCreate([
                    'merchant_id' => $merchant->id,
                    'step_key' => $step,
                ], [
                    'status' => 'todo',
                    'updated_at' => now(),
                ]);
            }
        }

        $steps = $merchant
            ? OnboardingStep::where('merchant_id', $merchant->id)->get()
            : collect();

        return view('onboarding.index', [
            'merchant' => $merchant,
            'steps' => $steps,
        ]);
    }

    public function update(Request $request, OnboardingStep $step): RedirectResponse
    {
        $status = $request->input('status', 'done');
        $step->status = $status;
        $step->updated_at = now();
        $step->save();

        return redirect()->route('onboarding.index', ['merchant_id' => $step->merchant_id]);
    }
}
