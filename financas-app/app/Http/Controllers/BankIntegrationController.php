<?php

namespace App\Http\Controllers;

use App\Models\BankIntegration;
use App\Models\SyncedTransaction;
use App\Models\Transaction;
use App\Services\OpenFinanceService;
use App\Services\RealOpenFinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankIntegrationController extends Controller
{
    protected $openFinanceService;

    public function __construct()
    {
        // Escolhe o serviço baseado na configuração
        $useRealApis = config('open_finance.production.use_real_apis', false);
        
        if ($useRealApis) {
            $this->openFinanceService = new RealOpenFinanceService();
        } else {
            $this->openFinanceService = new OpenFinanceService();
        }
    }

    /**
     * Lista integrações do usuário
     */
    public function index()
    {
        $integrations = Auth::user()->bankIntegrations()
            ->with('syncedTransactions')
            ->orderBy('created_at', 'desc')
            ->get();

        $supportedBanks = $this->openFinanceService->getSupportedBanks();

        return view('bank-integrations.index', compact('integrations', 'supportedBanks'));
    }

    /**
     * Página para nova integração
     */
    public function create()
    {
        $supportedBanks = $this->openFinanceService->getSupportedBanks();
        
        return view('bank-integrations.create', compact('supportedBanks'));
    }

    /**
     * Inicia processo de integração
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_code' => 'required|string|in:' . implode(',', array_keys($this->openFinanceService->getSupportedBanks())),
            'permissions' => 'array'
        ]);

        $result = $this->openFinanceService->initiateConsent(
            Auth::id(),
            $request->bank_code,
            $request->permissions ?? []
        );

        if ($result['success']) {
            return redirect($result['consent_url'])
                ->with('success', 'Redirecionando para autorização bancária...');
        }

        return back()
            ->withErrors(['error' => $result['error']])
            ->withInput();
    }

    /**
     * Callback após autorização
     */
    public function callback(Request $request, string $bank)
    {
        $request->validate([
            'consent_id' => 'required|string',
            'code' => 'required|string'
        ]);

        $result = $this->openFinanceService->processConsentCallback(
            $request->consent_id,
            $request->code
        );

        if ($result['success']) {
            $message = 'Integração bancária configurada com sucesso!';
            if (isset($result['sync_result']) && $result['sync_result']['success']) {
                $newTransactions = $result['sync_result']['new_transactions'] ?? 0;
                $message .= " {$newTransactions} transações foram importadas.";
            }
            
            return redirect()->route('bank-integrations.index')
                ->with('success', $message);
        }

        return redirect()->route('bank-integrations.index')
            ->withErrors(['error' => $result['error']]);
    }

    /**
     * Simulador de autorização (para demonstração)
     */
    public function simulate(Request $request)
    {
        $consentId = $request->get('consent_id');
        $bankCode = $request->get('bank_code');
        
        $integration = BankIntegration::where('consent_id', $consentId)->first();
        
        if (!$integration) {
            return redirect()->route('bank-integrations.index')
                ->withErrors(['error' => 'Consentimento não encontrado']);
        }

        $supportedBanks = $this->openFinanceService->getSupportedBanks();
        $bankName = $supportedBanks[$bankCode] ?? 'Banco desconhecido';

        return view('bank-integrations.simulate', compact('consentId', 'bankCode', 'bankName'));
    }

    /**
     * Processa autorização simulada
     */
    public function simulateAuthorize(Request $request)
    {
        $request->validate([
            'consent_id' => 'required|string',
            'action' => 'required|in:authorize,deny'
        ]);

        if ($request->action === 'authorize') {
            // Simula código de autorização
            $authCode = 'auth_' . uniqid();
            
            return $this->callback(new Request([
                'consent_id' => $request->consent_id,
                'code' => $authCode
            ]), 'simulated');
        }

        // Negou autorização
        $integration = BankIntegration::where('consent_id', $request->consent_id)->first();
        if ($integration) {
            $integration->update([
                'status' => 'revoked',
                'is_active' => false
            ]);
        }

        return redirect()->route('bank-integrations.index')
            ->with('info', 'Autorização bancária foi negada.');
    }

    /**
     * Detalhes da integração
     */
    public function show(BankIntegration $bankIntegration)
    {
        $this->authorize('view', $bankIntegration);

        $syncedTransactions = $bankIntegration->syncedTransactions()
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        $stats = [
            'total_transactions' => $bankIntegration->syncedTransactions()->count(),
            'processed_transactions' => $bankIntegration->syncedTransactions()->processed()->count(),
            'unprocessed_transactions' => $bankIntegration->syncedTransactions()->unprocessed()->count(),
            'last_sync' => $bankIntegration->last_sync_at,
            'total_amount' => $bankIntegration->syncedTransactions()->sum('amount')
        ];

        return view('bank-integrations.show', compact('bankIntegration', 'syncedTransactions', 'stats'));
    }

    /**
     * Sincronização manual
     */
    public function sync(BankIntegration $bankIntegration)
    {
        $this->authorize('update', $bankIntegration);

        $result = $this->openFinanceService->syncTransactions($bankIntegration);

        if ($result['success']) {
            return back()->with('success', 
                "Sincronização concluída! {$result['new_transactions']} novas transações importadas."
            );
        }

        return back()->withErrors(['error' => $result['error']]);
    }

    /**
     * Configurações da integração
     */
    public function edit(BankIntegration $bankIntegration)
    {
        $this->authorize('update', $bankIntegration);

        return view('bank-integrations.edit', compact('bankIntegration'));
    }

    /**
     * Atualiza configurações
     */
    public function update(Request $request, BankIntegration $bankIntegration)
    {
        $this->authorize('update', $bankIntegration);

        $request->validate([
            'auto_sync' => 'boolean',
            'sync_frequency' => 'in:daily,weekly,manual',
            'is_active' => 'boolean'
        ]);

        $bankIntegration->update($request->only([
            'auto_sync',
            'sync_frequency',
            'is_active'
        ]));

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Remove integração
     */
    public function destroy(BankIntegration $bankIntegration)
    {
        $this->authorize('delete', $bankIntegration);

        DB::transaction(function () use ($bankIntegration) {
            // Revoga consentimento
            $this->openFinanceService->revokeConsent($bankIntegration);
            
            // Remove transações sincronizadas
            $bankIntegration->syncedTransactions()->delete();
            
            // Remove integração
            $bankIntegration->delete();
        });

        return redirect()->route('bank-integrations.index')
            ->with('success', 'Integração bancária removida com sucesso!');
    }

    /**
     * Lista transações sincronizadas
     */
    public function transactions(Request $request)
    {
        $query = SyncedTransaction::where('user_id', Auth::id())
            ->with(['bankIntegration', 'category', 'transaction']);

        // Filtros
        if ($request->filled('bank_integration_id')) {
            $query->where('bank_integration_id', $request->bank_integration_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_processed')) {
            $processed = $request->boolean('is_processed');
            if ($processed) {
                $query->processed();
            } else {
                $query->unprocessed();
            }
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        $integrations = Auth::user()->bankIntegrations()
            ->active()
            ->pluck('bank_name', 'id');

        return view('bank-integrations.transactions', compact('transactions', 'integrations'));
    }

    /**
     * Processa transação sincronizada
     */
    public function processTransaction(Request $request, SyncedTransaction $syncedTransaction)
    {
        $this->authorize('update', $syncedTransaction);

        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'action' => 'required|in:create,ignore'
        ]);

        if ($request->action === 'ignore') {
            $syncedTransaction->markAsProcessed(null, 'Ignorada pelo usuário');
            return back()->with('success', 'Transação marcada como ignorada.');
        }

        // Cria transação no sistema
        DB::transaction(function () use ($syncedTransaction, $request) {
            $transactionData = $syncedTransaction->toTransaction();
            
            if ($request->filled('category_id')) {
                $transactionData['category_id'] = $request->category_id;
            }

            $transaction = Transaction::create($transactionData);
            $syncedTransaction->markAsProcessed($transaction, 'Processada automaticamente');
        });

        return back()->with('success', 'Transação adicionada ao sistema com sucesso!');
    }
}