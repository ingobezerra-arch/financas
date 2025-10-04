<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class GoalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $query = auth()->user()->goals()
            ->orderBy('target_date', 'asc');

        // Aplicar filtros
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'completed') {
                $query->completed();
            } elseif ($request->status === 'overdue') {
                $query->where('target_date', '<', now())
                      ->where('status', '!=', 'completed');
            }
        }

        $goals = $query->paginate(12);

        // Estatísticas
        $totalGoals = auth()->user()->goals()->count();
        $activeGoals = auth()->user()->goals()->active()->count();
        $completedGoals = auth()->user()->goals()->completed()->count();
        $totalTargetAmount = auth()->user()->goals()->active()->sum('target_amount');
        $totalCurrentAmount = auth()->user()->goals()->active()->sum('current_amount');
        $overdueGoals = auth()->user()->goals()
            ->where('target_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        return view('goals.index', compact(
            'goals',
            'totalGoals',
            'activeGoals',
            'completedGoals',
            'totalTargetAmount',
            'totalCurrentAmount',
            'overdueGoals'
        ));
    }

    public function create(): View
    {
        return view('goals.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0|lt:target_amount',
            'target_date' => 'required|date|after:today',
            'monthly_contribution' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['start_date'] = now();
        $validated['current_amount'] = $validated['current_amount'] ?? 0;
        $validated['status'] = 'active';
        $validated['color'] = $validated['color'] ?? '#007bff';
        $validated['icon'] = $validated['icon'] ?? 'fas fa-bullseye';

        Goal::create($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Meta criada com sucesso!');
    }

    public function show(Goal $goal): View
    {
        $this->authorize('view', $goal);

        // Calcular progresso mensal
        $monthlyProgress = [];
        $startDate = $goal->start_date->copy()->startOfMonth();
        $currentDate = now()->startOfMonth();
        
        while ($startDate->lte($currentDate)) {
            $monthlyProgress[] = [
                'month' => $startDate->format('M/Y'),
                'date' => $startDate->copy(),
                'target' => $goal->monthly_contribution ?? 0,
                'actual' => 0, // Aqui poderia implementar histórico de contribuições
            ];
            $startDate->addMonth();
        }

        return view('goals.show', compact('goal', 'monthlyProgress'));
    }

    public function edit(Goal $goal): View
    {
        $this->authorize('update', $goal);

        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'required|date',
            'monthly_contribution' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'status' => 'required|in:active,completed,cancelled',
        ]);

        // Verificar se a meta foi completada
        if ($validated['current_amount'] >= $validated['target_amount']) {
            $validated['status'] = 'completed';
        }

        $goal->update($validated);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Meta atualizada com sucesso!');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Meta excluída com sucesso!');
    }

    public function addContribution(Request $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $newAmount = $goal->current_amount + $validated['amount'];
        
        // Verificar se a meta será completada
        if ($newAmount >= $goal->target_amount) {
            $goal->update([
                'current_amount' => $goal->target_amount,
                'status' => 'completed'
            ]);
        } else {
            $goal->update([
                'current_amount' => $newAmount
            ]);
        }

        return back()->with('success', 'Contribuição adicionada com sucesso!');
    }

    public function removeContribution(Request $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $goal->current_amount,
            'description' => 'nullable|string|max:255',
        ]);

        $newAmount = max(0, $goal->current_amount - $validated['amount']);
        
        $goal->update([
            'current_amount' => $newAmount,
            'status' => 'active' // Volta para ativo se estava completa
        ]);

        return back()->with('success', 'Valor removido com sucesso!');
    }

    public function toggleStatus(Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $newStatus = match($goal->status) {
            'active' => 'cancelled',
            'cancelled' => 'active',
            'completed' => 'active',
            default => 'active'
        };

        // Se estiver reativando uma meta completa, verificar se ainda está completa
        if ($newStatus === 'active' && $goal->current_amount >= $goal->target_amount) {
            $newStatus = 'completed';
        }

        $goal->update(['status' => $newStatus]);

        $statusText = match($newStatus) {
            'active' => 'ativada',
            'cancelled' => 'cancelada',
            'completed' => 'marcada como completa',
        };

        return back()->with('success', "Meta {$statusText} com sucesso!");
    }
}
