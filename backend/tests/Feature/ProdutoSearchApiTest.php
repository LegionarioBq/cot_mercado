<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Produto;

class ProdutoSearchApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Roda o seeder com os 20 produtos
        $this->seed(\Database\Seeders\ProdutoSeeder::class);
    }

    #[Test]
    public function consegue_buscar_produtos_pelo_nome()
    {
        $produto = Produto::first();

        $response = $this->getJson("/api/produtos/search?search={$produto->nome}&filter=nome");

        $response->assertStatus(200)
                 ->assertJsonFragment(['nome' => $produto->nome]);
    }

    #[Test]
    public function consegue_buscar_produtos_pela_descricao()
    {
        $produto = Produto::whereNotNull('descricao')->first();

        $response = $this->getJson("/api/produtos/search?search={$produto->descricao}&filter=descricao");

        $response->assertStatus(200)
                 ->assertJsonFragment(['descricao' => $produto->descricao]);
    }

    #[Test]
    public function consegue_buscar_produtos_pelo_preco()
    {
        $produto = Produto::first();

        $response = $this->getJson("/api/produtos/search?search={$produto->preco}&filter=preco");

        $response->assertStatus(200)
                 ->assertJsonFragment(['nome' => $produto->nome]);
    }

    #[Test]
    public function consegue_buscar_produto_por_id()
    {
        $produto = Produto::first();

        $response = $this->getJson("/api/produtos/search?search={$produto->id}&filter=id");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $produto->id,
                     'nome' => $produto->nome
                 ]);
    }

    #[Test]
    public function retorna_vazio_se_nao_houver_resultados()
    {
        $response = $this->getJson('/api/produtos/search?search=InexistenteXYZ&filter=nome');

        $response->assertStatus(200)
                 ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function paginacao_funciona_na_busca()
    {
        $response = $this->getJson('/api/produtos/search?search=Produto&filter=nome&per_page=5&page=2');

        $response->assertStatus(200)
                 ->assertJsonFragment(['current_page' => 2])
                 ->assertJsonPath('per_page', 5);
    }
}
