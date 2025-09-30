<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Comente ou apague as linhas abaixo que tentam criar um 'User'
        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Mantém apenas as chamadas para os seus seeders personalizados
        $this->call([
            ProfessorSeeder::class,
            AlunoSeeder::class,
            AtividadeSeeder::class,
            AtvAlunoSeeder::class,
        ]);
    }
}

    
