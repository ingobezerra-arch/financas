<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
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
            'account_id' => [
                'required',
                'exists:accounts,id',
                Rule::exists('accounts', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'receipt_url' => ['nullable', 'url'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(),
            'status' => 'completed',
            'is_recurring' => false,
        ]);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'account_id.required' => 'A conta é obrigatória.',
            'account_id.exists' => 'A conta selecionada não é válida.',
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não é válida.',
            'type.required' => 'O tipo da transação é obrigatório.',
            'type.in' => 'O tipo deve ser receita ou despesa.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número válido.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'description.required' => 'A descrição é obrigatória.',
            'transaction_date.required' => 'A data é obrigatória.',
            'transaction_date.date' => 'A data deve ser válida.',
        ];
    }
}
