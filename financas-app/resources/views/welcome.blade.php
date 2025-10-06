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
            background: #0f1419;
            min-height: 100vh;
            scroll-behavior: smooth;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1a1f2e 0%, #2d1b69 50%, #1e3a8a 100%);
            color: white;
            padding: 120px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23ffffff" fill-opacity="0.03" points="0,1000 1000,0 1000,1000"/></svg>');
            pointer-events: none;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .floating-element:nth-child(1) {
            top: 20%;
            left: 10%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }
        
        .floating-element:nth-child(2) {
            top: 60%;
            right: 15%;
            width: 60px;
            height: 60px;
            animation-delay: 2s;
        }
        
        .floating-element:nth-child(3) {
            top: 40%;
            left: 80%;
            width: 100px;
            height: 100px;
            animation-delay: 4s;
        }
        
        /* Feature Cards */
        .feature-icon {
            font-size: 3.5rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            display: inline-block;
        }
        
        .feature-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .feature-card:hover::before {
            left: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        /* Buttons */
        .btn-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 15px 35px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .btn-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-custom:hover::before {
            left: 100%;
        }
        
        .btn-custom:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
            color: white;
        }
        
        .btn-outline-custom {
            border: 2px solid white;
            color: white;
            padding: 13px 35px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: transparent;
        }
        
        .btn-outline-custom:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
        }
        
        /* Demo Credentials */
        .demo-credentials {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 15px;
            padding: 25px;
            margin-top: 40px;
            backdrop-filter: blur(10px);
        }
        
        /* Navigation */
        .navbar-brand {
            font-weight: bold;
            color: white !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover {
            color: white !important;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        /* Pricing Section */
        .pricing-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 100px 0;
        }
        
        .pricing-card {
            background: white;
            border-radius: 20px;
            padding: 60px 30px 40px 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: visible;
            border: 2px solid transparent;
            margin-top: 30px;
        }
        
        .pricing-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #667eea, #764ba2);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }
        
        .pricing-card.featured {
            transform: scale(1.05);
            border: 2px solid #667eea;
        }
        
        .pricing-card.featured::before {
            opacity: 0.1;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .pricing-badge {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            z-index: 10;
        }
        
        .price {
            font-size: 3rem;
            font-weight: bold;
            color: #1a202c;
            margin: 20px 0;
        }
        
        .price-period {
            color: #718096;
            font-size: 1rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }
        
        .feature-list li {
            padding: 10px 0;
            color: #4a5568;
            font-weight: 500;
        }
        
        .feature-list li i {
            color: #667eea;
            margin-right: 10px;
        }
        
        /* Statistics Section */
        .stats-section {
            background: #1a202c;
            color: white;
            padding: 80px 0;
        }
        
        .stat-card {
            text-align: center;
            padding: 30px 20px;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
        }
        
        .stat-label {
            color: #a0aec0;
            font-weight: 500;
            margin-top: 10px;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 80px 0;
            }
            
            .pricing-card.featured {
                transform: none;
                margin-top: 20px;
            }
            
            .price {
                font-size: 2.5rem;
            }
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
        <div class="floating-elements">
            <div class="floating-element"></div>
            <div class="floating-element"></div>
            <div class="floating-element"></div>
        </div>
        <div class="container hero-content">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1 class="display-3 fw-bold mb-4 fade-in-up">
                        <i class="fas fa-chart-line me-3"></i>Transforme Sua Vida Financeira
                    </h1>
                    <p class="lead mb-5 fade-in-up" style="font-size: 1.3rem; max-width: 800px; margin: 0 auto;">
                        A plataforma mais completa para gerenciar suas finanças pessoais. 
                        Controle total de receitas, despesas, investimentos e muito mais em uma interface moderna e intuitiva.
                    </p>
                    
                    <div class="d-flex gap-4 justify-content-center flex-wrap mb-5 fade-in-up">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-custom btn-lg">
                                <i class="fas fa-rocket me-2"></i>Começar Gratuitamente
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-custom btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-custom btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i>Acessar Dashboard
                            </a>
                        @endguest
                    </div>
                    
                    @guest
                    <div class="demo-credentials fade-in-up">
                        <h5><i class="fas fa-key me-2"></i>Teste Gratuitamente</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>👑 Admin:</strong> admin@financas.com</p>
                                <p class="mb-0"><strong>🔑 Senha:</strong> password</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>👤 Usuário:</strong> usuario@financas.com</p>
                                <p class="mb-0"><strong>🔑 Senha:</strong> password</p>
                            </div>
                        </div>
                    </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <span class="stat-number">10K+</span>
                        <div class="stat-label">Usuários Ativos</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <span class="stat-number">R$ 50M+</span>
                        <div class="stat-label">Transações Gerenciadas</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <span class="stat-number">98%</span>
                        <div class="stat-label">Satisfação dos Clientes</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <span class="stat-number">24/7</span>
                        <div class="stat-label">Suporte Disponível</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3" style="color: #1a202c;">Funcionalidades Poderosas</h2>
                    <p class="lead text-muted">Tudo que você precisa para ter controle total das suas finanças</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4 style="color: #1a202c;">Contas Múltiplas</h4>
                        <p class="text-muted">Gerencie bancos, cartões, carteiras digitais e investimentos em um só lugar.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h4 style="color: #1a202c;">Transações Inteligentes</h4>
                        <p class="text-muted">Categorizagitão automática, transferências e controle total de receitas e despesas.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h4 style="color: #1a202c;">Orçamentos Avançados</h4>
                        <p class="text-muted">Planejamento inteligente com alertas e acompanhamento em tempo real.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4 style="color: #1a202c;">Metas & Objetivos</h4>
                        <p class="text-muted">Defina metas de economia e acompanhe seu progresso com gráficos motivacionais.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 style="color: #1a202c;">Relatórios Premium</h4>
                        <p class="text-muted">Análises detalhadas, tendências e insights para otimizar suas finanças.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 style="color: #1a202c;">Segurança Total</h4>
                        <p class="text-muted">Criptografia de ponta, autenticação em duas etapas e backup automático.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Section -->
    <div class="pricing-section" id="pricing">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3" style="color: #1a202c;">Planos que Cabem no Seu Bolso</h2>
                    <p class="lead text-muted">Escolha o plano ideal para suas necessidades financeiras</p>
                </div>
            </div>
            
            <div class="row g-4 justify-content-center">
                <!-- Plano Básico Mensal -->
                <div class="col-lg-3 col-md-6">
                    <div class="pricing-card">
                        <h3 style="color: #1a202c; margin-bottom: 10px; margin-top: 10px;">Básico</h3>
                        <div class="price">
                            R$ 19<span class="price-period">/mês</span>
                        </div>
                        <p class="text-muted mb-4">Perfeito para iniciantes</p>
                        
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> Até 3 contas bancárias</li>
                            <li><i class="fas fa-check"></i> Controle de transações</li>
                            <li><i class="fas fa-check"></i> Orçamentos básicos</li>
                            <li><i class="fas fa-check"></i> Relatórios mensais</li>
                            <li><i class="fas fa-check"></i> Suporte por email</li>
                            <li><i class="fas fa-times text-muted"></i> Metas financeiras</li>
                            <li><i class="fas fa-times text-muted"></i> Sincronização bancária</li>
                        </ul>
                        
                        <a href="{{ route('checkout') }}?plan=basico_mensal" class="btn btn-custom w-100">
                            Começar Agora
                        </a>
                    </div>
                </div>
                
                <!-- Plano Básico Anual -->
                <div class="col-lg-3 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-badge">2 meses grátis</div>
                        <h3 style="color: #1a202c; margin-bottom: 10px; margin-top: 10px;">Básico Anual</h3>
                        <div class="price">
                            R$ 15<span class="price-period">/mês</span>
                        </div>
                        <p class="text-muted mb-4">Economize 21% no plano anual</p>
                        
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> Até 3 contas bancárias</li>
                            <li><i class="fas fa-check"></i> Controle de transações</li>
                            <li><i class="fas fa-check"></i> Orçamentos básicos</li>
                            <li><i class="fas fa-check"></i> Relatórios mensais</li>
                            <li><i class="fas fa-check"></i> Suporte por email</li>
                            <li><i class="fas fa-times text-muted"></i> Metas financeiras</li>
                            <li><i class="fas fa-times text-muted"></i> Sincronização bancária</li>
                        </ul>
                        
                        <a href="{{ route('checkout') }}?plan=basico_anual" class="btn btn-custom w-100">
                            Assinar Anual
                        </a>
                    </div>
                </div>
                
                <!-- Plano Plus Mensal -->
                <div class="col-lg-3 col-md-6">
                    <div class="pricing-card featured">
                        <div class="pricing-badge">Mais Popular</div>
                        <h3 style="color: #1a202c; margin-bottom: 10px; margin-top: 10px;">Plus</h3>
                        <div class="price">
                            R$ 39<span class="price-period">/mês</span>
                        </div>
                        <p class="text-muted mb-4">Para usuários avançados</p>
                        
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> Contas ilimitadas</li>
                            <li><i class="fas fa-check"></i> Todas as funcionalidades</li>
                            <li><i class="fas fa-check"></i> Orçamentos avançados</li>
                            <li><i class="fas fa-check"></i> Metas financeiras</li>
                            <li><i class="fas fa-check"></i> Sincronização bancária</li>
                            <li><i class="fas fa-check"></i> Relatórios premium</li>
                            <li><i class="fas fa-check"></i> Suporte prioritário</li>
                        </ul>
                        
                        <a href="{{ route('checkout') }}?plan=plus_mensal" class="btn btn-custom w-100">
                            Upgrade Agora
                        </a>
                    </div>
                </div>
                
                <!-- Plano Plus Anual -->
                <div class="col-lg-3 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-badge">Melhor custo-benefício</div>
                        <h3 style="color: #1a202c; margin-bottom: 10px; margin-top: 10px;">Plus Anual</h3>
                        <div class="price">
                            R$ 29<span class="price-period">/mês</span>
                        </div>
                        <p class="text-muted mb-4">Economize 26% no plano anual</p>
                        
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> Contas ilimitadas</li>
                            <li><i class="fas fa-check"></i> Todas as funcionalidades</li>
                            <li><i class="fas fa-check"></i> Orçamentos avançados</li>
                            <li><i class="fas fa-check"></i> Metas financeiras</li>
                            <li><i class="fas fa-check"></i> Sincronização bancária</li>
                            <li><i class="fas fa-check"></i> Relatórios premium</li>
                            <li><i class="fas fa-check"></i> Suporte prioritário</li>
                        </ul>
                        
                        <a href="{{ route('checkout') }}?plan=plus_anual" class="btn btn-custom w-100">
                            Assinar Plus Anual
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <p class="text-muted">
                        <i class="fas fa-shield-alt me-2"></i>
                        Todos os planos incluem garantia de 30 dias. Cancele a qualquer momento.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-line me-2"></i>{{ config('app.name', 'FinancasApp') }}
                    </h5>
                    <p class="text-light opacity-75">
                        A plataforma mais completa para gerenciar suas finanças pessoais. 
                        Controle, planeje e alcance seus objetivos financeiros.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light opacity-75 hover-opacity-100">
                            <i class="fab fa-facebook-f fa-lg"></i>
                        </a>
                        <a href="#" class="text-light opacity-75 hover-opacity-100">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="text-light opacity-75 hover-opacity-100">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                        <a href="#" class="text-light opacity-75 hover-opacity-100">
                            <i class="fab fa-linkedin-in fa-lg"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Produto</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Funcionalidades</a></li>
                        <li class="mb-2"><a href="#pricing" class="text-light opacity-75 text-decoration-none">Preços</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Segurança</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Integrações</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Empresa</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Sobre Nós</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Blog</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Carreiras</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Contato</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Suporte</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Central de Ajuda</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Documentação</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Status</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Relatar Bug</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Privacidade</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Termos</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">Cookies</a></li>
                        <li class="mb-2"><a href="#" class="text-light opacity-75 text-decoration-none">LGPD</a></li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 opacity-25">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light opacity-75">
                        &copy; {{ date('Y') }} {{ config('app.name', 'FinancasApp') }}. Todos os direitos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-light opacity-75">
                        <i class="fas fa-heart text-danger"></i> Desenvolvido com Laravel e muito café
                    </small>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
