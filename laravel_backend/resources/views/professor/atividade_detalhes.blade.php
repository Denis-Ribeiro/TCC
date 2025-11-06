@extends('layouts.professor')

@section('content')
<div class="container mt-4">
    <h2>📘 Detalhes da Atividade #{{ $atividade->id_atividade }}</h2>
    <hr>

    {{-- Decodifica as perguntas do campo descricao --}}
    @php
        $decoded = json_decode($atividade->descricao, true);
        $questoes = isset($decoded['questions']) && is_array($decoded['questions'])
            ? $decoded['questions']
            : [];
    @endphp

    {{-- Lista de perguntas --}}
    @if (count($questoes) > 0)
        <div class="mt-3">
            <h4>📝 Perguntas</h4>
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Pergunta</th>
                        <th>Alternativas</th>
                        <th>Correta</th>
                        <th>Explicação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questoes as $index => $q)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $q['text'] ?? '—' }}</td>
                            <td>
                                @foreach($q['options'] ?? [] as $letra => $texto)
                                    <div>
                                        <strong>{{ strtoupper($letra) }}:</strong> {{ $texto }}
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ strtoupper($q['correct'] ?? '-') }}
                                </span>
                            </td>
                            <td>{{ $q['explanation'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning mt-3">
            Nenhuma questão encontrada ou formato inválido.
        </div>
    @endif

    {{-- Lista de respostas dos alunos --}}
    <div class="mt-5">
        <h4>🎓 Respostas dos Alunos</h4>
        @if($atividade->alunos->count() > 0)
            <table class="table table-striped table-hover mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Aluno</th>
                        <th>Status</th>
                        <th>Nota</th>
                        <th>Respostas</th>
                        <th>Enviado em</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($atividade->alunos as $aluno)
                       @php
                            $respostas = $aluno->pivot->answers;

                            // Se for JSON (string), decodifica
                            if (is_string($respostas)) {
                                $decoded = json_decode($respostas, true);
                                $respostas = is_array($decoded) ? $decoded : [];
                            }

                            // Se já for array, mantém
                            if (!is_array($respostas)) {
                                $respostas = [];
                            }
                        @endphp
                        <tr>
                            <td>{{ $aluno->nome }}</td>
                            <td>
                                @php
                                    $status = strtolower(trim((string) $aluno->pivot->status));
                                    $respondeu = !empty($aluno->pivot->answers);
                                @endphp

                                @if($status === 'respondido' || $status === '1' || $status === 'true' || $respondeu)
                                    <span class="badge bg-success">Respondido</span>
                                @else
                                    <span class="badge bg-secondary">Não Respondido</span>
                                @endif
                            </td>
                            <td>{{ $aluno->pivot->nota ?? '—' }}</td>
                            <td>
                                @if(is_array($respostas) && count($respostas) > 0)
                                    <ul class="mb-0">
                                        @foreach($respostas as $i => $resp)
                                            <li><strong>P{{ $i + 1 }}:</strong> {{ $resp }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <em>—</em>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($aluno->pivot->updated_at)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info mt-3">
                Nenhum aluno respondeu esta atividade ainda.
            </div>
        @endif
    </div>

    {{-- Rodapé --}}
    <div class="mt-4">
        <p><strong>Criada em:</strong> {{ $atividade->created_at->format('d/m/Y H:i') }}</p>
        <a href="{{ route('professor.dashboard') }}" class="btn btn-secondary">⬅ Voltar</a>
    </div>
</div>
@endsection
