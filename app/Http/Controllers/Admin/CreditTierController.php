<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Credit\Models\CreditTier;
use App\Domain\Credit\Models\CreditTierRule;
use App\Domain\Merchants\Models\Merchant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class CreditTierController extends Controller
{
    public function index(): View
    {
        $tiers = CreditTier::with('merchant', 'rules')->orderBy('merchant_id')->orderBy('priority')->get();

        return view('admin.credit_tiers.index', [
            'tiers' => $tiers,
        ]);
    }

    public function create(): View
    {
        $merchants = Merchant::orderBy('name')->get();

        return view('admin.credit_tiers.create', [
            'merchants' => $merchants,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'merchant_id' => ['required', 'exists:merchants,id'],
            'name' => ['required', 'string', 'max:120'],
            'max_exposure_amount' => ['required', 'numeric'],
            'max_days' => ['required', 'integer'],
            'priority' => ['required', 'integer'],
            'is_default' => ['nullable'],
            'is_active' => ['nullable'],
            'min_successful_collections' => ['nullable', 'integer'],
            'max_bounces_60d' => ['nullable', 'integer'],
            'min_account_age_days' => ['nullable', 'integer'],
        ]);

        if (!empty($validated['is_default'])) {
            CreditTier::where('merchant_id', $validated['merchant_id'])
                ->update(['is_default' => false]);
        }

        $tier = CreditTier::create([
            'merchant_id' => $validated['merchant_id'],
            'name' => $validated['name'],
            'max_exposure_amount' => $validated['max_exposure_amount'],
            'max_days' => $validated['max_days'],
            'priority' => $validated['priority'],
            'is_default' => !empty($validated['is_default']),
            'is_active' => !empty($validated['is_active']),
        ]);

        CreditTierRule::create([
            'merchant_id' => $validated['merchant_id'],
            'tier_id' => $tier->id,
            'min_successful_collections' => $validated['min_successful_collections'] ?? 0,
            'max_bounces_60d' => $validated['max_bounces_60d'] ?? 999,
            'min_account_age_days' => $validated['min_account_age_days'] ?? 0,
        ]);

        return redirect()->route('admin.credit_tiers.index');
    }

    public function edit(CreditTier $creditTier): View
    {
        $creditTier->load('rules');
        $merchants = Merchant::orderBy('name')->get();
        $rule = $creditTier->rules->first();

        return view('admin.credit_tiers.edit', [
            'tier' => $creditTier,
            'rule' => $rule,
            'merchants' => $merchants,
        ]);
    }

    public function update(Request $request, CreditTier $creditTier): RedirectResponse
    {
        $validated = $request->validate([
            'merchant_id' => ['required', 'exists:merchants,id'],
            'name' => ['required', 'string', 'max:120'],
            'max_exposure_amount' => ['required', 'numeric'],
            'max_days' => ['required', 'integer'],
            'priority' => ['required', 'integer'],
            'is_default' => ['nullable'],
            'is_active' => ['nullable'],
            'min_successful_collections' => ['nullable', 'integer'],
            'max_bounces_60d' => ['nullable', 'integer'],
            'min_account_age_days' => ['nullable', 'integer'],
        ]);

        if (!empty($validated['is_default'])) {
            CreditTier::where('merchant_id', $validated['merchant_id'])
                ->update(['is_default' => false]);
        }

        $creditTier->merchant_id = $validated['merchant_id'];
        $creditTier->name = $validated['name'];
        $creditTier->max_exposure_amount = $validated['max_exposure_amount'];
        $creditTier->max_days = $validated['max_days'];
        $creditTier->priority = $validated['priority'];
        $creditTier->is_default = !empty($validated['is_default']);
        $creditTier->is_active = !empty($validated['is_active']);
        $creditTier->save();

        $rule = $creditTier->rules()->first();
        if (!$rule) {
            $rule = new CreditTierRule([
                'merchant_id' => $validated['merchant_id'],
                'tier_id' => $creditTier->id,
            ]);
        }
        $rule->merchant_id = $validated['merchant_id'];
        $rule->min_successful_collections = $validated['min_successful_collections'] ?? 0;
        $rule->max_bounces_60d = $validated['max_bounces_60d'] ?? 999;
        $rule->min_account_age_days = $validated['min_account_age_days'] ?? 0;
        $rule->save();

        return redirect()->route('admin.credit_tiers.index');
    }
}
