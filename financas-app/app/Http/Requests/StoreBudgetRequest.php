<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StoreBudgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'exists:categories,id',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id())
                                 ->where('type', 'expense');
                })
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3'
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99'
            ],
            'period' => [
                'required',
                'in:weekly,monthly,quarterly,yearly'
            ],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'alert_percentage' => [
                'required',
                'numeric',
                'min:50',
                'max:100'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Selecione uma categoria para o orçamento.',
            'category_id.exists' => 'A categoria selecionada não existe ou não pertence a você.',
            'name.required' => 'O nome do orçamento é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'amount.required' => 'O valor do orçamento é obrigatório.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'amount.max' => 'O valor é muito alto.',
            'period.required' => 'Selecione o período do orçamento.',
            'period.in' => 'Período inválido.',
            'start_date.required' => 'A data de início é obrigatória.',
            'start_date.after_or_equal' => 'A data de início deve ser hoje ou no futuro.',
            'alert_percentage.required' => 'Defina a porcentagem para alertas.',
            'alert_percentage.min' => 'A porcentagem de alerta deve ser pelo menos 50%.',
            'alert_percentage.max' => 'A porcentagem de alerta não pode ser maior que 100%.',
        ];
    }
}
