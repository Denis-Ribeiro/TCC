<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Atividade;
use App\Models\Aluno;

class ProfessorController extends Controller
{
    /**
     * Exibe o painel do professor com as atividades e o progresso dos alunos.
     */
    public function dashboard()
    {
        $professor = Auth::user();
        
        // Carrega as atividades E os alunos associados a cada atividade
        $atividades = Atividade::where('id_professor', $professor->id_professor)
                                ->with('alunos') 
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Para cada atividade, percorre os alunos e descodifica as suas respostas
        foreach ($atividades as $atividade) {
            foreach($atividade->alunos as $aluno) {
                $aluno->pivot->answers = json_decode($aluno->pivot->answers, true);
            }
        }

        $alunos = Aluno::orderBy('nome')->get();

        return view('professor.dashboard', [
            'atividades' => $atividades,
            'alunos' => $alunos
        ]);
    }

    /**
     * Guarda um novo quiz manual e atribui aos alunos.
     */
    public function storeAtividade(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'alunos' => 'required|array',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|size:4',
            'questions.*.options.*' => 'required|string',
            'questions.*.correct' => 'required|string',
        ]);

        $professor = Auth::user();

        $quizData = [
            'questions' => array_values($request->questions)
        ];

        $atividade = Atividade::create([
            'titulo' => $request->titulo,
            'descricao' => json_encode($quizData),
            'id_professor' => $professor->id_professor,
        ]);

        $alunosSelecionados = $request->alunos;

        if (in_array('todos', $alunosSelecionados)) {
            $alunoIds = Aluno::pluck('id_aluno')->toArray();
            $atividade->alunos()->attach($alunoIds);
        } else {
            $atividade->alunos()->attach($alunosSelecionados);
        }

        return redirect()->route('professor.dashboard')->with('success', 'Quiz criado e atribuído com sucesso!');
    }

    /**
     * Apaga uma atividade e as suas associações.
     */
    public function destroyAtividade(Atividade $atividade)
    {
        if ($atividade->id_professor !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $atividade->alunos()->detach();
        $atividade->delete();

        return redirect()->route('professor.dashboard')->with('success', 'Atividade apagada com sucesso!');
    }
}

