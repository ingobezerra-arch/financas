<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Gerenciamento de Finanças Pessoais</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .btn-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            color: white;
        }
        .demo-credentials {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
        }
        .nav-link:hover {
            color: white !important;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: transparent;">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-chart-line me-2"></i>{{ config('app.name', 'FinancasApp') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Registrar</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="fas fa-chart-line me-3"></i>Controle Total das Suas Finanças
                    </h1>
                    <p class="lead mb-5">
                        Gerencie suas receitas, despesas, orçamentos e metas financeiras de forma simples e intuitiva. 
                        Tenha visão completa da sua vida financeira em um só lugar.
                    </p>
                    
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-custom btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Criar Conta Grátis
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-custom btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i>Ir para Dashboard
                            </a>
                        @endguest
                    </div>
                    
                    @guest
                    <div class="demo-credentials">
                        <h5><i class="fas fa-info-circle me-2"></i>Credenciais para Demonstração</h5>
                        <p class="mb-1"><strong>Admin:</strong> admin@financas.com | <strong>Senha:</strong> password</p>
                        <p class="mb-0"><strong>Usuário:</strong> usuario@financas.com | <strong>Senha:</strong> password</p>
                    </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Funcionalidades Completas</h2>
                    <p class="lead text-muted">Tudo que você precisa para gerenciar suas finanças pessoais</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4>Múltiplas Contas</h4>
                        <p class="text-muted">Gerencie várias contas bancárias, cartões e carteiras de forma organizada.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h4>Transações</h4>
                        <p class="text-muted">Registre receitas e despesas com categorização automática e controle total.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h4>Orçamentos</h4>
                        <p class="text-muted">Defina orçamentos por categoria e acompanhe seus gastos em tempo real.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4>Metas Financeiras</h4>
                        <p class="text-muted">Estabeleça e acompanhe o progresso das suas metas de economia e investimento.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Relatórios</h4>
                        <p class="text-muted">Visualize gráficos e relatórios detalhados sobre sua situação financeira.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Interface Responsiva</h4>
                        <p class="text-muted">Acesse de qualquer dispositivo com design adaptativo e intuitivo.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'FinancasApp') }}. Desenvolvido com Laravel.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        <i class="fas fa-code"></i> Feito com ❤️ para ajudar suas finanças
                    </small>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
