<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Atividades</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background-color: #f4f7f6; color: #333; }
        .container { max-width: 900px; margin: auto; }
        .atividade-card { border: 1px solid #dee2e6; padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .atividade-header { display: flex; justify-content: space-between; align-items: center; }
        .status { padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.8rem; font-weight: bold; }
        .status-pendente { background-color: #ffc107; color: #333; }
        .status-concluido { background-color: #28a745; color: white; }
        .question-block { margin-top: 1.5rem; }
        .options-list .option { margin-bottom: 0.5rem; }
        .btn-submit { background-color: #007bff; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; margin-top: 1rem; }
        .success-message { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
        h1, h2, h3 { color: #0056b3; }
        .back-link { display: inline-block; margin-bottom: 2rem; color: #007bff; text-decoration: none; }
        .results-section { margin-top: 1.5rem; }
        .result-question { border-top: 1px dashed #ccc; padding-top: 1rem; margin-top: 1rem; }
        .correct-answer { color: #28a745; font-weight: bold; }
        .incorrect-answer { color: #dc3545; font-weight: bold; }
        .explanation { font-style: italic; color: #6c757d; margin-top: 0.5rem; padding-left: 1rem; border-left: 3px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Minhas Atividades</h1>
        <a href="{{ route('aluno.dashboard') }}" class="back-link">&larr; Voltar ao Painel</a>

        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif

        @forelse($atividades as $atividade)
            <div class="atividade-card">
                <div class="atividade-header">
                    <h3>{{ $atividade->titulo }}</h3>
                    <span class="status {{ $atividade->pivot->status === 'Concluído' ? 'status-concluido' : 'status-pendente' }}">
                        {{ $atividade->pivot->status }}
                    </span>
                </div>
                <p><small>Professor(a): {{ $atividade->professor->nome ?? 'N/A' }}</small></p>

                @if($atividade->pivot->status === 'Pendente')
                    <form action="{{ route('aluno.quiz.submit', ['idAtividade' => $atividade->id_atividade]) }}" method="POST">
                        @csrf
                        @if(isset($atividade->quiz->questions))
                            @foreach($atividade->quiz->questions as $index => $question)
                                <div class="question-block">
                                    {{-- ▼▼▼ CORRIGIDO PARA SER COMPATÍVEL COM VERSÕES ANTERIORES ▼▼▼ --}}
                                    <p><strong>{{ $index + 1 }}. {{ $question->text ?? $question->question }}</strong></p>
                                    <div class="options-list">
                                        @foreach($question->options as $key => $option)
                                            <div class="option">
                                                <input type="radio" name="answers[{{ $index }}]" value="{{ $key }}" id="q{{$atividade->id_atividade}}_{{$index}}_opt{{$key}}" required>
                                                <label for="q{{$atividade->id_atividade}}_{{$index}}_opt{{$key}}">{{ strtoupper($key) }}) {{ $option }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <button type="submit" class="btn-submit">Enviar Respostas</button>
                        @endif
                    </form>
                @else
                    <div class="results-section">
                        <h4>Resultados:</h4>
                        <p><strong>Sua nota: {{ number_format($atividade->pivot->nota, 2) }} / 10</strong></p>
                        
                        @if(isset($atividade->quiz->questions))
                            @foreach($atividade->quiz->questions as $index => $question)
                                <div class="result-question">
                                    {{-- ▼▼▼ CORRIGIDO PARA SER COMPATÍVEL COM VERSÕES ANTERIORES ▼▼▼ --}}
                                    <p><strong>{{ $index + 1 }}. {{ $question->text ?? $question->question }}</strong></p>
                                    @php
                                        $userAnswerKey = $atividade->pivot->answers[$index] ?? null;
                                        // Também torna a chave de resposta correta compatível com versões anteriores
                                        $correctAnswerKey = $question->correct ?? $question->answer; 
                                        $isCorrect = ($userAnswerKey == $correctAnswerKey);
                                    @endphp
                                    <p class="{{ $isCorrect ? 'correct-answer' : 'incorrect-answer' }}">
                                        Sua resposta: {{ $userAnswerKey ? strtoupper($userAnswerKey) . ') ' . ($question->options->$userAnswerKey ?? 'Opção inválida') : 'Não respondida' }}
                                        {!! $isCorrect ? '&#9989; Correto!' : '&#10060; Incorreto.' !!}
                                    </p>
                                    @if(!$isCorrect)
                                        <p><strong>Resposta correta:</strong> {{ strtoupper($correctAnswerKey) . ') ' . ($question->options->$correctAnswerKey ?? 'Opção inválida') }}</p>
                                    @endif
                                    <p class="explanation"><em>Explicação: {{ $question->explanation ?? '' }}</em></p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <p>Você não tem nenhuma atividade atribuída no momento.</p>
        @endforelse
    </div>
</body>
</html>

