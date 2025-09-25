<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    // Listar todos os produtos
    public function index()
    {
        return response()->json([
            'data' => Produto::all()
        ]);
    }

    // Mostrar um produto específico
    public function show($id)
    {
        $produto = Produto::findOrFail($id);
        return response()->json($produto);
    }

    // Criar novo produto
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric',
            'descricao' => 'nullable|string',
        ]);

        $produto = Produto::create($validated);

        return response()->json($produto, 201);
    }

    // Atualizar produto
    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'sometimes|string|max:255',
            'preco' => 'sometimes|numeric',
            'descricao' => 'nullable|string',
        ]);

        $produto->update($validated);

        return response()->json($produto);
    }

    // Remover produto
    public function destroy($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();

        return response()->json(['message' => 'Produto removido com sucesso']);
    }
}
