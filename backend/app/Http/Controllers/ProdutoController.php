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

    public function index(Request $request)
    {
        return response()->json(
            $this->produtoService->listar($request->get('per_page', 5))
        );
    }

    public function show($id)
    {
        return response()->json(
            $this->produtoService->ver($id)
        );
    }

    public function store(ProdutoStoreRequest $request)
    {
        return response()->json(
            $this->produtoService->criar($request->validated()),
            201
        );
    }

    public function update(ProdutoUpdateRequest $request, $id)
    {
        return response()->json(
            $this->produtoService->atualizar($id, $request->validated())
        );
    }

    public function destroy($id)
    {
        $this->produtoService->deletar($id);

        return response()->json(['message' => 'Produto removido com sucesso']);
    }
}
