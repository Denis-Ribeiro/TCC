<!DOCTYPE html>
<html>
<head>
    <title>Criar Conta</title>
</head>
<body>
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
</body>
</html>
