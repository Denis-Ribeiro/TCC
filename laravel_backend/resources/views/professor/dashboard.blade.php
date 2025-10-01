<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel do Professor</title>
<style>
/* Corpo da página */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: linear-gradient(135deg, #22306f, #000000 30%, #1b2a6b 100%);
    color: #f0f0f0;
}

/* Container principal */
.container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    background: rgba(20,30,60,0.7);
}

h1 { color: #ffeb3b; }

/* Cabeçalho */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.btn-logout {
    background-color: #dc3545;
    color: white;
    padding: 0.5rem 1.2rem;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

.btn-logout:hover { background-color: #b52b2b; }

/* Formulário de criação de quiz */
.form-section {
    background: rgba(30,40,70,0.6);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    margin-bottom: 2rem;
}

.form-section h2 { color: #ffeb3b; margin-bottom: 1rem; }

.form-group { margin-bottom: 1rem; }

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

input[type="text"], textarea, select {
    width: 100%;
    padding: 0.75rem;
    border-radius: 12px;
    border: none;
    outline: none;
    background: rgba(255,255,255,0.1);
    color: #f0f0f0;
}

select[multiple] { height: auto; }

/* Diminuir espaçamento entre opções */
.option-input { margin-bottom: 0.5rem; }

/* Botões */
.btn-submit, #add-question, .remove-question {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

.btn-submit {
    background-color: #28a745;
    color: white;
}

.btn-submit:hover { background-color: #218838; }

#add-question {
    background-color: #6c757d;
    color: white;
    margin-top: 1rem;
}

#add-question:hover { background-color: #5a6268; }

.remove-question {
    background-color: #dc3545;
    color: white;
    margin-top: 0.5rem;
}

.remove-question:hover { background-color: #b52b2b; }

/* Cartões de atividades */
.atividades-section {
    background: rgba(30,40,70,0.6);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.atividades-section h2 { color: #ffeb3b; margin-bottom: 1rem; }

.atividade-item {
    background: rgba(20,30,60,0.5);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    transition: transform 0.2s, box-shadow 0.2s;
}

.atividade-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.5);
}

.atividade-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    cursor: pointer;
}

.btn-delete:hover { background-color: #b52b2b; }

/* Mensagem de sucesso */
.success-message {
    color: #0f5132;
    background-color: #d1e7dd;
    border: 1px solid #badbcc;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 12px;
}

/* Lista de alunos */
.student-list {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.2);
}

.student-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.student-item:last-child { border-bottom: none; }

.status-pendente { color: #ffc107; font-weight: bold; }
.status-concluido { color: #28a745; font-weight: bold; }

h3, h4 { color: #ffeb3b; margin: 0; }
p, small { color: #f0f0f0; }
</style>
</head>
<body>
<div class="container">
<div class="header">
<h1>Painel do Professor</h1>
<form action="{{ route('logout') }}" method="POST">
@csrf
<button type="submit" class="btn-logout">Sair</button>
</form>
</div>

<p>Bem-vindo, {{ Auth::user()->nome }}!</p>

<!-- Formulário de criação de quiz -->
<section class="form-section">
<h2>Criar Novo Quiz Manual</h2>
@if(session('success'))
<div class="success-message">{{ session('success') }}</div>
@endif

<form action="{{ route('professor.atividades.store') }}" method="POST">
@csrf
<div class="form-group">
<label for="titulo">Título do Quiz</label>
<input type="text" id="titulo" name="titulo" required>
</div>

<div id="questions-container"></div>
<button type="button" id="add-question">Adicionar Pergunta</button>

<hr style="margin: 1.5rem 0; border-color: rgba(255,255,255,0.2);">

<div class="form-group">
<label for="alunos">Atribuir para:</label>
<select name="alunos[]" id="alunos" multiple required size="5">
<option value="todos">Todos os Alunos</option>
@foreach($alunos as $aluno)
<option value="{{ $aluno->id_aluno }}">{{ $aluno->nome }}</option>
@endforeach
</select>
<small>Segure a tecla Ctrl (ou Cmd no Mac) para selecionar múltiplos alunos.</small>
</div>

<button type="submit" class="btn-submit">Lançar Atividade</button>
</form>
</section>

<!-- Lista de atividades -->
<section class="atividades-section">
<h2>Suas Atividades Lançadas</h2>
@forelse($atividades as $atividade)
<div class="atividade-item">
<div class="atividade-header">
<h3>{{ $atividade->titulo }}</h3>
<form action="{{ route('professor.atividades.destroy', $atividade) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja apagar esta atividade?');">
@csrf
@method('DELETE')
<button type="submit" class="btn-delete">Apagar</button>
</form>
</div>
<small>Criada em: {{ $atividade->created_at->format('d/m/Y') }}</small>
<div class="student-list">
<h4>Progresso dos Alunos:</h4>
@forelse($atividade->alunos as $aluno)
<div class="student-item">
<span>{{ $aluno->nome }}</span>
<span class="status-{{ strtolower($aluno->pivot->status) }}"><strong>Estado:</strong> {{ $aluno->pivot->status }}</span>
<span><strong>Nota:</strong> {{ $aluno->pivot->nota !== null ? number_format($aluno->pivot->nota, 2) : 'Não avaliado' }}</span>
</div>
@empty
<p>Nenhum aluno foi atribuído a esta atividade ainda.</p>
@endforelse
</div>
</div>
@empty
<p>Você ainda não criou nenhuma atividade.</p>
@endforelse
</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addQuestionBtn = document.getElementById('add-question');
    const questionsContainer = document.getElementById('questions-container');
    let questionIndex = 0;

    addQuestion();

    addQuestionBtn.addEventListener('click', addQuestion);

    function addQuestion() {
        const questionBlock = document.createElement('div');
        questionBlock.classList.add('form-group');

        questionBlock.innerHTML = `
        <div style="background: rgba(40,50,80,0.6); border-radius: 12px; padding: 1rem; margin-bottom: 1rem;">
            <h4>Pergunta ${questionIndex + 1}</h4>
            <label>Texto da Pergunta:</label>
            <input type="text" name="questions[${questionIndex}][text]" required>
            <label>Opções (a, b, c, d):</label>
            <input type="text" class="option-input" name="questions[${questionIndex}][options][a]" placeholder="Opção a" required>
            <input type="text" class="option-input" name="questions[${questionIndex}][options][b]" placeholder="Opção b" required>
            <input type="text" class="option-input" name="questions[${questionIndex}][options][c]" placeholder="Opção c" required>
            <input type="text" class="option-input" name="questions[${questionIndex}][options][d]" placeholder="Opção d" required>
            <label>Resposta Correta:</label>
            <select name="questions[${questionIndex}][correct]">
                <option value="a">a</option>
                <option value="b">b</option>
                <option value="c">c</option>
                <option value="d">d</option>
            </select>
            <button type="button" class="remove-question">Remover Pergunta</button>
        </div>
        `;

        questionsContainer.appendChild(questionBlock);
        questionIndex++;
    }

    questionsContainer.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-question')) {
            e.target.closest('.form-group').remove();
        }
    });
});
</script>
</body>
</html>
