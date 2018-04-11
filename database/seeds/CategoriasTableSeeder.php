<?php

use Illuminate\Database\Seeder;
use startnow\categorias;

class CategoriasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        categorias::create(['name' => 'Audio']);
        categorias::create(['name' => 'Energia']);
        categorias::create(['name' => 'Moda']);
        categorias::create(['name' => 'Tecnológia Ecologica']);
        categorias::create(['name' => 'Salud']);
        categorias::create(['name' => 'Transporte']);
        categorias::create(['name' => 'Viajes']);
    }
}
