<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AtvAluno; // Importa o Model da tabela pivô

class AtvAlunoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Cria as conexões entre alunos e atividades
        AtvAluno::insert([
            // Atribui a Atividade 1 (Big Bang) ao Aluno 1 (João)
            [
                'id_aluno' => 1,
                'id_atividade' => 1,
                'status' => 'Pendente',
                'nota' => null,
            ],
            // Atribui a Atividade 2 (2ª Guerra) ao Aluno 1 (João)
            [
                'id_aluno' => 1,
                'id_atividade' => 2,
                'status' => 'Pendente',
                'nota' => null,
            ],
            // Atribui a Atividade 3 (Química) à Aluna 2 (Maria)
            [
                'id_aluno' => 2,
                'id_atividade' => 3,
                'status' => 'Concluída',
                'nota' => 8.5,
            ],
            // Atribui a Atividade 1 (Big Bang) ao Aluno 3 (Pedro)
            [
                'id_aluno' => 3,
                'id_atividade' => 1,
                'status' => 'Pendente',
                'nota' => null,
            ],
        ]);
    }
}
