@extends('layouts.app')

@section('content')
<div class="max-w-screen-2xl mx-auto py-6 animate-slide-in-up">
    <!-- Header Principal -->
    <div class="card-modern animate-fade-in-scale mb-8">
        <div class="flex items-center justify-between p-8 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center animate-float">
                    <i class="fas fa-chart-pie text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-plus-circle text-green-500 mr-3"></i>{{ __('Novo Orçamento') }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">Crie um orçamento para controlar seus gastos</p>
                </div>
            </div>
            <a href="{{ route('budgets.index') }}" class="btn-gradient-secondary animate-glow transform hover:scale-105 transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Formulário de Criação -->
    <div class="card-modern animate-fade-in-scale">
        <div class="p-8">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6 animate-slide-in-up">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Erro na validação:</strong>
                    </div>
                    <ul class="list-disc ml-6">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('budgets.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Informações Básicas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Coluna Esquerda -->
                    <div class="space-y-6">
                        <!-- Nome do Orçamento -->
                        <div class="form-group">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-tag text-purple-500 mr-2"></i>Nome do Orçamento
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   class="form-control w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('name') border-red-500 @enderror"
                                   placeholder="Ex: Alimentação Dezembro"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Categoria -->
                        <div class="form-group">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-tags text-blue-500 mr-2"></i>Categoria
                            </label>
                            <select id="category_id" 
                                    name="category_id" 
                                    class="form-control w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('category_id') border-red-500 @enderror"
                                    required>
                                <option value="">Selecione uma categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Apenas categorias de despesa podem ter orçamentos
                            </p>
                        </div>

                        <!-- Valor do Orçamento -->
                        <div class="form-group">
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-dollar-sign text-green-500 mr-2"></i>Valor do Orçamento
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500">R$</span>
                                <input type="number" 
                                       id="amount" 
                                       name="amount" 
                                       value="{{ old('amount') }}" 
                                       class="form-control w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('amount') border-red-500 @enderror"
                                       placeholder="0,00"
                                       step="0.01"
                                       min="0.01"
                                       required>
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Coluna Direita -->
                    <div class="space-y-6">
                        <!-- Período -->
                        <div class="form-group">
                            <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-calendar text-indigo-500 mr-2"></i>Período
                            </label>
                            <select id="period" 
                                    name="period" 
                                    class="form-control w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('period') border-red-500 @enderror"
                                    required>
                                <option value="">Selecione o período</option>
                                <option value="weekly" {{ old('period') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                <option value="monthly" {{ old('period') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                                <option value="quarterly" {{ old('period') == 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                                <option value="yearly" {{ old('period') == 'yearly' ? 'selected' : '' }}>Anual</option>
                            </select>
                            @error('period')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Data de Início -->
                        <div class="form-group">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-calendar-alt text-orange-500 mr-2"></i>Data de Início
                            </label>
                            <input type="date" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', now()->format('Y-m-d')) }}" 
                                   class="form-control w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('start_date') border-red-500 @enderror"
                                   required>
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Percentual de Alerta -->
                        <div class="form-group">
                            <label for="alert_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-bell text-yellow-500 mr-2"></i>Alerta quando atingir (%)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="alert_percentage" 
                                       name="alert_percentage" 
                                       value="{{ old('alert_percentage', 80) }}" 
                                       class="form-control w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('alert_percentage') border-red-500 @enderror"
                                       min="50"
                                       max="100"
                                       step="5"
                                       required>
                                <span class="absolute right-3 top-3 text-gray-500">%</span>
                            </div>
                            @error('alert_percentage')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Você será alertado quando os gastos atingirem este percentual
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Descrição -->
                <div class="form-group">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-align-left text-gray-500 mr-2"></i>Descrição (Opcional)
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4" 
                              class="form-control w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 @error('description') border-red-500 @enderror"
                              placeholder="Adicione uma descrição para este orçamento...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informações do Período -->
                <div id="period-info" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 hidden">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <strong class="text-blue-800 dark:text-blue-200">Informações do Período</strong>
                    </div>
                    <div class="text-blue-700 dark:text-blue-300 text-sm">
                        <p id="period-details"></p>
                        <p id="end-date-info"></p>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('budgets.index') }}" 
                       class="btn-gradient-secondary transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    
                    <button type="submit" 
                            class="btn-gradient-primary transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-save mr-2"></i>Criar Orçamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('period');
    const startDateInput = document.getElementById('start_date');
    const periodInfo = document.getElementById('period-info');
    const periodDetails = document.getElementById('period-details');
    const endDateInfo = document.getElementById('end-date-info');

    function updatePeriodInfo() {
        const period = periodSelect.value;
        const startDate = startDateInput.value;

        if (!period || !startDate) {
            periodInfo.classList.add('hidden');
            return;
        }

        const start = new Date(startDate);
        let end = new Date(start);
        let periodText = '';

        switch(period) {
            case 'weekly':
                end.setDate(start.getDate() + 6);
                periodText = 'Este orçamento será válido por 1 semana (7 dias).';
                break;
            case 'monthly':
                end.setMonth(start.getMonth() + 1);
                end.setDate(end.getDate() - 1);
                periodText = 'Este orçamento será válido por 1 mês.';
                break;
            case 'quarterly':
                end.setMonth(start.getMonth() + 3);
                end.setDate(end.getDate() - 1);
                periodText = 'Este orçamento será válido por 1 trimestre (3 meses).';
                break;
            case 'yearly':
                end.setFullYear(start.getFullYear() + 1);
                end.setDate(end.getDate() - 1);
                periodText = 'Este orçamento será válido por 1 ano.';
                break;
        }

        const endDateText = end.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        periodDetails.textContent = periodText;
        endDateInfo.innerHTML = `<strong>Data de término:</strong> ${endDateText}`;
        
        periodInfo.classList.remove('hidden');
        periodInfo.classList.add('animate-slide-in-up');
    }

    // Event listeners
    periodSelect.addEventListener('change', updatePeriodInfo);
    startDateInput.addEventListener('change', updatePeriodInfo);

    // Validação de formulário
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
                field.classList.remove('border-gray-300');
            } else {
                field.classList.remove('border-red-500');
                field.classList.add('border-gray-300');
            }
        });

        if (!isValid) {
            e.preventDefault();
            
            // Scroll para o primeiro campo com erro
            const firstError = form.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                firstError.focus();
            }
        }
    });

    // Formatação do campo de valor
    const amountInput = document.getElementById('amount');
    amountInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^\d.,]/g, '');
        value = value.replace(',', '.');
        
        // Limitar a 2 casas decimais
        const parts = value.split('.');
        if (parts[1] && parts[1].length > 2) {
            parts[1] = parts[1].substring(0, 2);
            value = parts.join('.');
        }
        
        e.target.value = value;
    });

    // Animações de entrada
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            group.style.transition = 'all 0.5s ease-out';
            group.style.opacity = '1';
            group.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Inicializar informações do período se já houver dados
    updatePeriodInfo();
});
</script>
@endpush