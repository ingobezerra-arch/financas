<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        
        .checkout-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 50px 0;
        }
        
        .plan-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
        }
        
        .plan-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .plan-price {
            font-size: 3rem;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 30px 0 0 0;
        }
        
        .plan-features li {
            padding: 8px 0;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .plan-features li i {
            margin-right: 10px;
            color: #fff;
        }
        
        .payment-form {
            padding: 40px;
        }
        
        .form-label {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-checkout {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-checkout:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .security-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .payment-methods {
            display: flex;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .payment-method {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            flex: 1;
            min-width: 120px;
        }
        
        .payment-method.active {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        
        .payment-method:hover {
            border-color: #667eea;
        }
        
        .payment-method i {
            font-size: 1.5rem;
            margin-bottom: 8px;
            color: #667eea;
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .order-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1rem;
            padding-top: 15px;
            margin-top: 10px;
            border-top: 2px solid #e2e8f0;
        }
        
        .back-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-block;
        }
        
        @media (max-width: 768px) {
            .checkout-container {
                margin: 20px 0;
            }
            
            .plan-summary, .payment-form {
                padding: 30px 20px;
            }
            
            .plan-price {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-4">
                <a href="{{ url('/') }}" class="back-link">
                    <i class="fas fa-arrow-left me-2"></i>Voltar para página inicial
                </a>
            </div>
            
            <div class="col-lg-10">
                <div class="checkout-container">
                    <div class="row g-0">
                        <!-- Plan Summary -->
                        <div class="col-lg-5">
                            <div class="plan-summary h-100">
                                <div class="plan-badge" id="planBadge">Plano Selecionado</div>
                                <h2 id="planName">Carregando...</h2>
                                <div class="plan-price" id="planPrice">R$ 0</div>
                                <p id="planDescription">Descrição do plano</p>
                                
                                <ul class="plan-features" id="planFeatures">
                                    <!-- Features serão carregadas dinamicamente -->
                                </ul>
                                
                                <div class="mt-4">
                                    <small style="color: rgba(255, 255, 255, 0.8);">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        Pagamento 100% seguro com criptografia SSL
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Form -->
                        <div class="col-lg-7">
                            <div class="payment-form">
                                <h3 class="mb-4">Finalizar Pagamento</h3>
                                
                                <!-- Método de Pagamento -->
                                <div class="mb-4">
                                    <label class="form-label">Método de Pagamento</label>
                                    <div class="payment-methods">
                                        <div class="payment-method active" data-method="credit_card">
                                            <i class="fas fa-credit-card"></i>
                                            <div>Cartão de Crédito</div>
                                        </div>
                                        <div class="payment-method" data-method="pix">
                                            <i class="fas fa-qrcode"></i>
                                            <div>PIX</div>
                                        </div>
                                        <div class="payment-method" data-method="boleto">
                                            <i class="fas fa-barcode"></i>
                                            <div>Boleto</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <form id="paymentForm">
                                    @csrf
                                    <input type="hidden" name="plan" id="selectedPlan" value="">
                                    <input type="hidden" name="payment_method" id="paymentMethod" value="credit_card">
                                    
                                    <!-- Dados do Cliente -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="first_name" class="form-label">Nome</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="last_name" class="form-label">Sobrenome</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Telefone</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="document" class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="document" name="document" placeholder="000.000.000-00" required>
                                    </div>
                                    
                                    <!-- Dados do Cartão (visível apenas para cartão de crédito) -->
                                    <div id="cardFields">
                                        <div class="mb-3">
                                            <label for="card_number" class="form-label">Número do Cartão</label>
                                            <input type="text" class="form-control" id="card_number" name="card_number" placeholder="0000 0000 0000 0000">
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="card_expiry_month" class="form-label">Mês</label>
                                                <select class="form-control" id="card_expiry_month" name="card_expiry_month">
                                                    <option value="">Mês</option>
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="card_expiry_year" class="form-label">Ano</label>
                                                <select class="form-control" id="card_expiry_year" name="card_expiry_year">
                                                    <option value="">Ano</option>
                                                    @for($i = date('Y'); $i <= date('Y') + 15; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="card_cvv" class="form-label">CVV</label>
                                                <input type="text" class="form-control" id="card_cvv" name="card_cvv" placeholder="123" maxlength="4">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="card_holder_name" class="form-label">Nome no Cartão</label>
                                            <input type="text" class="form-control" id="card_holder_name" name="card_holder_name" placeholder="Nome como aparece no cartão">
                                        </div>
                                    </div>
                                    
                                    <!-- Resumo do Pedido -->
                                    <div class="order-summary">
                                        <h5 class="mb-3">Resumo do Pedido</h5>
                                        <div class="order-row">
                                            <span id="orderPlanName">Plano</span>
                                            <span id="orderPlanPrice">R$ 0,00</span>
                                        </div>
                                        <div class="order-row" id="discountRow" style="display: none;">
                                            <span>Desconto</span>
                                            <span id="discountAmount" class="text-success">-R$ 0,00</span>
                                        </div>
                                        <div class="order-row">
                                            <span>Total</span>
                                            <span id="totalAmount">R$ 0,00</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Informações de Segurança -->
                                    <div class="security-info">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-shield-alt text-success me-3 fa-2x"></i>
                                            <div>
                                                <h6 class="mb-1">Transação 100% Segura</h6>
                                                <small class="text-muted">
                                                    Seus dados são protegidos por criptografia SSL e processados pelo PagSeguro
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-checkout" id="submitBtn">
                                        <span class="loading" id="loadingSpinner">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                        </span>
                                        <span id="submitText">Finalizar Pagamento</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- PagSeguro Checkout Transparente -->
    <script src="https://assets.pagseguro.com.br/checkout-sdk-js/rc/dist/browser/pagseguro.min.js"></script>
    
    <script>
        // Configuração dos planos
        const plans = {
            'basico_mensal': {
                name: 'Básico Mensal',
                price: 19.00,
                period: '/mês',
                description: 'Perfeito para iniciantes',
                badge: 'Plano Básico',
                features: [
                    'Até 3 contas bancárias',
                    'Controle de transações',
                    'Orçamentos básicos',
                    'Relatórios mensais',
                    'Suporte por email'
                ]
            },
            'basico_anual': {
                name: 'Básico Anual',
                price: 15.00,
                originalPrice: 19.00,
                period: '/mês',
                description: 'Economize 21% no plano anual',
                badge: '2 meses grátis',
                features: [
                    'Até 3 contas bancárias',
                    'Controle de transações',
                    'Orçamentos básicos',
                    'Relatórios mensais',
                    'Suporte por email'
                ]
            },
            'plus_mensal': {
                name: 'Plus Mensal',
                price: 39.00,
                period: '/mês',
                description: 'Para usuários avançados',
                badge: 'Mais Popular',
                features: [
                    'Contas ilimitadas',
                    'Todas as funcionalidades',
                    'Orçamentos avançados',
                    'Metas financeiras',
                    'Sincronização bancária',
                    'Relatórios premium',
                    'Suporte prioritário'
                ]
            },
            'plus_anual': {
                name: 'Plus Anual',
                price: 29.00,
                originalPrice: 39.00,
                period: '/mês',
                description: 'Economize 26% no plano anual',
                badge: 'Melhor custo-benefício',
                features: [
                    'Contas ilimitadas',
                    'Todas as funcionalidades',
                    'Orçamentos avançados',
                    'Metas financeiras',
                    'Sincronização bancária',
                    'Relatórios premium',
                    'Suporte prioritário'
                ]
            }
        };
        
        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedPlan = urlParams.get('plan') || 'basico_mensal';
            
            loadPlan(selectedPlan);
            setupEventListeners();
            setupMasks();
        });
        
        function loadPlan(planKey) {
            const plan = plans[planKey];
            if (!plan) return;
            
            document.getElementById('selectedPlan').value = planKey;
            document.getElementById('planBadge').textContent = plan.badge;
            document.getElementById('planName').textContent = plan.name;
            document.getElementById('planPrice').innerHTML = `R$ ${plan.price.toFixed(0)}<span style="font-size: 1rem;">${plan.period}</span>`;
            document.getElementById('planDescription').textContent = plan.description;
            
            // Features
            const featuresHtml = plan.features.map(feature => 
                `<li><i class="fas fa-check"></i> ${feature}</li>`
            ).join('');
            document.getElementById('planFeatures').innerHTML = featuresHtml;
            
            // Order summary
            document.getElementById('orderPlanName').textContent = plan.name;
            document.getElementById('orderPlanPrice').textContent = `R$ ${plan.price.toFixed(2).replace('.', ',')}`;
            
            // Desconto para planos anuais
            if (plan.originalPrice) {
                const discount = plan.originalPrice - plan.price;
                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('discountAmount').textContent = `-R$ ${discount.toFixed(2).replace('.', ',')}`;
            }
            
            document.getElementById('totalAmount').textContent = `R$ ${plan.price.toFixed(2).replace('.', ',')}`;
        }
        
        function setupEventListeners() {
            // Payment method selection
            document.querySelectorAll('.payment-method').forEach(method => {
                method.addEventListener('click', function() {
                    document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
                    this.classList.add('active');
                    
                    const paymentMethod = this.dataset.method;
                    document.getElementById('paymentMethod').value = paymentMethod;
                    
                    // Show/hide card fields
                    const cardFields = document.getElementById('cardFields');
                    if (paymentMethod === 'credit_card') {
                        cardFields.style.display = 'block';
                        // Make card fields required
                        cardFields.querySelectorAll('input, select').forEach(field => {
                            field.required = true;
                        });
                    } else {
                        cardFields.style.display = 'none';
                        // Remove required from card fields
                        cardFields.querySelectorAll('input, select').forEach(field => {
                            field.required = false;
                        });
                    }
                });
            });
            
            // Form submission
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                processPayment();
            });
        }
        
        function setupMasks() {
            // CPF mask
            document.getElementById('document').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = value;
            });
            
            // Phone mask
            document.getElementById('phone').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            });
            
            // Card number mask
            document.getElementById('card_number').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{4})(\d)/, '$1 $2');
                value = value.replace(/(\d{4})(\d)/, '$1 $2');
                value = value.replace(/(\d{4})(\d)/, '$1 $2');
                e.target.value = value;
            });
        }
        
        async function processPayment() {
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const submitText = document.getElementById('submitText');
            
            // Show loading
            submitBtn.disabled = true;
            loadingSpinner.classList.add('show');
            submitText.textContent = 'Processando...';
            
            try {
                const formData = new FormData(document.getElementById('paymentForm'));
                const paymentData = Object.fromEntries(formData.entries());
                
                const response = await fetch('/api/process-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(paymentData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (result.payment_method === 'credit_card') {
                        // Redirect to success page
                        window.location.href = '/payment/success?order=' + result.order_id;
                    } else if (result.payment_method === 'pix') {
                        // Show PIX QR Code
                        showPixPayment(result);
                    } else if (result.payment_method === 'boleto') {
                        // Redirect to boleto
                        window.open(result.boleto_url, '_blank');
                        window.location.href = '/payment/pending?order=' + result.order_id;
                    }
                } else {
                    throw new Error(result.message || 'Erro no processamento do pagamento');
                }
                
            } catch (error) {
                alert('Erro no pagamento: ' + error.message);
                console.error('Payment error:', error);
            } finally {
                // Hide loading
                submitBtn.disabled = false;
                loadingSpinner.classList.remove('show');
                submitText.textContent = 'Finalizar Pagamento';
            }
        }
        
        function showPixPayment(paymentData) {
            // Implement PIX payment display
            alert('PIX gerado com sucesso! Código PIX: ' + paymentData.pix_code);
        }
    </script>
</body>
</html>