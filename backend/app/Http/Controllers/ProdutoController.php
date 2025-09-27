<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoStoreRequest;
use App\Http\Requests\ProdutoUpdateRequest;
use App\Services\ProdutoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProdutoController extends Controller
{
    protected $produtoService;

    public function __construct(ProdutoService $produtoService)
    {
        $this->produtoService = $produtoService;
    }

    /**
     * Listar produtos com paginação
     * (qualquer usuário autenticado pode visualizar)
     */
    public function index(Request $request)
    {
        return response()->json(
            $this->produtoService->listar($request->get('per_page', 5))
        );
    }

    /**
     * Buscar produtos com filtro (id, nome, preco, descricao)
     * (qualquer usuário autenticado pode buscar)
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
     * (qualquer usuário autenticado pode visualizar)
     */
    public function show($id)
    {
        return response()->json(
            $this->produtoService->ver($id)
        );
    }

    /**
     * Criar novo produto
     * (somente admin e editor)
     */
    public function store(ProdutoStoreRequest $request)
    {
        Gate::authorize('manage-produtos');

        return response()->json(
            $this->produtoService->criar($request->validated()),
            201
        );
    }

    /**
     * Atualizar produto existente
     * (somente admin e editor)
     */
    public function update(ProdutoUpdateRequest $request, $id)
    {
        Gate::authorize('manage-produtos');

        return response()->json(
            $this->produtoService->atualizar($id, $request->validated())
        );
    }

    /**
     * Excluir produto
     * (somente admin)
     */
    public function destroy($id)
    {
        Gate::authorize('delete-produtos');

        $this->produtoService->deletar($id);

        return response()->json(['message' => 'Produto removido com sucesso']);
    }
}
