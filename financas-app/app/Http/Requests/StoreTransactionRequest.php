<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

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
                'integer',
                'exists:accounts,id',
                Rule::exists('accounts', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id())
                                 ->where('is_active', true);
                })
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id())
                                 ->where('is_active', true);
                }),
                function ($attribute, $value, $fail) {
                    $category = \App\Models\Category::find($value);
                    $requestType = $this->input('type');
                    
                    if ($category && $category->type !== $requestType) {
                        $fail('A categoria selecionada não corresponde ao tipo de transação.');
                    }
                },
            ],
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/' // Máximo 2 casas decimais
            ],
            'description' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[\p{L}\p{N}\s\-_.,!?()]+$/u' // Caracteres válidos incluindo acentos
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
                'regex:/^[\p{L}\p{N}\s\-_.,!?()\n\r]+$/u'
            ],
            'transaction_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:' . Carbon::now()->subYears(5)->format('Y-m-d') // Não mais que 5 anos atrás
            ],
            'tags' => [
                'nullable',
                'array',
                'max:10' // Máximo 10 tags
            ],
            'tags.*' => [
                'string',
                'min:2',
                'max:30',
                'regex:/^[\p{L}\p{N}\-_]+$/u' // Apenas letras, números, hífen e underscore
            ],
            'receipt_url' => [
                'nullable',
                'url',
                'max:2048',
                'regex:/^https?:\/\/.+\.(jpg|jpeg|png|pdf|gif)$/i' // Apenas URLs de imagens/PDF
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpar e formatar dados antes da validação
        $this->merge([
            'user_id' => auth()->id(),
            'status' => 'completed',
            'is_recurring' => false,
            'amount' => $this->formatAmount($this->input('amount')),
            'description' => $this->sanitizeText($this->input('description')),
            'notes' => $this->sanitizeText($this->input('notes')),
            'tags' => $this->sanitizeTags($this->input('tags')),
        ]);
    }

    /**
     * Formatar valor monetário
     */
    private function formatAmount($amount): ?string
    {
        if (!$amount) return null;
        
        // Remover caracteres não numéricos exceto ponto e vírgula
        $cleaned = preg_replace('/[^0-9.,]/', '', $amount);
        
        // Converter vírgula para ponto
        $cleaned = str_replace(',', '.', $cleaned);
        
        // Garantir apenas um ponto decimal
        $parts = explode('.', $cleaned);
        if (count($parts) > 2) {
            $cleaned = $parts[0] . '.' . end($parts);
        }
        
        return $cleaned;
    }

    /**
     * Sanitizar texto
     */
    private function sanitizeText($text): ?string
    {
        if (!$text) return null;
        
        // Remover caracteres de controle e espaços extras
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }

    /**
     * Sanitizar tags
     */
    private function sanitizeTags($tags): ?array
    {
        if (!is_array($tags)) return null;
        
        return array_filter(array_map(function($tag) {
            $tag = trim(strtolower($tag));
            $tag = preg_replace('/[^\p{L}\p{N}\-_]/u', '', $tag);
            return $tag;
        }, $tags));
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'account_id.required' => 'A conta é obrigatória.',
            'account_id.exists' => 'A conta selecionada não é válida ou está inativa.',
            'account_id.integer' => 'A conta deve ser um número válido.',
            
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não é válida ou está inativa.',
            'category_id.integer' => 'A categoria deve ser um número válido.',
            
            'type.required' => 'O tipo da transação é obrigatório.',
            'type.in' => 'O tipo deve ser receita ou despesa.',
            
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número válido.',
            'amount.min' => 'O valor deve ser maior que R$ 0,01.',
            'amount.max' => 'O valor não pode exceder R$ 999.999.999,99.',
            'amount.regex' => 'O valor deve ter no máximo 2 casas decimais.',
            
            'description.required' => 'A descrição é obrigatória.',
            'description.min' => 'A descrição deve ter pelo menos 3 caracteres.',
            'description.max' => 'A descrição não pode exceder 255 caracteres.',
            'description.regex' => 'A descrição contém caracteres inválidos.',
            
            'notes.max' => 'As observações não podem exceder 1000 caracteres.',
            'notes.regex' => 'As observações contêm caracteres inválidos.',
            
            'transaction_date.required' => 'A data é obrigatória.',
            'transaction_date.date' => 'A data deve ser válida.',
            'transaction_date.before_or_equal' => 'A data não pode ser futura.',
            'transaction_date.after' => 'A data não pode ser anterior a 5 anos.',
            
            'tags.max' => 'Você pode adicionar no máximo 10 tags.',
            'tags.*.min' => 'Cada tag deve ter pelo menos 2 caracteres.',
            'tags.*.max' => 'Cada tag não pode exceder 30 caracteres.',
            'tags.*.regex' => 'As tags podem conter apenas letras, números, hífen e underscore.',
            
            'receipt_url.url' => 'A URL do comprovante deve ser válida.',
            'receipt_url.max' => 'A URL do comprovante é muito longa.',
            'receipt_url.regex' => 'A URL deve apontar para um arquivo de imagem ou PDF.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'account_id' => 'conta',
            'category_id' => 'categoria',
            'type' => 'tipo',
            'amount' => 'valor',
            'description' => 'descrição',
            'notes' => 'observações',
            'transaction_date' => 'data',
            'tags' => 'tags',
            'receipt_url' => 'URL do comprovante',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log de tentativas de validação falhadas para segurança
        \Log::info('Tentativa de criação de transação com dados inválidos', [
            'user_id' => auth()->id(),
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['_token'])
        ]);

        parent::failedValidation($validator);
    }
}