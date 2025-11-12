<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Medicamentos',
                'description' => 'Medicamentos prescritos e de venda livre para diversos tratamentos',
                'is_active' => true,
            ],
            [
                'name' => 'Vitaminas e Suplementos',
                'description' => 'Suplementos alimentares, vitaminas e minerais essenciais',
                'is_active' => true,
            ],
            [
                'name' => 'Cuidados com a Pele',
                'description' => 'Produtos dermatológicos e para cuidados com a pele',
                'is_active' => true,
            ],
            [
                'name' => 'Saúde Sexual',
                'description' => 'Preservativos, anticoncepcionais e produtos íntimos',
                'is_active' => true,
            ],
            [
                'name' => 'Primeiros Socorros',
                'description' => 'Curativos, antissépticos e itens para emergências',
                'is_active' => true,
            ],
            [
                'name' => 'Equipamentos Médicos',
                'description' => 'Aparelhos de pressão, glicosímetros, nebulizadores',
                'is_active' => true,
            ],
            [
                'name' => 'Saúde Natural',
                'description' => 'Produtos naturais, fitoterápicos e orgânicos',
                'is_active' => true,
            ],
            [
                'name' => 'Cuidados Diários',
                'description' => 'Produtos para higiene pessoal e cuidados cotidianos',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'uuid' => Str::uuid(),
                'name' => $category['name'],
                'description' => $category['description'],
                'is_active' => $category['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
