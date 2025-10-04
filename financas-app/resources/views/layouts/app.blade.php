<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-theme="{{ auth()->check() ? (auth()->user()->theme ?? 'light') : 'light' }}">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('accounts.index') }}">
                                    <i class="fas fa-wallet"></i> Contas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('categories.index') }}">
                                    <i class="fas fa-tags"></i> Categorias
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('transactions.index') }}">
                                    <i class="fas fa-exchange-alt"></i> Transações
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('recurring-transactions.index') }}">
                                    <i class="fas fa-redo-alt"></i> Recorrentes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('budgets.index') }}">
                                    <i class="fas fa-chart-pie"></i> Orçamentos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('goals.index') }}">
                                    <i class="fas fa-bullseye"></i> Metas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('reports.index') }}">
                                    <i class="fas fa-chart-line"></i> Relatórios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('bank-integrations.index') }}">
                                    <i class="fas fa-university"></i> Bancos
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="debtsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-credit-card"></i> Dívidas
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="debtsDropdown">
                                    <li><a class="dropdown-item" href="{{ route('debts.index') }}">
                                        <i class="fas fa-list"></i> Minhas Dívidas
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('debts.create') }}">
                                        <i class="fas fa-plus"></i> Nova Dívida
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('payment-plans.index') }}">
                                        <i class="fas fa-calendar-alt"></i> Planos de Pagamento
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('payment-plans.create') }}">
                                        <i class="fas fa-magic"></i> Criar Plano
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('payment-schedules.upcoming') }}">
                                        <i class="fas fa-clock"></i> Próximos Pagamentos
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('payment-schedules.overdue') }}">
                                        <i class="fas fa-exclamation-triangle text-danger"></i> Em Atraso
                                    </a></li>
                                </ul>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <!-- Botão rápido de tema -->
                            <li class="nav-item me-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleTheme()" title="Alternar Tema">
                                    <i class="fas fa-moon" id="themeIcon"></i>
                                </button>
                            </li>
                        @endauth
                        
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-circle"></i> Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    
    <!-- Custom Scripts -->
    @stack('scripts')
    
    <!-- Theme Script -->
    <script>
        // Função para aplicar tema imediatamente
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            document.body.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            // Forçar atualização de elementos dinâmicos
            const event = new CustomEvent('themeChanged', { detail: { theme: theme } });
            document.dispatchEvent(event);
        }
        
        // Aplicar tema imediatamente (antes do DOM carregar)
        (function() {
            @auth
                const userTheme = '{{ auth()->user()->theme ?? "light" }}';
                applyTheme(userTheme);
            @else
                const savedTheme = localStorage.getItem('theme') || 'light';
                applyTheme(savedTheme);
            @endauth
        })();
        
        // Confirmar aplicação quando DOM carregar
        document.addEventListener('DOMContentLoaded', function() {
            @auth
                const userTheme = '{{ auth()->user()->theme ?? "light" }}';
                if (document.body.getAttribute('data-theme') !== userTheme) {
                    applyTheme(userTheme);
                }
            @endauth
            
            // Adicionar listener para mudanças de tema via formulário
            const themeForm = document.getElementById('themeForm');
            if (themeForm) {
                themeForm.addEventListener('submit', function(e) {
                    const selectedTheme = document.getElementById('themeInput').value;
                    applyTheme(selectedTheme);
                });
            }
        });
        
        // Função global para alternância manual
        window.toggleTheme = function() {
            const currentTheme = document.body.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(newTheme);
            updateThemeIcon(newTheme);
            
            // Atualizar no servidor se logado
            @auth
                fetch('{{ route("profile.update-theme") }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ theme: newTheme })
                });
            @endauth
        };
        
        // Função para atualizar ícone do tema
        function updateThemeIcon(theme) {
            const themeIcon = document.getElementById('themeIcon');
            if (themeIcon) {
                themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                themeIcon.parentElement.title = theme === 'dark' ? 'Alternar para Tema Claro' : 'Alternar para Tema Escuro';
            }
        }
        
        // Atualizar ícone na inicialização
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = document.body.getAttribute('data-theme') || 'light';
            updateThemeIcon(currentTheme);
        });
    </script>
</body>
</html>
