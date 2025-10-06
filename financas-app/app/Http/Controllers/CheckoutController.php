<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    private $pagseguroEmail;
    private $pagseguroToken;
    private $pagseguroSandbox;

    public function __construct()
    {
        $this->pagseguroEmail = config('services.pagseguro.email');
        $this->pagseguroToken = config('services.pagseguro.token');
        $this->pagseguroSandbox = config('services.pagseguro.sandbox', true);
    }

    /**
     * Exibe a página de checkout
     */
    public function index(Request $request)
    {
        $plan = $request->query('plan', 'basico_mensal');
        
        // Validar se o plano existe
        $validPlans = ['basico_mensal', 'basico_anual', 'plus_mensal', 'plus_anual'];
        if (!in_array($plan, $validPlans)) {
            return redirect()->route('welcome')->with('error', 'Plano inválido selecionado.');
        }

        return view('checkout', compact('plan'));
    }

    /**
     * Processa o pagamento via API
     */
    public function processPayment(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'plan' => 'required|in:basico_mensal,basico_anual,plus_mensal,plus_anual',
                'payment_method' => 'required|in:credit_card,pix,boleto',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'document' => 'required|string|max:14',
                // Campos do cartão (opcionais dependendo do método)
                'card_number' => 'nullable|string',
                'card_expiry_month' => 'nullable|string',
                'card_expiry_year' => 'nullable|string',
                'card_cvv' => 'nullable|string',
                'card_holder_name' => 'nullable|string',
            ]);

            // Obter informações do plano
            $planInfo = $this->getPlanInfo($validatedData['plan']);
            
            // Processar de acordo com o método de pagamento
            switch ($validatedData['payment_method']) {
                case 'credit_card':
                    return $this->processCreditCardPayment($validatedData, $planInfo);
                case 'pix':
                    return $this->processPixPayment($validatedData, $planInfo);
                case 'boleto':
                    return $this->processBoletoPayment($validatedData, $planInfo);
                default:
                    throw new \Exception('Método de pagamento inválido');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos fornecidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro no processamento do pagamento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno no processamento do pagamento'
            ], 500);
        }
    }

    /**
     * Processa pagamento com cartão de crédito
     */
    private function processCreditCardPayment($data, $planInfo)
    {
        // Limpar e formatar dados do cartão
        $cardNumber = preg_replace('/\D/', '', $data['card_number']);
        $document = preg_replace('/\D/', '', $data['document']);
        $phone = preg_replace('/\D/', '', $data['phone']);

        // Dados para o PagSeguro
        $paymentData = [
            'email' => $this->pagseguroEmail,
            'token' => $this->pagseguroToken,
            'currency' => 'BRL',
            'itemId1' => $data['plan'],
            'itemDescription1' => $planInfo['name'],
            'itemAmount1' => number_format($planInfo['price'], 2, '.', ''),
            'itemQuantity1' => 1,
            'senderName' => $data['first_name'] . ' ' . $data['last_name'],
            'senderEmail' => $data['email'],
            'senderPhone' => substr($phone, 0, 2),
            'senderAreaCode' => substr($phone, 2),
            'senderCPF' => $document,
            'creditCardToken' => $this->generateCardToken($data),
            'paymentMode' => 'default',
            'paymentMethod' => 'creditCard',
            'creditCardHolderName' => $data['card_holder_name'],
            'creditCardHolderCPF' => $document,
            'creditCardHolderBirthDate' => '01/01/1990', // Seria ideal coletar do usuário
            'creditCardHolderPhone' => substr($phone, 0, 2),
            'creditCardHolderAreaCode' => substr($phone, 2),
            'installmentQuantity' => 1,
            'installmentValue' => number_format($planInfo['price'], 2, '.', ''),
            'noInterestInstallmentQuantity' => 12,
            'reference' => 'PLAN_' . strtoupper($data['plan']) . '_' . time(),
        ];

        $url = $this->pagseguroSandbox 
            ? 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions'
            : 'https://ws.pagseguro.uol.com.br/v2/transactions';

        $response = Http::asForm()->post($url, $paymentData);

        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());
            
            if (isset($xml->code)) {
                return response()->json([
                    'success' => true,
                    'payment_method' => 'credit_card',
                    'transaction_id' => (string)$xml->code,
                    'status' => (string)$xml->status,
                    'order_id' => $paymentData['reference'],
                    'message' => 'Pagamento processado com sucesso'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro no processamento do cartão de crédito'
                ], 400);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Falha na comunicação com o gateway de pagamento'
        ], 500);
    }

    /**
     * Processa pagamento via PIX
     */
    private function processPixPayment($data, $planInfo)
    {
        $document = preg_replace('/\D/', '', $data['document']);
        $phone = preg_replace('/\D/', '', $data['phone']);

        // Dados para PIX via PagSeguro
        $pixData = [
            'email' => $this->pagseguroEmail,
            'token' => $this->pagseguroToken,
            'currency' => 'BRL',
            'itemId1' => $data['plan'],
            'itemDescription1' => $planInfo['name'],
            'itemAmount1' => number_format($planInfo['price'], 2, '.', ''),
            'itemQuantity1' => 1,
            'senderName' => $data['first_name'] . ' ' . $data['last_name'],
            'senderEmail' => $data['email'],
            'senderPhone' => substr($phone, 0, 2),
            'senderAreaCode' => substr($phone, 2),
            'senderCPF' => $document,
            'paymentMode' => 'default',
            'paymentMethod' => 'pix',
            'reference' => 'PIX_' . strtoupper($data['plan']) . '_' . time(),
        ];

        // Para sandbox, simular resposta PIX
        if ($this->pagseguroSandbox) {
            return response()->json([
                'success' => true,
                'payment_method' => 'pix',
                'pix_code' => 'PIX_SIMULADO_' . rand(100000, 999999),
                'qr_code_image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
                'order_id' => $pixData['reference'],
                'expires_at' => now()->addMinutes(30)->toISOString(),
                'message' => 'PIX gerado com sucesso'
            ]);
        }

        // Implementar integração real com PagSeguro PIX
        return response()->json([
            'success' => false,
            'message' => 'PIX em desenvolvimento'
        ], 501);
    }

    /**
     * Processa pagamento via Boleto
     */
    private function processBoletoPayment($data, $planInfo)
    {
        $document = preg_replace('/\D/', '', $data['document']);
        $phone = preg_replace('/\D/', '', $data['phone']);

        // Dados para Boleto via PagSeguro
        $boletoData = [
            'email' => $this->pagseguroEmail,
            'token' => $this->pagseguroToken,
            'currency' => 'BRL',
            'itemId1' => $data['plan'],
            'itemDescription1' => $planInfo['name'],
            'itemAmount1' => number_format($planInfo['price'], 2, '.', ''),
            'itemQuantity1' => 1,
            'senderName' => $data['first_name'] . ' ' . $data['last_name'],
            'senderEmail' => $data['email'],
            'senderPhone' => substr($phone, 0, 2),
            'senderAreaCode' => substr($phone, 2),
            'senderCPF' => $document,
            'paymentMode' => 'default',
            'paymentMethod' => 'boleto',
            'reference' => 'BOLETO_' . strtoupper($data['plan']) . '_' . time(),
        ];

        // Para sandbox, simular resposta Boleto
        if ($this->pagseguroSandbox) {
            return response()->json([
                'success' => true,
                'payment_method' => 'boleto',
                'boleto_url' => 'https://sandbox.pagseguro.uol.com.br/checkout/payment/boleto.html?c=' . rand(100000, 999999),
                'order_id' => $boletoData['reference'],
                'expires_at' => now()->addDays(3)->toISOString(),
                'message' => 'Boleto gerado com sucesso'
            ]);
        }

        // Implementar integração real com PagSeguro Boleto
        return response()->json([
            'success' => false,
            'message' => 'Boleto em desenvolvimento'
        ], 501);
    }

    /**
     * Gera token do cartão para o PagSeguro
     */
    private function generateCardToken($data)
    {
        // Em produção, isso seria feito via JavaScript no frontend
        // usando a biblioteca do PagSeguro para tokenização segura
        return 'fake_token_' . time();
    }

    /**
     * Obtém informações do plano
     */
    private function getPlanInfo($planKey)
    {
        $plans = [
            'basico_mensal' => [
                'name' => 'Básico Mensal',
                'price' => 19.00,
                'period' => 'monthly',
                'features' => ['Até 3 contas', 'Controle básico', 'Suporte email']
            ],
            'basico_anual' => [
                'name' => 'Básico Anual',
                'price' => 180.00, // 15 * 12
                'period' => 'yearly',
                'features' => ['Até 3 contas', 'Controle básico', 'Suporte email']
            ],
            'plus_mensal' => [
                'name' => 'Plus Mensal',
                'price' => 39.00,
                'period' => 'monthly',
                'features' => ['Contas ilimitadas', 'Recursos avançados', 'Suporte prioritário']
            ],
            'plus_anual' => [
                'name' => 'Plus Anual',
                'price' => 348.00, // 29 * 12
                'period' => 'yearly',
                'features' => ['Contas ilimitadas', 'Recursos avançados', 'Suporte prioritário']
            ]
        ];

        return $plans[$planKey] ?? $plans['basico_mensal'];
    }

    /**
     * Página de sucesso do pagamento
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order');
        return view('payment.success', compact('orderId'));
    }

    /**
     * Página de pagamento pendente
     */
    public function pending(Request $request)
    {
        $orderId = $request->query('order');
        return view('payment.pending', compact('orderId'));
    }
}