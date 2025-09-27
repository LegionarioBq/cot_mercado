<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Produto;

class ProdutoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Roda o seeder que popula os 20 produtos do SQL
        $this->seed(\Database\Seeders\ProdutoSeeder::class);
    }

    #[Test]
    public function consegue_listar_produtos_com_paginacao()
    {
        $response = $this->getJson('/api/produtos?per_page=5&page=1');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'current_page',
                     'data' => [
                         '*' => ['id', 'nome', 'preco', 'descricao', 'created_at', 'updated_at']
                     ],
                     'per_page',
                     'total'
                 ])
                 ->assertJsonFragment(['nome' => 'Notebook Dell Inspiron']); // garante que o seed foi carregado
    }

    #[Test]
    public function consegue_criar_um_produto()
    {
        $payload = [
            'nome' => 'Notebook Gamer',
            'preco' => 5999.90,
            'descricao' => 'RTX 4060, 16GB RAM'
        ];

        $response = $this->postJson('/api/produtos', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nome' => 'Notebook Gamer']);
    }

    #[Test]
    public function consegue_ver_um_produto_especifico()
    {
        $produto = Produto::first(); // busca o primeiro do banco seedado

        $response = $this->getJson("/api/produtos/{$produto->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $produto->id,
                     'nome' => $produto->nome
                 ]);
    }

    #[Test]
    public function consegue_atualizar_um_produto()
    {
        $produto = Produto::first(); // pega qualquer produto existente

        $response = $this->putJson("/api/produtos/{$produto->id}", [
            'nome' => 'Produto Atualizado',
            'preco' => 999.99
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nome' => 'Produto Atualizado']);
    }

    #[Test]
    public function consegue_excluir_um_produto()
    {
        $produto = Produto::first(); // pega qualquer produto existente

        $response = $this->deleteJson("/api/produtos/{$produto->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Produto removido com sucesso']);
    }
}
