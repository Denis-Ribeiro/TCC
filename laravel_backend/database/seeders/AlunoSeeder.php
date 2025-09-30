<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aluno;
use Illuminate\Support\Facades\Hash; // Importa a classe Hash

class AlunoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Aluno::create([
            'nome' => 'João da Silva',
            'email' => 'joao.silva@aluno.com',
            'password' => Hash::make('password'), // Adiciona uma senha padrão
        ]);

        Aluno::create([
            'nome' => 'Maria Oliveira',
            'email' => 'maria.oliveira@aluno.com',
            'password' => Hash::make('password'), // Adiciona uma senha padrão
        ]);

        Aluno::create([
            'nome' => 'Pedro Martins',
            'email' => 'pedro.martins@aluno.com',
            'password' => Hash::make('password'), // Adiciona uma senha padrão
        ]);

        Aluno::create([
            'nome' => 'Sofia Santos',
            'email' => 'sofia.santos@aluno.com',
            'password' => Hash::make('password'), // Adiciona uma senha padrão
        ]);

        Aluno::create([
            'nome' => 'Lucas Ferreira',
            'email' => 'lucas.ferreira@aluno.com',
            'password' => Hash::make('password'), // Adiciona uma senha padrão
        ]);
    }
}

    

