<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Atividade;
use App\Models\Aluno;

class ProfessorController extends Controller
{
    /**
     * Exibe o painel de relatórios do professor.
     */
    public function dashboard()
    {
        $professor = Auth::user();

        // ✅ Inclui status, nota e answers da tabela pivot
        $atividades = Atividade::where('id_professor', $professor->id_professor)
            ->with(['alunos' => function ($query) {
                $query->withPivot('status', 'nota', 'answers');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ Decodifica as respostas (answers) de JSON → array PHP
        foreach ($atividades as $atividade) {
            foreach ($atividade->alunos as $aluno) {
                if (!empty($aluno->pivot->answers)) {
                    $aluno->pivot->answers = json_decode($aluno->pivot->answers, true);
                } else {
                    $aluno->pivot->answers = [];
                }
            }
        }

        return view('professor.dashboard', ['atividades' => $atividades]);
    }

    /**
     * Mostra o formulário para criar uma nova atividade.
     */
    public function create()
    {
        $alunos = Aluno::orderBy('nome')->get();
        return view('professor.create', ['alunos' => $alunos]);
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
            'questions.*.explanation' => 'nullable|string', // Explicação opcional
        ]);

        $professor = Auth::user();

        // ✅ Garante que todas as perguntas tenham campo de explicação
        $questions = collect($request->questions)->map(function ($q) {
            $q['explanation'] = $q['explanation'] ?? '';
            return $q;
        })->values()->toArray();

        $quizData = ['questions' => $questions];

        // ✅ Cria a atividade
        $atividade = Atividade::create([
            'titulo' => $request->titulo,
            'descricao' => json_encode($quizData),
            'id_professor' => $professor->id_professor,
        ]);

        // ✅ Atribui aos alunos selecionados (ou a todos)
        if (in_array('todos', $request->alunos)) {
            $alunoIds = Aluno::pluck('id_aluno')->toArray();
            $atividade->alunos()->attach($alunoIds, [
                'status' => 'Pendente', // Status inicial
                'nota' => 0,
                'answers' => json_encode([]),
            ]);
        } else {
            $atividade->alunos()->attach($request->alunos, [
                'status' => 'Pendente',
                'nota' => 0,
                'answers' => json_encode([]),
            ]);
        }

        return redirect()
            ->route('professor.dashboard')
            ->with('success', 'Quiz criado e atribuído com sucesso!');
    }

    /**
     * Apaga uma atividade e as suas associações.
     */
    public function destroyAtividade(Atividade $atividade)
    {
        // ✅ Impede que um professor apague atividade de outro
        if ($atividade->id_professor !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Remove relações e a própria atividade
        $atividade->alunos()->detach();
        $atividade->delete();

        return redirect()
            ->route('professor.dashboard')
            ->with('success', 'Atividade apagada com sucesso!');
    }

    public function showAtividade($id)
{
    // Busca a atividade pelo ID ou lança erro 404
    $atividade = \App\Models\Atividade::findOrFail($id);

    // Carrega os alunos vinculados à atividade (tabela pivô: atv_alunos)
    $atividade->load(['alunos' => function ($query) {
        $query->withPivot('status', 'nota', 'answers');
    }]);

    // Decodifica o campo JSON 'answers' para array em cada aluno
    foreach ($atividade->alunos as $aluno) {
        if (!empty($aluno->pivot->answers)) {
            $decoded = json_decode($aluno->pivot->answers, true);
            $aluno->pivot->answers = is_array($decoded) ? $decoded : [];
        } else {
            $aluno->pivot->answers = [];
        }
    }

    // Retorna a view independente (sem extends)
    return view('professor.atividade_detalhes', compact('atividade'));
}
    public function show($id)
{
    $atividade = Atividade::with('alunos')->findOrFail($id);
    return view('professor.atividade_detalhes', compact('atividade'));
}
}
