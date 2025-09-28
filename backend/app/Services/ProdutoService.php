<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\ProdutoImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ProdutoService
{
    /**
     * Lista todos os produtos paginados (com imagens)
     */
    public function listar(int $perPage = 5)
    {
        return Produto::with('imagens')->paginate($perPage);
    }

    /**
     * Busca produtos por filtro (id, nome, preco, quantidade, descricao) com imagens
     */
    public function buscar(string $filter, ?string $search, int $perPage = 5)
    {
        $query = Produto::query()->with('imagens');

        if (!empty($search)) {
            switch ($filter) {
                case 'id':
                    $query->where('id', (int) $search);
                    break;

                case 'preco':
                    $query->where('preco', $search);
                    break;

                case 'quantidade':
                    $query->where('quantidade', (int) $search);
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
     * Ver um produto específico (com imagens)
     */
    public function ver(int $id)
    {
        return Produto::with('imagens')->findOrFail($id);
    }

    /**
     * Criar novo produto + (opcional) imagem
     */
    public function criar(array $dados)
    {
        $imagem = $dados['imagem'] ?? null;
        unset($dados['imagem']);

        $produto = Produto::create($dados);

        if ($imagem) {
            $this->salvarImagem($produto, $imagem);
        }

        return $produto->load('imagens');
    }

    /**
     * Atualizar produto + (opcional) nova imagem
     */
    public function atualizar(int $id, array $dados)
    {
        $imagem = $dados['imagem'] ?? null;
        unset($dados['imagem']);

        $produto = Produto::findOrFail($id);
        $produto->update($dados);

        if ($imagem) {
            $this->salvarImagem($produto, $imagem);
        }

        return $produto->load('imagens');
    }

    /**
     * Excluir produto + arquivos de imagens relacionados
     */
    public function deletar(int $id)
    {
        $produto = Produto::with('imagens')->findOrFail($id);

        foreach ($produto->imagens as $img) {
            // transforma o caminho salvo no BD (img/xxxx.webp) para o storage
            $storagePath = "public/" . str_replace("img/", "img/", $img->path);

            if (Storage::exists($storagePath)) {
                Storage::delete($storagePath);
                Log::info("🗑️ Imagem removida do storage", ['path' => $storagePath]);
            }
        }

        $produto->imagens()->delete();
        $produto->delete();

        return true;
    }

    /**
     * Salva a imagem no disco público (storage/app/public/img)
     * e cria o registro em produto_images.
     * Se a hash já existir, lança erro para avisar ao usuário.
     */
    protected function salvarImagem(Produto $produto, UploadedFile $file): void
    {
        $mime = strtolower($file->getClientMimeType());
        $ext  = strtolower($file->getClientOriginalExtension());
        $size = (int) $file->getSize();

        $hash = sha1_file($file->getRealPath());

        if (ProdutoImage::where('hash', $hash)->exists()) {
            throw new UnprocessableEntityHttpException("Essa imagem já foi registrada no sistema.");
        }

        $filename = "{$hash}.{$ext}";

        //Salva dentro de storage/app/public/img
        $path = $file->storeAs("img", $filename, 'public');

        //Agora salva no BD apenas como "img/xxxx.webp"
        $relativePath = "img/{$filename}";

        // 🔎 Log para confirmar salvamento
        if (Storage::exists("public/img/{$filename}")) {
            Log::info("Imagem salva com sucesso no storage", [
                'produto_id' => $produto->id,
                'hash'       => $hash,
                'path'       => $relativePath,
                'url'        => "/{$relativePath}",
            ]);
        } else {
            Log::error("Erro ao salvar imagem no storage", [
                'produto_id' => $produto->id,
                'hash'       => $hash,
                'path'       => $relativePath,
            ]);
        }

        // Cria registro no banco
        $produto->imagens()->create([
            'hash'       => $hash,
            'path'       => $relativePath,
            'mime_type'  => $mime,
            'size'       => $size,
        ]);
    }

}
