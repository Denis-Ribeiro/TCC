<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Professor;
use Illuminate\Support\Facades\Hash; // Importa a classe Hash

class ProfessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usa o Model para criar os registos
        Professor::create([
            'nome' => 'Carlos Silva',
            'email' => 'carlos.silva@escola.com',
            'password' => Hash::make('password'), // Adiciona uma senha padrão
        ]);

        Professor::create([
            'nome' => 'Ana Pereira',
            'email' => 'ana.pereira@escola.com',
            'password' => Hash::make('password'), // Adiciona uma senha padrão
        ]);

        Professor::create([
            'nome' => 'Beatriz Costa',
            'email' => 'beatriz.costa@escola.com',
            'password' => Hash::make('password'), // Adiciona uma senha padrão
        ]);
    }
}

    

