<?php

namespace Tests\Feature;

use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProdutoApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function consegue_listar_produtos_com_paginacao()
    {
        Produto::factory()->count(10)->create();

        $response = $this->getJson('/api/produtos');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'current_page',
                     'data' => [
                         '*' => ['id', 'nome', 'preco', 'descricao', 'created_at', 'updated_at']
                     ],
                     'per_page',
                     'total'
                 ]);
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
        $produto = Produto::factory()->create();

        $response = $this->getJson("/api/produtos/{$produto->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $produto->id]);
    }

    #[Test]
    public function consegue_atualizar_um_produto()
    {
        $produto = Produto::factory()->create();

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
        $produto = Produto::factory()->create();

        $response = $this->deleteJson("/api/produtos/{$produto->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Produto removido com sucesso']);
    }
}
