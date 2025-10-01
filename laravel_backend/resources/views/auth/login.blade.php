<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Plataforma</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f1f1f1;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            background: #1e1e2f;
        }
        .login-card .card-header {
            border: none;
            text-align: center;
        }
        .login-card h2 {
            font-weight: bold;
            color: #ffffff;
        }
        label {
            color: #ddd; /* labels mais claros */
        }
        .form-control {
            background-color: #2a2a3d;
            border: none;
            color: #fff;
        }
        .form-control::placeholder {
            color: #bbb;
        }
        .form-control:focus {
            border-color: #2575fc;
            background-color: #2a2a3d;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(37,117,252,.25);
        }
        .input-group-text {
            background-color: #2a2a3d;
            border: none;
            color: #bbb;
            cursor: pointer;
        }
        .btn-custom {
            background: #2575fc;
            border: none;
            color: #fff;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: #1a5ed1;
        }
        /* Ajuste para o texto abaixo do botão */
        .text-muted-dark {
            color: #ccc !important; 
        }
        a {
            color: #66b2ff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .text-danger {
            color: #ff6b6b !important; /* vermelho vivo */
        }
        .alert-success {
            background-color: #1b4332;
            border: none;
            color: #95d5b2;
        }
    </style>
</head>
<body>
    <div class="card login-card p-4">
        <div class="card-header">
            <h2>Login na Plataforma</h2>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required placeholder="Digite seu email">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Digite sua senha">
                        <span class="input-group-text" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-custom w-100">Entrar</button>
            </form>

            <div class="text-center mt-3">
                <p class="text-muted-dark">Não tem uma conta? <a href="{{ route('register') }}">Crie uma agora!</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const icon = document.getElementById("toggleIcon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>
</body>
</html>
