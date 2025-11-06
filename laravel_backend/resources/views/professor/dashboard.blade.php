<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor - Relatórios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light text-dark">

<div class="container py-4">

    {{-- Mensagem de sucesso --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Relatórios de Atividades</h2>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-secondary">Sair</button>
        </form>
    </div>

    {{-- Botão criar atividade --}}
    <div class="mb-3">
        <a href="{{ route('professor.atividades.create') }}" class="btn btn-primary">
            Criar Novo Quiz
        </a>
    </div>

    {{-- FILTROS --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Filtros</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" id="filtroTitulo" class="form-control" placeholder="Filtrar por título">
                </div>
                <div class="col-md-4">
                    <input type="text" id="filtroAluno" class="form-control" placeholder="Filtrar por aluno">
                </div>
                <div class="col-md-4">
                    <select id="filtroStatus" class="form-select">
                        <option value="">-- Status --</option>
                        <option value="respondido">Respondido</option>
                        <option value="nao-respondido">Não Respondido</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- TABELA RELATÓRIO --}}
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" id="tabelaRelatorio">
            <thead class="table-dark">
                <tr>
                    <th>Atividade</th>
                    <th>Criada em</th>
                    <th>Aluno</th>
                    <th>Status</th>
                    <th>Respostas / Detalhes</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($atividades as $atividade)
                    @foreach($atividade->alunos as $aluno)
                        <tr>
                            <td>{{ $atividade->titulo }}</td>
                            <td>{{ $atividade->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $aluno->nome }}</td>
                            <td>
                                @if(!empty($aluno->pivot->answers))
                                    <span class="badge bg-success">Respondido</span>
                                @else
                                    <span class="badge bg-secondary">Não Respondido</span>
                                @endif
                            </td>

                            {{-- Respostas + Ver Detalhes --}}
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    {{-- Exibe se o aluno respondeu --}}
                                    @if(!empty($aluno->pivot->answers))
                                        <span class="text-success fw-bold">Respostas enviadas</span>
                                    @else
                                        <span class="text-muted fst-italic">Sem respostas</span>
                                    @endif

                                    {{-- Botão "Ver Detalhes" abaixo das respostas --}}
                                    <a href="{{ route('professor.atividades.show', $atividade->id_atividade) }}"
                                       class="btn btn-info btn-sm mt-1"
                                       target="_blank">
                                       Ver Detalhes
                                    </a>
                                </div>
                            </td>

                            {{-- Ações (Excluir) --}}
                            <td>
                                @if ($loop->first)
                                    <form action="{{ route('professor.atividades.destroy', $atividade->id_atividade) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Tem certeza que deseja excluir esta atividade?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Excluir Atividade</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        function filtrarTabela() {
            let titulo = $("#filtroTitulo").val().toLowerCase();
            let aluno = $("#filtroAluno").val().toLowerCase();
            let status = $("#filtroStatus").val();

            $("#tabelaRelatorio tbody tr").each(function() {
                let colTitulo = $(this).find("td:eq(0)").text().toLowerCase();
                let colAluno = $(this).find("td:eq(2)").text().toLowerCase();
                let colStatus = $(this).find("td:eq(3)").text().toLowerCase();

                let exibir = true;

                if (titulo && !colTitulo.includes(titulo)) exibir = false;
                if (aluno && !colAluno.includes(aluno)) exibir = false;
                if (status === "respondido" && !colStatus.includes("respondido")) exibir = false;
                if (status === "nao-respondido" && !colStatus.includes("não respondido")) exibir = false;

                $(this).toggle(exibir);
            });
        }

        $("#filtroTitulo, #filtroAluno, #filtroStatus").on("input change", filtrarTabela);
    });
</script>

</body>
</html>
