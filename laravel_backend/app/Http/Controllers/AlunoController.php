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
        $atividades = $aluno->atividades()
                            ->with('professor')
                            ->orderBy('created_at', 'desc')
                            ->get();

        // ▼▼▼ CORREÇÃO IMPORTANTE AQUI ▼▼▼
        // Prepara TODOS os dados para a view usar, decodificando tanto
        // as perguntas do quiz quanto as respostas salvas do aluno.
        foreach ($atividades as $atividade) {
            $atividade->quiz = json_decode($atividade->descricao);
            
            // Decodifica as respostas do aluno de JSON para um array PHP.
            // Se não houver respostas, usa um array vazio para evitar erros.
            $atividade->pivot->answers = json_decode($atividade->pivot->answers, true) ?? [];
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
                // Compara a resposta enviada com a resposta correta no JSON
                if (isset($request->answers[$index]) && $request->answers[$index] === $question->correct) {
                    $score++;
                }
            }
            $nota = ($score / $totalQuestions) * 10;
        } else {
            $nota = 0;
        }

        // ▼▼▼ ATUALIZAÇÃO DO STATUS E NOTA ▼▼▼
        // Ao finalizar, o status é definido como 'Concluído'
        $aluno->atividades()->updateExistingPivot($idAtividade, [
            'status' => 'Concluído',
            'nota' => $nota,
            'answers' => json_encode($request->answers)
        ]);

        return redirect()->route('aluno.atividades')->with('success', 'Quiz finalizado com sucesso!');
    }
}