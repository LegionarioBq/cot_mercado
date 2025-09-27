<?php

namespace App\Services;

use App\Models\Produto;

class ProdutoService
{
    public function listar($perPage = 5)
    {
        return Produto::paginate($perPage);
    }

    public function ver($id)
    {
        return Produto::findOrFail($id);
    }

    public function criar(array $dados)
    {
        return Produto::create($dados);
    }

    public function atualizar($id, array $dados)
    {
        $produto = Produto::findOrFail($id);
        $produto->update($dados);
        return $produto;
    }

    public function deletar($id)
    {
        $produto = Produto::findOrFail($id);
        return $produto->delete();
    }
}
