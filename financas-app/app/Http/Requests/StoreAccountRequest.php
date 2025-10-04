<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:checking,savings,credit_card,investment,cash'],
            'balance' => ['required', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'color' => ['sometimes', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => ['sometimes', 'string', 'max:50'],
            'description' => ['sometimes', 'string', 'max:500'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(),
            'currency' => $this->currency ?? 'BRL',
            'color' => $this->color ?? '#007bff',
            'is_active' => true,
        ]);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome da conta é obrigatório.',
            'type.required' => 'O tipo da conta é obrigatório.',
            'type.in' => 'O tipo da conta deve ser: conta corrente, poupança, cartão de crédito, investimento ou dinheiro.',
            'balance.required' => 'O saldo inicial é obrigatório.',
            'balance.numeric' => 'O saldo deve ser um número válido.',
            'balance.min' => 'O saldo não pode ser negativo.',
            'color.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
        ];
    }
}
