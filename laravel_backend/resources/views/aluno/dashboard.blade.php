<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Aluno</title>
    <style>
        /* Corpo da página */
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            background: linear-gradient(135deg, #22306f, #000000 30%, #1b2a6b 100%);
            color: #f0f0f0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
        }

        /* Container principal */
        .container { 
            max-width: 900px; 
            width: 100%; 
            margin: auto; 
            padding: 2rem; 
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            background: rgba(20,30,60,0.7);
        }

        /* Cabeçalho */
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 2rem; 
        }

        .btn-logout { 
            background-color: #ff6b6b; 
            color: white; 
            padding: 0.5rem 1.2rem; 
            border: none; 
            border-radius: 12px; 
            cursor: pointer; 
            transition: background 0.3s; 
        }

        .btn-logout:hover { 
            background-color: #e55a5a; 
        }

        h1 { 
            color: #ffffff; 
        }

        /* Boas-vindas */
        .welcome-text { 
            margin-bottom: 3rem; 
            text-align: center; 
        }

        .welcome-text h2 { 
            color: #ffeb3b; 
        }

        .welcome-text p { 
            color: #c0c0c0; 
        }

        /* Grade de navegação */
        .navigation-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 2rem; 
        }

        /* Cartões de navegação */
        .nav-card { 
            background: rgba(40,50,90,0.7);
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); 
            text-decoration: none; 
            color: #f0f0f0; 
            padding: 2rem; 
            text-align: center; 
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s; 
        }

        .nav-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 6px 20px rgba(0,0,0,0.4); 
            background: rgba(0,123,255,0.8);
            color: white;
        }

        .nav-card h2 { 
            margin-top: 0; 
            color: #ffeb3b; 
        }

        .nav-card p { 
            color: #e0e0e0; 
        }
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
