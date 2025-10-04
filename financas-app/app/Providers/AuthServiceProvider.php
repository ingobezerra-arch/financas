<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Account::class => \App\Policies\AccountPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\Transaction::class => \App\Policies\TransactionPolicy::class,
        \App\Models\RecurringTransaction::class => \App\Policies\RecurringTransactionPolicy::class,
        \App\Models\Budget::class => \App\Policies\BudgetPolicy::class,
        \App\Models\Goal::class => \App\Policies\GoalPolicy::class,
        \App\Models\BankIntegration::class => \App\Policies\BankIntegrationPolicy::class,
        \App\Models\SyncedTransaction::class => \App\Policies\SyncedTransactionPolicy::class,
        \App\Models\Debt::class => \App\Policies\DebtPolicy::class,
        \App\Models\PaymentPlan::class => \App\Policies\PaymentPlanPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
