<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagamento Realizado - {{ config('app.name', 'Laravel') }}</title>
    
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
        
        .success-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            padding: 60px 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: successPulse 2s ease-in-out infinite;
        }
        
        .success-icon i {
            font-size: 3rem;
            color: white;
        }
        
        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .success-title {
            color: #1a202c;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .success-message {
            color: #6b7280;
            font-size: 1.2rem;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .order-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            border-left: 5px solid #10b981;
        }
        
        .order-info h5 {
            color: #1a202c;
            margin-bottom: 15px;
        }
        
        .order-detail {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
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
        
        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
        }
        
        .confetti-piece {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #667eea;
            animation: confetti-fall 3s linear infinite;
        }
        
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="confetti" id="confetti"></div>
    
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            
            <h1 class="success-title">Pagamento Aprovado!</h1>
            
            <p class="success-message">
                Parabéns! Seu pagamento foi processado com sucesso. <br>
                Sua assinatura já está ativa e você pode começar a usar todas as funcionalidades do seu plano.
            </p>
            
            <div class="order-info">
                <h5><i class="fas fa-receipt me-2"></i>Detalhes do Pedido</h5>
                <div class="order-detail">
                    <span><strong>Número do Pedido:</strong></span>
                    <span class="text-muted">{{ $orderId ?? 'N/A' }}</span>
                </div>
                <div class="order-detail">
                    <span><strong>Status:</strong></span>
                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Aprovado</span>
                </div>
                <div class="order-detail">
                    <span><strong>Data do Pagamento:</strong></span>
                    <span class="text-muted">{{ now()->format('d/m/Y H:i') }}</span>
                </div>
                <div class="order-detail">
                    <span><strong>Método de Pagamento:</strong></span>
                    <span class="text-muted">Cartão de Crédito</span>
                </div>
            </div>
            
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                Um email de confirmação foi enviado para seu endereço. 
                Guarde este número de pedido para futuras consultas.
            </div>
            
            <div class="d-flex flex-wrap justify-content-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Ir para Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-dashboard">
                        <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                    </a>
                @endauth
                
                <a href="{{ route('welcome') }}" class="btn-home">
                    <i class="fas fa-home me-2"></i>Página Inicial
                </a>
            </div>
            
            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-headset me-2"></i>
                    Precisa de ajuda? Entre em contato com nosso suporte: 
                    <a href="mailto:suporte@financasapp.com" class="text-decoration-none">suporte@financasapp.com</a>
                </small>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Criar confetti animado
        function createConfetti() {
            const confettiContainer = document.getElementById('confetti');
            const colors = ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
            
            for (let i = 0; i < 50; i++) {
                const confettiPiece = document.createElement('div');
                confettiPiece.className = 'confetti-piece';
                confettiPiece.style.left = Math.random() * 100 + '%';
                confettiPiece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confettiPiece.style.animationDelay = Math.random() * 3 + 's';
                confettiPiece.style.animationDuration = (Math.random() * 3 + 2) + 's';
                confettiContainer.appendChild(confettiPiece);
            }
            
            // Remover confetti após 5 segundos
            setTimeout(() => {
                confettiContainer.innerHTML = '';
            }, 5000);
        }
        
        // Iniciar confetti quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            createConfetti();
        });
    </script>
</body>
</html>