<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login na Plataforma</h2>
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div>
            <label>Senha:</label>
            <input type="password" name="password" required>
        </div>
        @error('email')
            <p style="color: red;">{{ $message }}</p>
        @enderror
        <button type="submit">Entrar</button>
    </form>
    <p>Não tem uma conta? <a href="{{ route('register') }}">Crie uma agora!</a></p>
</body>
</html>
