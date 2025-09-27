<?php

namespace App\Services;

use App\Models\Produto;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProdutoService
{
    /**
     * Lista todos os produtos paginados
     */
    public function listar(int $perPage = 5)
    {
        return Produto::paginate($perPage);
    }

    /**
     * Busca produtos por filtro (id, nome, preco, descricao)
     */
    public function buscar(string $filter, ?string $search, int $perPage = 5)
    {
        $query = Produto::query();

        if (!empty($search)) {
            switch ($filter) {
                case 'id':
                    // 🔹 Força o search a int para evitar o erro dos testes
                    $query->where('id', (int) $search);
                    break;

                case 'preco':
                    // 🔹 Compara preço de forma exata
                    $query->where('preco', $search);
                    break;

                case 'descricao':
                    $query->where('descricao', 'LIKE', "%{$search}%");
                    break;

                case 'nome':
                default:
                    $query->where('nome', 'LIKE', "%{$search}%");
                    break;
            }
        }

        return $query->paginate($perPage);
    }

    /**
     * Ver um produto específico
     */
    public function ver(int $id)
    {
        return Produto::findOrFail($id);
    }

    /**
     * Criar novo produto
     */
    public function criar(array $dados)
    {
        return Produto::create($dados);
    }

    /**
     * Atualizar produto existente
     */
    public function atualizar(int $id, array $dados)
    {
        $produto = Produto::findOrFail($id);
        $produto->update($dados);

        return $produto;
    }

    /**
     * Excluir produto
     */
    public function deletar(int $id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();

        return true;
    }
}
