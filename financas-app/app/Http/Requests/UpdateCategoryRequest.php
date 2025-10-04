<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && $this->route('category')->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $category = $this->route('category');
        
        return [
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('categories')->where(function ($query) use ($category) {
                    return $query->where('user_id', auth()->id())
                                ->where('type', $this->type)
                                ->where('id', '!=', $category->id);
                })
            ],
            'type' => ['required', 'string', 'in:income,expense'],
            'color' => ['sometimes', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => ['sometimes', 'string', 'max:50'],
            'description' => ['sometimes', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome para este tipo.',
            'type.required' => 'O tipo da categoria é obrigatório.',
            'type.in' => 'O tipo deve ser receita ou despesa.',
            'color.regex' => 'A cor deve estar no formato hexadecimal (#RRGGBB).',
        ];
    }
}
