<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produtos = [
            ['nome' => 'Notebook Dell Inspiron', 'preco' => 3500.00, 'descricao' => 'Notebook com processador i7 e 16GB RAM'],
            ['nome' => 'Teclado Mecânico', 'preco' => 250.00, 'descricao' => 'Teclado mecânico RGB para gamers'],
            ['nome' => 'Mouse Sem Fio Logitech', 'preco' => 120.00, 'descricao' => 'Mouse ergonômico sem fio'],
            ['nome' => 'Monitor LG 24\"', 'preco' => 800.00, 'descricao' => 'Monitor Full HD com 75Hz'],
            ['nome' => 'Headset HyperX', 'preco' => 400.00, 'descricao' => 'Headset com microfone removível'],
            ['nome' => 'Impressora HP Deskjet', 'preco' => 600.00, 'descricao' => 'Impressora multifuncional Wi-Fi'],
            ['nome' => 'Cadeira Gamer', 'preco' => 950.00, 'descricao' => 'Cadeira ergonômica ajustável'],
            ['nome' => 'HD Externo 1TB', 'preco' => 300.00, 'descricao' => 'HD portátil USB 3.0'],
            ['nome' => 'SSD 480GB Kingston', 'preco' => 280.00, 'descricao' => 'SSD rápido para notebooks e PCs'],
            ['nome' => 'Placa de Vídeo RTX 3060', 'preco' => 2800.00, 'descricao' => 'Placa de vídeo para games e design'],
            ['nome' => 'Processador Ryzen 5 5600X', 'preco' => 1300.00, 'descricao' => 'Processador com 6 núcleos e 12 threads'],
            ['nome' => 'Fonte 600W Corsair', 'preco' => 420.00, 'descricao' => 'Fonte certificada 80 Plus Bronze'],
            ['nome' => 'Placa-Mãe ASUS B450', 'preco' => 750.00, 'descricao' => 'Compatível com Ryzen de 3ª geração'],
            ['nome' => 'Memória RAM 8GB DDR4', 'preco' => 180.00, 'descricao' => 'Módulo DDR4 2666MHz'],
            ['nome' => 'Smartphone Samsung Galaxy A52', 'preco' => 1800.00, 'descricao' => 'Celular com 128GB de armazenamento'],
            ['nome' => 'Tablet Lenovo M10', 'preco' => 900.00, 'descricao' => 'Tablet Android com tela de 10 polegadas'],
            ['nome' => 'Carregador Portátil 10000mAh', 'preco' => 120.00, 'descricao' => 'Powerbank compacto e rápido'],
            ['nome' => 'Caixa de Som JBL Flip 5', 'preco' => 600.00, 'descricao' => 'Caixa de som Bluetooth à prova d’água'],
            ['nome' => 'Smartwatch Amazfit Bip U', 'preco' => 320.00, 'descricao' => 'Relógio inteligente com monitor cardíaco'],
            ['nome' => 'Câmera Logitech C920', 'preco' => 500.00, 'descricao' => 'Webcam Full HD para videoconferência'],
        ];

        // Insere todos os produtos de uma vez
        Produto::insert($produtos);
    }
}
