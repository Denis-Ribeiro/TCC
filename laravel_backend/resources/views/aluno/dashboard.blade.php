<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Aluno</title>
    <style>
        body { font-family: sans-serif; margin: 0; background-color: #f4f7f6; color: #333; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { max-width: 900px; width: 100%; margin: auto; padding: 2rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .logout-form { display: inline; }
        .btn-logout { background-color: #6c757d; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .welcome-text { margin-bottom: 3rem; text-align: center; }
        .navigation-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        .nav-card { background-color: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-decoration: none; color: inherit; padding: 2rem; text-align: center; transition: transform 0.2s, box-shadow 0.2s; }
        .nav-card:hover { transform: translateY(-5px); box-shadow: 0 6px 16px rgba(0,0,0,0.15); }
        .nav-card h2 { margin-top: 0; color: #0056b3; }
        .nav-card p { color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Painel do Aluno</h1>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">Sair</button>
            </form>
        </div>

        <div class="welcome-text">
            <h2>Bem-vindo, {{ Auth::user()->nome }}!</h2>
            <p>O que você gostaria de fazer hoje?</p>
        </div>

        <div class="navigation-grid">
            <a href="http://localhost:5173" class="nav-card">
                <h2>Ferramentas de Estudo</h2>
                <p>Acesse o Tutor com IA, o Gerador de Quizzes e os Jogos Educativos para praticar e aprender.</p>
            </a>
            <a href="{{ route('aluno.atividades') }}" class="nav-card">
                <h2>Minhas Atividades</h2>
                <p>Veja e responda aos quizzes e atividades enviados pelos seus professores.</p>
            </a>
        </div>
    </div>
</body>
</html>

