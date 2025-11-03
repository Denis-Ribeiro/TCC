<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Criar Quiz</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #22306f, #000000 30%, #1b2a6b 100%);
    color: #f0f0f0;
    margin: 0;
    padding: 2rem;
}

.container {
    max-width: 700px;
    margin: auto;
    padding: 2rem;
    border-radius: 16px;
    background: rgba(20,30,60,0.7);
    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}

.top-actions {
    max-width: 700px;
    margin: 0 auto 1rem;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.btn-report {
    display: inline-block;
    padding: 0.6rem 1rem;
    border-radius: 12px;
    background-color: #6c757d;
    color: white;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.2s;
}

.btn-report:hover { background-color: #5a6268; }

h2 { color: #ffeb3b; margin-bottom: 1rem; }

.form-group { margin-bottom: 1rem; }

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

input[type="text"], select {
    width: 100%;
    padding: 0.75rem;
    border-radius: 12px;
    border: none;
    background: rgba(255,255,255,0.1);
    color: #f0f0f0;
    margin-bottom: 0.5rem;
    box-sizing: border-box;
}

.option-input { margin-bottom: 0.5rem; }

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
</style>
</head>
<body>

<!-- Botão que leva para a página de Relatórios (rota Blade) -->
<div class="top-actions">
    <a href="{{ route('professor.dashboard') }}" class="btn-report">Ver Relatórios</a>
</div>

<div class="container">
    <section class="form-section">
        <h2>Criar Novo Quiz</h2>

        <!-- Ajuste para Laravel -->
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

            <button type="submit" class="btn-submit">Salvar Quiz</button>
        </form>
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
            <select name="questions[${questionIndex}][correct]" required>
                <option value="a">a</option>
                <option value="b">b</option>
                <option value="c">c</option>
                <option value="d">d</option>
            </select>
            {{-- ▼▼▼ CAMPO DE EXPLICAÇÃO ADICIONADO ▼▼▼ --}}
            <label>Explicação da Resposta (opcional):</label>
            <input type="text" name="questions[${questionIndex}][explanation]" placeholder="Explicação da resposta correta">
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

