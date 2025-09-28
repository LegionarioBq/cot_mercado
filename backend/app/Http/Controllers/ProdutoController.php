<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoStoreRequest;
use App\Http\Requests\ProdutoUpdateRequest;
use App\Services\ProdutoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
     * Buscar produtos com filtro (id, nome, preco, quantidade, descricao)
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
     * Criar novo produto (somente admin e editor)
     */
    public function store(ProdutoStoreRequest $request)
    {
        $user = auth()->user();
        Log::info('👤 Tentando criar produto', [
            'id'    => $user->id ?? null,
            'email' => $user->email ?? null,
            'type'  => $user->type ?? null,
        ]);

        if (! Gate::allows('manage-produtos')) {
            Log::warning('🚫 Acesso negado ao criar produto', [
                'id'    => $user->id ?? null,
                'email' => $user->email ?? null,
                'type'  => $user->type ?? null,
            ]);

            return response()->json([
                'message' => '❌ Apenas administradores e editores podem criar produtos.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Cria produto
        $dados   = $request->validated();
        $imagem  = $request->file('imagem');

        return response()->json(
            $this->produtoService->criar($dados, $imagem),
            Response::HTTP_CREATED
        );
    }

    /**
     * Atualizar produto existente (somente admin e editor)
     */
    public function update(ProdutoUpdateRequest $request, $id)
    {
        $user = auth()->user();
        Log::info('👤 Tentando atualizar produto', [
            'id'         => $user->id ?? null,
            'email'      => $user->email ?? null,
            'type'       => $user->type ?? null,
            'produto_id' => $id,
        ]);

        if (! Gate::allows('manage-produtos')) {
            Log::warning('🚫 Acesso negado ao atualizar produto', [
                'id'         => $user->id ?? null,
                'email'      => $user->email ?? null,
                'type'       => $user->type ?? null,
                'produto_id' => $id,
            ]);

            return response()->json([
                'message' => 'Apenas administradores e editores podem editar produtos.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Atualiza produto
        $dados   = $request->validated();
        $imagem  = $request->file('imagem');

        return response()->json(
            $this->produtoService->atualizar($id, $dados, $imagem)
        );
    }

    /**
     * Excluir produto (somente admin e editor)
     */
    public function destroy($id)
    {
        $user = auth()->user();
        Log::info('👤 Tentando excluir produto', [
            'id'         => $user->id ?? null,
            'email'      => $user->email ?? null,
            'type'       => $user->type ?? null,
            'produto_id' => $id,
        ]);

        if (! Gate::allows('delete-produtos')) {
            Log::warning('🚫 Acesso negado ao excluir produto', [
                'id'         => $user->id ?? null,
                'email'      => $user->email ?? null,
                'type'       => $user->type ?? null,
                'produto_id' => $id,
            ]);

            return response()->json([
                'message' => 'Apenas administradores podem excluir produtos.'
            ], Response::HTTP_FORBIDDEN);
        }

        $this->produtoService->deletar($id);

        return response()->json([
            'message' => 'Produto removido com sucesso'
        ]);
    }
}
