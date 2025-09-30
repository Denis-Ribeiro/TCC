<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atividade; // Importa o Model Atividade

class AtividadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Usa o Model Atividade para inserir os dados.
        Atividade::insert([
            [
                'titulo' => 'Resumo do Big Bang',
                'descricao' => 'Escreva um resumo de 200 palavras sobre a teoria do Big Bang.',
                'id_professor' => 1, // Atribuído ao Professor Carlos Silva
            ],
            [
                'titulo' => 'Questionário sobre a 2ª Guerra',
                'descricao' => 'Responda as 10 questões de múltipla escolha sobre a Segunda Guerra Mundial.',
                'id_professor' => 2, // Atribuído à Professora Ana Pereira
            ],
            [
                'titulo' => 'Experimento de Química',
                'descricao' => 'Realize o experimento de densidade da água e relate os resultados.',
                'id_professor' => 3, // Atribuído à Professora Beatriz Costa
            ],
        ]);
    }
}
