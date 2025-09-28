<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'       => ['required', 'string', 'max:255'],
            'preco'      => ['required', 'numeric', 'min:0'],
            'quantidade' => ['required', 'integer', 'min:0'],
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
