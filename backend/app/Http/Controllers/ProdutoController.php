<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoStoreRequest;
use App\Http\Requests\ProdutoUpdateRequest;
use App\Services\ProdutoService;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    protected $produtoService;

    public function __construct(ProdutoService $produtoService)
    {
        $this->produtoService = $produtoService;
    }

    /**
     * Listar produtos com paginação
     */
    public function index(Request $request)
    {
        return response()->json(
            $this->produtoService->listar($request->get('per_page', 5))
        );
    }

    /**
     * Buscar produtos com filtro (id, nome, preco, descricao)
     */
    public function search(Request $request)
    {
        $search  = $request->get('search');
        $filter  = $request->get('filter', 'nome');
        $perPage = $request->get('per_page', 5);

        return response()->json(
            $this->produtoService->buscar($filter, $search, $perPage)
        );
    }

    /**
     * Ver um produto específico
     */
    public function show($id)
    {
        return response()->json(
            $this->produtoService->ver($id)
        );
    }

    /**
     * Criar novo produto
     */
    public function store(ProdutoStoreRequest $request)
    {
        return response()->json(
            $this->produtoService->criar($request->validated()),
            201
        );
    }

    /**
     * Atualizar produto existente
     */
    public function update(ProdutoUpdateRequest $request, $id)
    {
        return response()->json(
            $this->produtoService->atualizar($id, $request->validated())
        );
    }

    /**
     * Excluir produto
     */
    public function destroy($id)
    {
        $this->produtoService->deletar($id);

        return response()->json(['message' => 'Produto removido com sucesso']);
    }
}
