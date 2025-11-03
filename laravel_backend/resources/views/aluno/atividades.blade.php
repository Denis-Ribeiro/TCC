<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Atividades</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            background: linear-gradient(135deg, #22306f, #000000 30%, #1b2a6b 100%);
            color: #f0f0f0; 
        }
        .container { 
            max-width: 900px; 
            margin: 2rem auto; 
            padding: 2rem; 
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            background: rgba(20,30,60,0.7);
        }
        h1 { color: #ffeb3b; margin-bottom: 1rem; }
        .back-link { display: inline-block; margin-bottom: 2rem; color: #ffeb3b; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }
        .atividade-card { background: rgba(30,40,70,0.6); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); padding: 1.5rem; margin-bottom: 1.5rem; transition: transform 0.2s, box-shadow 0.2s; }
        .atividade-header { display: flex; justify-content: space-between; align-items: center; }
        h3 { color: #f0f0f0; }
        .status { padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.8rem; font-weight: bold; }
        .status-pendente { background-color: #ffc107; color: #333; }
        .status-concluido { background-color: #28a745; color: white; }

        .question-block { margin-top: 1.5rem; }
        .options-list .option { margin-bottom: 0.5rem; }
        .options-list .option label { color: #ffeb3b; cursor: pointer; }
        input[type="radio"] + label { cursor: pointer; }
        .btn-submit { background-color: #28a745; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 12px; cursor: pointer; margin-top: 1rem; font-weight: bold; transition: background 0.3s; }
        .btn-submit:hover { background-color: #218838; }

        .success-message { color: #0f5132; background-color: #d1e7dd; border: 1px solid #badbcc; padding: 1rem; margin-bottom: 1rem; border-radius: 12px; }
        .results-section { margin-top: 1.5rem; }
        .result-question { border-top: 1px dashed #888; padding-top: 1rem; margin-top: 1rem; }

        .correct-answer { color: #155724; background: rgba(40,167,69,0.08); padding: 0.6rem; border-radius: 8px; font-weight: bold; }
        .incorrect-answer { color: #721c24; background: rgba(220,53,69,0.06); padding: 0.6rem; border-radius: 8px; font-weight: bold; }
        .correct-answer small, .incorrect-answer small { display: block; font-weight: normal; color: inherit; opacity: 0.95; margin-top: 0.4rem; }

        .explanation { font-style: italic; color: #c0c0c0; margin-top: 0.5rem; padding-left: 1rem; border-left: 3px solid #888; }
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
                    <span class="status status-{{ strtolower($atividade->pivot->status) }}">
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
                                    <p><strong>{{ $index + 1 }}. {{ $question->text ?? $question->question }}</strong></p>
                                    <div class="options-list">
                                        @php
                                            $options = is_array($question->options) ? $question->options : (array) $question->options;
                                        @endphp
                                        @foreach($options as $key => $option)
                                            <div class="option">
                                                <input type="radio" name="answers[{{ $index }}]" value="{{ $key }}" id="q{{$atividade->id_atividade}}_{{$index}}_opt{{$key}}" required>
                                                <label for="q{{$atividade->id_atividade}}_{{$index}}_opt{{$key}}">{{ $key }}) {{ $option }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <button type="submit" class="btn-submit">Enviar Respostas</button>
                        @else
                            <p>Este quiz não contém perguntas.</p>
                        @endif
                    </form>
                @else
                    <div class="results-section">
                        <h4>Resultados:</h4>
                        <p><strong>Sua nota: {{ number_format($atividade->pivot->nota, 2) }} / 10</strong></p>
                        
                        @if(isset($atividade->quiz->questions))
                            @php
                                // --- Normalizar o formato das respostas do usuário ---
                                $rawAnswers = $atividade->pivot->answers ?? [];
                                // Se for string JSON, decodifica
                                if(is_string($rawAnswers)) {
                                    $decoded = json_decode($rawAnswers, true);
                                    if(json_last_error() === JSON_ERROR_NONE) {
                                        $userAnswers = $decoded;
                                    } else {
                                        // se não for JSON válido, tenta transformar em array vazio
                                        $userAnswers = [];
                                    }
                                } elseif(is_object($rawAnswers)) {
                                    $userAnswers = (array) $rawAnswers;
                                } else {
                                    $userAnswers = is_array($rawAnswers) ? $rawAnswers : [];
                                }
                            @endphp

                            @foreach($atividade->quiz->questions as $index => $question)
                                @php
                                    $options = is_array($question->options) ? $question->options : (array) $question->options;
                                    $correctAnswerKey = $question->correct ?? null;

                                    // tentar localizar a resposta do aluno de várias formas:
                                    $userAnswerKey = null;

                                    // 1) por index numérico (0,1,2...)
                                    if(array_key_exists($index, $userAnswers)) {
                                        $userAnswerKey = $userAnswers[$index];
                                    }
                                    // 2) por index como string "0","1"
                                    elseif(array_key_exists((string)$index, $userAnswers)) {
                                        $userAnswerKey = $userAnswers[(string)$index];
                                    }
                                    // 3) por id da pergunta se existir (ex: $question->id)
                                    elseif(isset($question->id) && array_key_exists($question->id, $userAnswers)) {
                                        $userAnswerKey = $userAnswers[$question->id];
                                    }
                                    // 4) id como string
                                    elseif(isset($question->id) && array_key_exists((string)$question->id, $userAnswers)) {
                                        $userAnswerKey = $userAnswers[(string)$question->id];
                                    }
                                    // 5) por alguma chave 'key' da pergunta (ex: question->key)
                                    elseif(isset($question->key) && array_key_exists($question->key, $userAnswers)) {
                                        $userAnswerKey = $userAnswers[$question->key];
                                    }

                                    // Normaliza valores vazios
                                    if($userAnswerKey === '') {
                                        $userAnswerKey = null;
                                    }

                                    $isCorrect = ($userAnswerKey !== null && $correctAnswerKey !== null && (string)$userAnswerKey === (string)$correctAnswerKey);
                                @endphp

                                <div class="result-question">
                                    <p><strong>{{ $index + 1 }}. {{ $question->text ?? $question->question }}</strong></p>

                                    {{-- Se o aluno respondeu --}}
                                    @if($userAnswerKey !== null)
                                        @if($isCorrect)
                                            <div class="correct-answer">
                                                🎉 Perfeito!<br>
                                                <small>Sua resposta: {{ $userAnswerKey }}) {{ $options[$userAnswerKey] ?? 'Opção inválida' }}</small>
                                            </div>
                                        @else
                                            <div class="incorrect-answer">
                                                ❌ Resposta errada<br>
                                                <small>Sua resposta: {{ $userAnswerKey }}) {{ $options[$userAnswerKey] ?? 'Opção inválida' }}</small>
                                            </div>

                                            {{-- Mostrar resposta correta caso exista --}}
                                            @if($correctAnswerKey !== null)
                                                <p style="margin-top:0.6rem;"><strong>Resposta correta:</strong> {{ $correctAnswerKey }}) {{ $options[$correctAnswerKey] ?? 'Opção inválida' }}</p>
                                            @endif
                                        @endif
                                    @else
                                        {{-- Se desejar exibir algo quando não respondeu, descomente abaixo --}}
                                        {{-- <p class="text-muted"><em>Questão não respondida.</em></p> --}}
                                    @endif

                                    @if(isset($question->explanation) && !empty($question->explanation))
                                        <p class="explanation"><em>Explicação: {{ $question->explanation }}</em></p>
                                    @endif
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
