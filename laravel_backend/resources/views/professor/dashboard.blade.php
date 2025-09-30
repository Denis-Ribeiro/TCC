<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background-color: #f8f9fa; }
        .container { max-width: 900px; margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .form-section, .atividades-section { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="text"], textarea, select { width: 100%; padding: 0.5rem; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px; }
        button { padding: 0.75rem 1.5rem; cursor: pointer; border: none; border-radius: 4px; color: white; font-weight: bold; }
        .btn-submit { background-color: #007bff; }
        .btn-logout { background-color: #dc3545; }
        .btn-delete { background-color: #dc3545; font-size: 0.8rem; padding: 0.4rem 0.8rem; }
        #add-question, .remove-question { background-color: #6c757d; font-size: 0.8rem; padding: 0.5rem 1rem; margin-top: 0.5rem; }
        .atividade-item { border: 1px solid #dee2e6; padding: 1.5rem; margin-bottom: 1rem; border-radius: 5px; }
        .atividade-header { display: flex; justify-content: space-between; align-items: center; }
        .success-message { color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .student-list { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee; }
        .student-item { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; border-bottom: 1px solid #f1f1f1; }
        .student-item:last-child { border-bottom: none; }
        .status-pendente { color: #ffc107; }
        .status-concluido { color: #28a745; }
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

                <div id="questions-container">
                    <!-- Perguntas dinâmicas serão adicionadas aqui -->
                </div>
                <button type="button" id="add-question">Adicionar Pergunta</button>
                <hr style="margin: 1.5rem 0;">

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

        <section class="atividades-section">
            <h2>Suas Atividades Lançadas</h2>
            @forelse($atividades as $atividade)
                <div class="atividade-item">
                    <div class="atividade-header">
                        <h3>{{ $atividade->titulo }}</h3>
                        <form action="{{ route('professor.atividades.destroy', $atividade) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja apagar esta atividade? Esta ação não pode ser desfeita.');">
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

            // Adiciona uma pergunta inicial ao carregar a página
            addQuestion();

            addQuestionBtn.addEventListener('click', addQuestion);

            function addQuestion() {
                const questionBlock = document.createElement('div');
                questionBlock.classList.add('form-group');
                questionBlock.innerHTML = `
                    <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; border-radius: 5px;">
                        <h4>Pergunta ${questionIndex + 1}</h4>
                        <label>Texto da Pergunta:</label>
                        <input type="text" name="questions[${questionIndex}][text]" required>
                        <label>Opções (a, b, c, d):</label>
                        <input type="text" name="questions[${questionIndex}][options][a]" placeholder="Opção a" required>
                        <input type="text" name="questions[${questionIndex}][options][b]" placeholder="Opção b" required>
                        <input type="text" name="questions[${questionIndex}][options][c]" placeholder="Opção c" required>
                        <input type="text" name="questions[${questionIndex}][options][d]" placeholder="Opção d" required>
                        <label>Resposta Correta:</label>
                        <select name="questions[${questionIndex}][correct]">
                            <option value="a">a</option>
                            <option value="b">b</option>
                            <option value="c">c</option>
                            <option value="d">d</option>
                        </select>
                        <button type="button" class="remove-question" style="margin-top: 10px; background-color: #dc3545;">Remover Pergunta</button>
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

