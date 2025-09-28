<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'       => ['sometimes', 'string', 'max:255'],
            'preco'      => ['sometimes', 'numeric', 'min:0'],
            'quantidade' => ['sometimes', 'integer', 'min:0'],
            'descricao'  => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'preco.min'      => 'O preço não pode ser negativo.',
            'quantidade.min' => 'A quantidade não pode ser negativa.',
        ];
    }
}
