<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Romance',
                'slug' => 'romance',
                'description' => 'Histórias de amor e relacionamentos',
                'color' => '#ec4899',
                'order' => 1,
            ],
            [
                'name' => 'Ação',
                'slug' => 'acao',
                'description' => 'Adrenalina, lutas e aventura',
                'color' => '#ef4444',
                'order' => 2,
            ],
            [
                'name' => 'Comédia',
                'slug' => 'comedia',
                'description' => 'Momentos leves e risadas garantidas',
                'color' => '#f59e0b',
                'order' => 3,
            ],
            [
                'name' => 'Drama',
                'slug' => 'drama',
                'description' => 'Histórias emocionantes e realistas',
                'color' => '#8b5cf6',
                'order' => 4,
            ],
            [
                'name' => 'Mistério',
                'slug' => 'misterio',
                'description' => 'Enigmas e suspense que prendem a atenção',
                'color' => '#6366f1',
                'order' => 5,
            ],
            [
                'name' => 'Histórico',
                'slug' => 'historico',
                'description' => 'Histórias baseadas em eventos históricos',
                'color' => '#84cc16',
                'order' => 6,
            ],
            [
                'name' => 'Fantasia',
                'slug' => 'fantasia',
                'description' => 'Mundos mágicos e seres sobrenaturais',
                'color' => '#14b8a6',
                'order' => 7,
            ],
            [
                'name' => 'Terror',
                'slug' => 'terror',
                'description' => 'Sustos e histórias assustadoras',
                'color' => '#1f2937',
                'order' => 8,
            ],
            [
                'name' => 'Escolar',
                'slug' => 'escolar',
                'description' => 'Histórias ambientadas em escolas e faculdades',
                'color' => '#3b82f6',
                'order' => 9,
            ],
            [
                'name' => 'Médico',
                'slug' => 'medico',
                'description' => 'Dramas ambientados em hospitais',
                'color' => '#10b981',
                'order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
