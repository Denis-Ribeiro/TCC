<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Atividades</title>
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

        h1 { 
            color: #ffeb3b; 
            margin-bottom: 1rem;
        }

        /* Link de voltar */
        .back-link { 
            display: inline-block; 
            margin-bottom: 2rem; 
            color: #ffeb3b; 
            text-decoration: none; 
            font-weight: bold;
        }
        .back-link:hover { 
            text-decoration: underline; 
        }

        /* Cartão de atividade */
        .atividade-card { 
            background: rgba(30,40,70,0.6);
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); 
            padding: 1.5rem; 
            margin-bottom: 1.5rem; 
            transition: transform 0.2s, box-shadow 0.2s, background 0.3s;
        }
        .atividade-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.5);
            background: rgba(0,123,255,0.6);
        }

        .atividade-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }

        .status { 
            padding: 0.25rem 0.75rem; 
            border-radius: 12px; 
            font-size: 0.8rem; 
            font-weight: bold; 
        }
        .status-pendente { 
            background-color: #ffc107; 
            color: #333; 
        }
        .status-concluido { 
            background-color: #28a745; 
            color: white; 
        }

        .question-block { 
            margin-top: 1.5rem; 
        }

        .options-list .option { 
            margin-bottom: 0.5rem; 
        }
        .options-list .option label {
            color: #ffeb3b; 
            cursor: pointer;
        }

        /* Botão enviar */
        .btn-submit { 
            background-color: #28a745; 
            color: white; 
            padding: 0.75rem 1.5rem; 
            border: none; 
            border-radius: 12px; 
            cursor: pointer; 
            margin-top: 1rem; 
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background-color: #218838;
        }

        /* Mensagem de sucesso */
        .success-message { 
            color: #0f5132; 
            background-color: #d1e7dd; 
            border: 1px solid #badbcc;
            padding: 1rem; 
            margin-bottom: 1rem; 
            border-radius: 12px; 
        }

        /* Resultados */
        .results-section { 
            margin-top: 1.5rem; 
        }

        .result-question { 
            border-top: 1px dashed #888; 
            padding-top: 1rem; 
            margin-top: 1rem; 
        }

        .correct-answer { 
            color: #28a745; 
            font-weight: bold; 
        }

        .incorrect-answer { 
            color: #dc3545; 
            font-weight: bold; 
        }

        .explanation { 
            font-style: italic; 
            color: #c0c0c0; 
            margin-top: 0.5rem; 
            padding-left: 1rem; 
            border-left: 3px solid #888; 
        }

        input[type="radio"] + label {
            cursor: pointer;
        }

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
                                    <p><strong>{{ $index + 1 }}. {{ $question->text ?? $question->question }}</strong></p>
                                    @php
                                        $userAnswerKey = $atividade->pivot->answers[$index] ?? null;
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
