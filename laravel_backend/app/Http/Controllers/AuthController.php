<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Professor;
use App\Models\Aluno;

class AuthController extends Controller
{
    // Exibe o formulário de registo
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Processa o registo
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:professors|unique:alunos',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:professor,aluno',
        ]);

        if ($request->role === 'professor') {
            Professor::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } else {
            Aluno::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        }
        
        return redirect('/login')->with('success', 'Conta criada com sucesso! Por favor, faça o login.');
    }

    // Exibe o formulário de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Processa o login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tenta autenticar como professor
        if (Auth::guard('professor')->attempt($credentials)) {
            $request->session()->regenerate();
            // Redireciona para o painel do professor
            return redirect()->intended(route('professor.dashboard'));
        }

        // Tenta autenticar como aluno
        if (Auth::guard('aluno')->attempt($credentials)) {
            $request->session()->regenerate();
            // Redireciona para o painel do aluno
            return redirect()->intended(route('aluno.dashboard'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registos.',
        ])->onlyInput('email');
    }

    // Processa o logout
    public function logout(Request $request)
    {
        if (Auth::guard('professor')->check()) {
            Auth::guard('professor')->logout();
        }

        if (Auth::guard('aluno')->check()) {
            Auth::guard('aluno')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

