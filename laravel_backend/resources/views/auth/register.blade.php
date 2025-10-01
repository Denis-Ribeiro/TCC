<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta</title>
    <style>
        body {
            background-color: #121212;
            color: #E0E0E0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #1E1E1E;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.5);
            width: 350px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffffff;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #E0E0E0;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: none;
            border-radius: 6px;
            background-color: #2C2C2C;
            color: #ffffff;
        }
        input:focus, select:focus {
            outline: none;
            border: 2px solid #6200ea;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #6200ea;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #3700b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Crie sua Conta</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div>
                <label>Eu sou:</label>
                <select name="role" required>
                    <option value="aluno">Aluno</option>
                    <option value="professor">Professor</option>
                </select>
            </div>
            <div>
                <label>Nome Completo:</label>
                <input type="text" name="nome" value="{{ old('nome') }}" required>
            </div>
            <div>
                <label>Email:</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label>Senha:</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <label>Confirmar Senha:</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit">Criar Conta</button>
        </form>
    </div>
</body>
</html>
