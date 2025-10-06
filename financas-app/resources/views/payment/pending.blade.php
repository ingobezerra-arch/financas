<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagamento Pendente - {{ config('app.name', 'Laravel') }}</title>
    
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
            display: flex;
            align-items: center;
        }
        
        .pending-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            padding: 60px 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .pending-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #f59e0b, #d97706);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pendingPulse 2s ease-in-out infinite;
        }
        
        .pending-icon i {
            font-size: 3rem;
            color: white;
        }
        
        @keyframes pendingPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pending-title {
            color: #1a202c;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .pending-message {
            color: #6b7280;
            font-size: 1.2rem;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .order-info {
            background: #fffbeb;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            border-left: 5px solid #f59e0b;
        }
        
        .order-info h5 {
            color: #1a202c;
            margin-bottom: 15px;
        }
        
        .order-detail {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #fde68a;
        }
        
        .order-detail:last-child {
            border-bottom: none;
        }
        
        .btn-dashboard {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
        
        .btn-dashboard:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-home {
            background: transparent;
            border: 2px solid #667eea;
            padding: 13px 40px;
            border-radius: 50px;
            color: #667eea;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
        
        .btn-home:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .timeline-item {
            display: flex;
            align-items: center;
            margin: 15px 0;
            position: relative;
        }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            background: #f59e0b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 20px;
            z-index: 1;
        }
        
        .timeline-icon.completed {
            background: #10b981;
        }
        
        .timeline-content {
            flex: 1;
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 10px;
        }
        
        .timeline-content h6 {
            margin-bottom: 5px;
            color: #1a202c;
        }
        
        .timeline-content small {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="pending-container">
            <div class="pending-icon">
                <i class="fas fa-clock"></i>
            </div>
            
            <h1 class="pending-title">Pagamento Pendente</h1>
            
            <p class="pending-message">
                Seu pedido foi registrado com sucesso! <br>
                Estamos aguardando a confirmação do pagamento.
            </p>
            
            <div class="order-info">
                <h5><i class="fas fa-receipt me-2"></i>Detalhes do Pedido</h5>
                <div class="order-detail">
                    <span><strong>Número do Pedido:</strong></span>
                    <span class="text-muted">{{ $orderId ?? 'N/A' }}</span>
                </div>
                <div class="order-detail">
                    <span><strong>Status:</strong></span>
                    <span class="text-warning"><i class="fas fa-clock me-1"></i>Aguardando Pagamento</span>
                </div>
                <div class="order-detail">
                    <span><strong>Data do Pedido:</strong></span>
                    <span class="text-muted">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
                <div class="order-detail">
                    <span><strong>Método de Pagamento:</strong></span>
                    <span class="text-muted">PIX / Boleto Bancário</span>
                </div>
            </div>
            
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Atenção:</strong> Para PIX, o pagamento pode ser confirmado em até 30 minutos. 
                Para boleto, a confirmação pode levar até 2 dias úteis.
            </div>
            
            <!-- Timeline de Status -->
            <div class="mt-4">
                <h5 class="text-start mb-3">Status do Pagamento</h5>
                <div class="timeline text-start">
                    <div class="timeline-item">
                        <div class="timeline-icon completed">
                            <i class="fas fa-check fa-sm"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Pedido Criado</h6>
                            <small>{{ now()->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-credit-card fa-sm"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Aguardando Pagamento</h6>
                            <small>Processando...</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-icon" style="background: #d1d5db;">
                            <i class="fas fa-user-check fa-sm"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Ativação da Conta</h6>
                            <small>Aguardando confirmação do pagamento</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                Um email com as instruções de pagamento foi enviado para seu endereço. 
                Você receberá outra confirmação assim que o pagamento for aprovado.
            </div>
            
            <div class="d-flex flex-wrap justify-content-center">
                <a href="{{ route('welcome') }}" class="btn-home">
                    <i class="fas fa-home me-2"></i>Página Inicial
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Ir para Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-dashboard">
                        <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                    </a>
                @endauth
            </div>
            
            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-headset me-2"></i>
                    Dúvidas sobre seu pagamento? Entre em contato: 
                    <a href="mailto:suporte@financasapp.com" class="text-decoration-none">suporte@financasapp.com</a>
                </small>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-refresh para verificar status do pagamento a cada 30 segundos
        let checkCount = 0;
        const maxChecks = 20; // Máximo 20 verificações (10 minutos)
        
        function checkPaymentStatus() {
            if (checkCount >= maxChecks) {
                return; // Para de verificar após 10 minutos
            }
            
            checkCount++;
            
            // Simular verificação de status (em produção, seria uma chamada AJAX real)
            fetch('/api/check-payment-status?order={{ $orderId ?? "" }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'approved') {
                        window.location.href = '/payment/success?order={{ $orderId ?? "" }}';
                    }
                })
                .catch(error => {
                    console.log('Erro ao verificar status:', error);
                });
        }
        
        // Verificar status a cada 30 segundos
        setInterval(checkPaymentStatus, 30000);
        
        // Primeira verificação após 30 segundos
        setTimeout(checkPaymentStatus, 30000);
    </script>
</body>
</html>