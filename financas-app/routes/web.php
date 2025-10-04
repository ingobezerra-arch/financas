<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BankIntegrationController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\PaymentPlanController;
use App\Http\Controllers\PaymentScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.show'); // Alias em português
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/theme', [ProfileController::class, 'updateTheme'])->name('profile.update-theme');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Account routes
    Route::resource('accounts', AccountController::class);
    Route::patch('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
    
    // Category routes
    Route::resource('categories', CategoryController::class);
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    
    // Transaction routes
    Route::resource('transactions', TransactionController::class);
    
    // Recurring Transaction routes
    Route::resource('recurring-transactions', RecurringTransactionController::class);
    Route::post('recurring-transactions/{recurringTransaction}/execute', [RecurringTransactionController::class, 'execute'])
        ->name('recurring-transactions.execute');
    Route::post('recurring-transactions/{recurringTransaction}/toggle', [RecurringTransactionController::class, 'toggle'])
        ->name('recurring-transactions.toggle');
    
    // Budget routes
    Route::resource('budgets', BudgetController::class);
    Route::post('budgets/{budget}/toggle', [BudgetController::class, 'toggle'])
        ->name('budgets.toggle');
    
    // Goal routes
    Route::resource('goals', GoalController::class);
    Route::post('goals/{goal}/add-contribution', [GoalController::class, 'addContribution'])
        ->name('goals.add-contribution');
    Route::post('goals/{goal}/remove-contribution', [GoalController::class, 'removeContribution'])
        ->name('goals.remove-contribution');
    Route::post('goals/{goal}/toggle-status', [GoalController::class, 'toggleStatus'])
        ->name('goals.toggle-status');
    
    // Report routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
        Route::get('/budgets', [ReportController::class, 'budgets'])->name('budgets');
        Route::get('/goals', [ReportController::class, 'goals'])->name('goals');
        Route::get('/chart-data', [ReportController::class, 'getChartData'])->name('chart-data');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
    });
    
    // Bank Integration routes
    Route::prefix('bank-integrations')->name('bank-integrations.')->group(function () {
        Route::get('/', [BankIntegrationController::class, 'index'])->name('index');
        Route::get('/create', [BankIntegrationController::class, 'create'])->name('create');
        Route::get('/setup', function() {
            return view('bank-integrations.setup');
        })->name('setup');
        Route::get('/transactions/list', [BankIntegrationController::class, 'transactions'])->name('transactions');
        Route::post('/', [BankIntegrationController::class, 'store'])->name('store');
        Route::get('/{bankIntegration}', [BankIntegrationController::class, 'show'])->name('show');
        Route::get('/{bankIntegration}/edit', [BankIntegrationController::class, 'edit'])->name('edit');
        Route::patch('/{bankIntegration}', [BankIntegrationController::class, 'update'])->name('update');
        Route::delete('/{bankIntegration}', [BankIntegrationController::class, 'destroy'])->name('destroy');
        Route::post('/{bankIntegration}/sync', [BankIntegrationController::class, 'sync'])->name('sync');
        Route::post('/transactions/{syncedTransaction}/process', [BankIntegrationController::class, 'processTransaction'])->name('transactions.process');
    });
    
    // Debt Management routes
    Route::resource('debts', DebtController::class);
    Route::get('debts/{debt}/simulate', [DebtController::class, 'simulate'])->name('debts.simulate');
    Route::post('debts/{debt}/record-payment', [DebtController::class, 'recordPayment'])->name('debts.record-payment');
    
    // Payment Plan routes
    Route::resource('payment-plans', PaymentPlanController::class);
    Route::post('payment-plans/compare-strategies', [PaymentPlanController::class, 'compareStrategies'])->name('payment-plans.compare-strategies');
    Route::patch('payment-plans/{paymentPlan}/toggle-status', [PaymentPlanController::class, 'toggleStatus'])->name('payment-plans.toggle-status');
    Route::patch('payment-plans/{paymentPlan}/update-priorities', [PaymentPlanController::class, 'updatePriorities'])->name('payment-plans.update-priorities');
    Route::post('payment-plans/{paymentPlan}/add-debt', [PaymentPlanController::class, 'addDebt'])->name('payment-plans.add-debt');
    Route::delete('payment-plans/{paymentPlan}/debts/{debt}', [PaymentPlanController::class, 'removeDebt'])->name('payment-plans.remove-debt');
    
    // Payment Schedule routes
    Route::get('payment-plans/{paymentPlan}/schedules', [PaymentScheduleController::class, 'index'])->name('payment-schedules.index');
    Route::get('payment-schedules/upcoming', [PaymentScheduleController::class, 'upcoming'])->name('payment-schedules.upcoming');
    Route::get('payment-schedules/overdue', [PaymentScheduleController::class, 'overdue'])->name('payment-schedules.overdue');
    Route::post('payment-schedules/{schedule}/record-payment', [PaymentScheduleController::class, 'recordPayment'])->name('payment-schedules.record-payment');
    Route::post('payment-schedules/{schedule}/skip', [PaymentScheduleController::class, 'skipPayment'])->name('payment-schedules.skip');
    Route::patch('payment-schedules/{schedule}/reschedule', [PaymentScheduleController::class, 'reschedule'])->name('payment-schedules.reschedule');
    Route::post('payment-schedules/bulk-mark-paid', [PaymentScheduleController::class, 'bulkMarkAsPaid'])->name('payment-schedules.bulk-mark-paid');
    Route::get('payment-plans/{paymentPlan}/schedules/report', [PaymentScheduleController::class, 'report'])->name('payment-schedules.report');
    
    // Bank Integration Callback routes (outside auth middleware for callbacks)
});

// Public routes for bank integration callbacks
Route::prefix('bank')->name('bank.')->group(function () {
    Route::get('/integration/callback/{bank}', [BankIntegrationController::class, 'callback'])->name('integration.callback');
    Route::get('/integration/simulate', [BankIntegrationController::class, 'simulate'])->name('integration.simulate');
    Route::post('/integration/simulate/authorize', [BankIntegrationController::class, 'simulateAuthorize'])->name('integration.simulate.authorize');
});

require __DIR__.'/auth.php';
