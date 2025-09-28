<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'preco',
        'quantidade',
        'descricao',
    ];

    /**
     * Relacionamento: Produto possui muitas imagens
     */
    public function imagens()
    {
        return $this->hasMany(ProdutoImage::class, 'produto_id');
    }
}
