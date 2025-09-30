<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlunoController extends Controller
{
    /**
     * Exibe o painel principal do aluno (o hub de navegação).
     */
    public function dashboard()
    {
        return view('aluno.dashboard');
    }

    /**
     * Exibe a página com a lista de atividades do aluno.
     */
    public function atividades()
    {
        $aluno = Auth::user();
        $atividades = $aluno->atividades()->with('professor')->orderBy('created_at', 'desc')->get();

        // Prepara os dados para a view
        foreach ($atividades as $atividade) {
            $atividade->quiz = json_decode($atividade->descricao);
            $atividade->pivot->answers = json_decode($atividade->pivot->answers, true); 
        }

        return view('aluno.atividades', [
            'atividades' => $atividades
        ]);
    }

    /**
     * Processa as respostas de um quiz, calcula a nota e guarda as respostas.
     */
    public function submitQuiz(Request $request, $idAtividade)
    {
        $request->validate([
            'answers' => 'required|array'
        ]);

        $aluno = Auth::user();
        $atividade = $aluno->atividades()->findOrFail($idAtividade);
        $quizData = json_decode($atividade->descricao);
        
        $score = 0;
        $totalQuestions = isset($quizData->questions) ? count($quizData->questions) : 0;

        if ($totalQuestions > 0) {
            foreach ($quizData->questions as $index => $question) {
                // ▼▼▼ A CORREÇÃO ESTÁ AQUI ▼▼▼
                // A chave da resposta correta no nosso JSON é 'correct', não 'answer'.
                if (isset($request->answers[$index]) && $request->answers[$index] === $question->correct) {
                    $score++;
                }
            }
            $nota = ($score / $totalQuestions) * 10;
        } else {
            $nota = 0;
        }

        // Atualiza a tabela pivô com a nota, o estado e as respostas em formato JSON
        $aluno->atividades()->updateExistingPivot($idAtividade, [
            'status' => 'Concluído',
            'nota' => $nota,
            'answers' => json_encode($request->answers)
        ]);

        return redirect()->route('aluno.atividades')->with('success', 'Quiz finalizado com sucesso!');
    }
}

