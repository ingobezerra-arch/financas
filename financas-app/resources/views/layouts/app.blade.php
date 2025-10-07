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
    <link href="https://fonts.bunny.net/css?family=Inter:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-theme="{{ auth()->check() ? (auth()->user()->theme ?? 'light') : 'light' }}" class="bg-gray-50 dark:bg-gray-900">
    <div id="app" class="min-h-screen">
        @auth
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transform transition-all duration-300 ease-in-out" id="sidebar" data-collapsed="false">
            <div class="flex items-center justify-center h-16 px-4 border-b border-white/10" id="sidebar-header">
                <div class="flex items-center" id="sidebar-brand">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-white text-sm"></i>
                    </div>
                    <span class="ml-3 text-white font-bold text-lg sidebar-text">{{ config('app.name', 'FinançasPro') }}</span>
                </div>
            </div>
            
            <nav class="mt-8 px-4">
                <div class="space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        <span class="font-medium sidebar-text">Dashboard</span>
                    </a>
                    
                    <!-- Transações -->
                    <div class="sidebar-group">
                        <div class="sidebar-group-title" title="Transações">
                            <div class="flex items-center">
                                <i class="fas fa-exchange-alt mr-3"></i>
                                <span class="font-medium sidebar-text">Transações</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-chevron sidebar-text"></i>
                        </div>
                        <div class="sidebar-submenu">
                            <a href="{{ route('transactions.index') }}" class="sidebar-sublink {{ request()->routeIs('transactions.*') ? 'active' : '' }}" title="Todas as Transações">
                                <span class="sidebar-text">Todas as Transações</span>
                            </a>
                            <a href="{{ route('transactions.create') }}" class="sidebar-sublink" title="Nova Transação">
                                <span class="sidebar-text">Nova Transação</span>
                            </a>
                            <a href="{{ route('recurring-transactions.index') }}" class="sidebar-sublink {{ request()->routeIs('recurring-transactions.*') ? 'active' : '' }}" title="Recorrentes">
                                <span class="sidebar-text">Recorrentes</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Contas -->
                    <a href="{{ route('accounts.index') }}" class="sidebar-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" title="Contas">
                        <i class="fas fa-wallet mr-3"></i>
                        <span class="font-medium sidebar-text">Contas</span>
                    </a>
                    
                    <!-- Categorias -->
                    <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" title="Categorias">
                        <i class="fas fa-tags mr-3"></i>
                        <span class="font-medium sidebar-text">Categorias</span>
                    </a>
                    
                    <!-- Orçamentos -->
                    <a href="{{ route('budgets.index') }}" class="sidebar-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}" title="Orçamentos">
                        <i class="fas fa-chart-pie mr-3"></i>
                        <span class="font-medium sidebar-text">Orçamentos</span>
                    </a>
                    
                    <!-- Metas -->
                    <a href="{{ route('goals.index') }}" class="sidebar-link {{ request()->routeIs('goals.*') ? 'active' : '' }}" title="Metas">
                        <i class="fas fa-bullseye mr-3"></i>
                        <span class="font-medium sidebar-text">Metas</span>
                    </a>
                    
                    <!-- Dívidas -->
                    <div class="sidebar-group">
                        <div class="sidebar-group-title" title="Dívidas">
                            <div class="flex items-center">
                                <i class="fas fa-credit-card mr-3"></i>
                                <span class="font-medium sidebar-text">Dívidas</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-chevron sidebar-text"></i>
                        </div>
                        <div class="sidebar-submenu">
                            <a href="{{ route('debts.index') }}" class="sidebar-sublink {{ request()->routeIs('debts.*') ? 'active' : '' }}" title="Minhas Dívidas">
                                <span class="sidebar-text">Minhas Dívidas</span>
                            </a>
                            <a href="{{ route('payment-plans.index') }}" class="sidebar-sublink {{ request()->routeIs('payment-plans.*') ? 'active' : '' }}" title="Planos de Pagamento">
                                <span class="sidebar-text">Planos de Pagamento</span>
                            </a>
                            <a href="{{ route('payment-schedules.upcoming') }}" class="sidebar-sublink {{ request()->routeIs('payment-schedules.*') ? 'active' : '' }}" title="Próximos Pagamentos">
                                <span class="sidebar-text">Próximos Pagamentos</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Bancos -->
                    <a href="{{ route('bank-integrations.index') }}" class="sidebar-link {{ request()->routeIs('bank-integrations.*') ? 'active' : '' }}" title="Integrações Bancárias">
                        <i class="fas fa-university mr-3"></i>
                        <span class="font-medium sidebar-text">Integrações Bancárias</span>
                    </a>
                    
                    <!-- Relatórios -->
                    <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" title="Relatórios">
                        <i class="fas fa-chart-line mr-3"></i>
                        <span class="font-medium sidebar-text">Relatórios</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="ml-64 transition-all duration-300 ease-in-out" id="main-content" style="width: calc(100% - 16rem); overflow-x: hidden;">
            <!-- Top Navigation -->
            <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 h-16 w-full">
                <div class="px-6 h-full flex items-center justify-between">
                    <div class="flex items-center">
                        <button class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" id="sidebar-toggle" title="Expandir/Recolher Menu">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="hidden lg:flex items-center">
                            <div class="relative">
                                <input type="text" placeholder="Buscar..." class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Values Visibility Toggle -->
                        <button type="button" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleValuesVisibility()" title="Mostrar/Ocultar Valores" id="values-toggle">
                            <i class="fas fa-eye" id="values-icon"></i>
                        </button>
                        
                        <!-- Theme Toggle -->
                        <button type="button" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="toggleTheme()" title="Alternar Tema">
                            <i class="fas fa-moon dark:hidden"></i>
                            <i class="fas fa-sun hidden dark:block"></i>
                        </button>
                        
                        <!-- Notifications -->
                        <button class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 relative">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <span class="text-gray-700 dark:text-gray-300 font-medium">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-user-circle mr-2"></i> Perfil
                                </a>
                                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="w-full min-h-screen">
                <div class="container-fluid px-4 py-4" style="max-width: none; overflow-x: hidden;">
                    @yield('content')
                </div>
            </main>
        </div>
        @else
        <!-- Guest Layout -->
        <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 flex items-center justify-center">
            <div class="max-w-md w-full">
                @yield('content')
            </div>
        </div>
        @endauth
    </div>
    
    <!-- Custom Scripts -->
    @stack('scripts')
    
    <!-- Sidebar Toggle Fallback Script -->
    <script>
        // Fallback function if main script fails
        window.toggleSidebarFallback = function() {
            console.log('Using fallback toggle function');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleButton = document.getElementById('sidebar-toggle');
            
            if (!sidebar || !mainContent) {
                console.error('Required elements not found');
                return;
            }
            
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            console.log('Fallback - current state collapsed:', isCollapsed);
            
            if (isCollapsed) {
                // Expand
                console.log('Fallback - expanding');
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.style.width = '16rem';
                mainContent.classList.remove('main-content-collapsed');
                mainContent.style.marginLeft = '16rem';
                mainContent.style.width = 'calc(100% - 16rem)';
                sidebar.setAttribute('data-collapsed', 'false');
                if (toggleButton) {
                    toggleButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
                    toggleButton.title = 'Recolher Menu';
                }
                localStorage.setItem('sidebarCollapsed', 'false');
            } else {
                // Collapse
                console.log('Fallback - collapsing');
                sidebar.classList.add('sidebar-collapsed');
                sidebar.style.width = '4rem';
                mainContent.classList.add('main-content-collapsed');
                mainContent.style.marginLeft = '4rem';
                mainContent.style.width = 'calc(100% - 4rem)';
                sidebar.setAttribute('data-collapsed', 'true');
                if (toggleButton) {
                    toggleButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
                    toggleButton.title = 'Expandir Menu';
                }
                localStorage.setItem('sidebarCollapsed', 'true');
                // Close submenus
                document.querySelectorAll('.sidebar-group').forEach(group => {
                    group.classList.remove('open');
                });
            }
        };
        
        // Add click event listener when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('sidebar-toggle');
            if (toggleButton) {
                toggleButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Try main function first, fallback if it fails
                    try {
                        if (window.toggleSidebar) {
                            window.toggleSidebar();
                        } else {
                            window.toggleSidebarFallback();
                        }
                    } catch (error) {
                        console.error('Main toggle failed, using fallback:', error);
                        window.toggleSidebarFallback();
                    }
                });
            }
        });
    </script>
    
    <!-- Simple Theme Toggle -->
    <script>
        window.toggleTheme = function() {
            const body = document.body;
            const currentTheme = body.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            body.setAttribute('data-theme', newTheme);
        };
        
        // Values Visibility Toggle - Super Simple Version
        window.toggleValuesVisibility = function() {
            const body = document.body;
            const valuesHidden = body.classList.contains('values-hidden');
            const icon = document.getElementById('values-icon');
            const button = document.getElementById('values-toggle');
            
            if (valuesHidden) {
                // Show values
                body.classList.remove('values-hidden');
                if (icon) icon.className = 'fas fa-eye';
                if (button) button.title = 'Ocultar Valores';
                localStorage.setItem('valuesHidden', 'false');
                restoreValues();
            } else {
                // Hide values
                body.classList.add('values-hidden');
                if (icon) icon.className = 'fas fa-eye-slash';
                if (button) button.title = 'Mostrar Valores';
                localStorage.setItem('valuesHidden', 'true');
                hideValues();
            }
        };
        
        // Simple hide function - only replace text content
        function hideValues() {
            // Simple regex to find and replace R$ values in text nodes only
            const walker = document.createTreeWalker(
                document.body,
                NodeFilter.SHOW_TEXT,
                function(node) {
                    // Skip script, style, and the toggle button area
                    const parent = node.parentElement;
                    if (!parent || 
                        parent.tagName === 'SCRIPT' || 
                        parent.tagName === 'STYLE' ||
                        parent.closest('#values-toggle')) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    
                    // Only accept nodes with monetary values
                    return /R\$\s*[\d.,]+/.test(node.textContent) ? 
                           NodeFilter.FILTER_ACCEPT : 
                           NodeFilter.FILTER_REJECT;
                },
                false
            );
            
            let node;
            while (node = walker.nextNode()) {
                const parent = node.parentElement;
                if (!parent.hasAttribute('data-original-text')) {
                    parent.setAttribute('data-original-text', node.textContent);
                    node.textContent = node.textContent.replace(/R\$\s*[\d.,]+/g, 'R$ ••••••');
                }
            }
        }
        
        // Simple restore function
        function restoreValues() {
            document.querySelectorAll('[data-original-text]').forEach(element => {
                const originalText = element.getAttribute('data-original-text');
                if (originalText && element.firstChild && element.firstChild.nodeType === Node.TEXT_NODE) {
                    element.firstChild.textContent = originalText;
                    element.removeAttribute('data-original-text');
                }
            });
        }
        

        
        // Initialize values visibility on page load - Super Simple
        document.addEventListener('DOMContentLoaded', function() {
            const valuesHidden = localStorage.getItem('valuesHidden') === 'true';
            const body = document.body;
            const icon = document.getElementById('values-icon');
            const button = document.getElementById('values-toggle');
            
            if (valuesHidden) {
                body.classList.add('values-hidden');
                if (icon) icon.className = 'fas fa-eye-slash';
                if (button) button.title = 'Mostrar Valores';
                
                // Apply value hiding with delay to ensure DOM is ready
                setTimeout(() => {
                    try {
                        hideValues();
                    } catch (error) {
                        console.log('Error hiding values:', error);
                        // Fallback: just apply CSS class
                        body.classList.add('values-hidden');
                    }
                }, 1000);
            } else {
                body.classList.remove('values-hidden');
                if (icon) icon.className = 'fas fa-eye';
                if (button) button.title = 'Ocultar Valores';
            }
        });
    </script>
</body>
</html>
