<?php

namespace App\Providers;

use App\Domain\Security\Models\AdminUser;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('credit.edit', fn (AdminUser $user) => in_array($user->role, ['super_admin', 'merchant_admin'], true));
        Gate::define('refund.approve', fn (AdminUser $user) => in_array($user->role, ['super_admin', 'merchant_admin'], true));
        Gate::define('exports.view', fn (AdminUser $user) => in_array($user->role, ['super_admin', 'merchant_admin', 'merchant_support'], true));
        Gate::define('billing.view', fn (AdminUser $user) => in_array($user->role, ['super_admin', 'merchant_admin'], true));
    }
}
