<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id',
        'hash',
        'path',
        'mime_type',
        'size',
    ];

    /**
     * Sempre incluir no JSON o campo calculado "url"
     */
    protected $appends = ['url'];

    /**
     * Accessor: gera a URL pública da imagem
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Relacionamento: Imagem pertence a um produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
