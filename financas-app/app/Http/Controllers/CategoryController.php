<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $incomeCategories = auth()->user()->categories()
            ->where('type', 'income')
            ->active()
            ->orderBy('name')
            ->get();
            
        $expenseCategories = auth()->user()->categories()
            ->where('type', 'expense')
            ->active()
            ->orderBy('name')
            ->get();
            
        $inactiveCategories = auth()->user()->categories()
            ->where('is_active', false)
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('incomeCategories', 'expenseCategories', 'inactiveCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        auth()->user()->categories()->create($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $this->authorize('view', $category);
        
        // Transações da categoria no mês atual
        $monthlyTransactions = $category->transactions()
            ->with(['account'])
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->latest('transaction_date')
            ->get();
            
        // Total gasto/recebido na categoria este mês
        $monthlyTotal = $monthlyTransactions->sum('amount');
        
        // Transações recentes da categoria
        $recentTransactions = $category->transactions()
            ->with(['account'])
            ->latest('transaction_date')
            ->take(10)
            ->get();

        return view('categories.show', compact('category', 'monthlyTransactions', 'monthlyTotal', 'recentTransactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);
        
        $category->update($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);
        
        if ($category->transactions()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Não é possível excluir uma categoria que possui transações.');
        }
        
        if ($category->budgets()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Não é possível excluir uma categoria que possui orçamentos.');
        }
        
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }

    /**
     * Toggle category status (active/inactive)
     */
    public function toggleStatus(Category $category): RedirectResponse
    {
        $this->authorize('update', $category);
        
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'ativada' : 'desativada';
        
        return redirect()->route('categories.index')
            ->with('success', "Categoria {$status} com sucesso!");
    }
}
