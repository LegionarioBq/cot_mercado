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
            'imagem'     => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'preco.min'        => 'O preço não pode ser negativo.',
            'quantidade.min'   => 'A quantidade não pode ser negativa.',
            'imagem.image'     => 'O arquivo enviado deve ser uma imagem.',
            'imagem.mimes'     => 'A imagem deve estar no formato JPEG, PNG ou WEBP.',
            'imagem.max'       => 'A imagem não pode ultrapassar 10MB.',
        ];
    }
}
