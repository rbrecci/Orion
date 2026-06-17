<?php
defined('ORION') || exit('Acesso negado.');

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            redirect(Auth::home());
        }
        $this->view('auth/login', ['title' => 'Entrar'], 'auth');
    }

    public function login()
    {
        $this->guardPost();

        $username = trim($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $logs = new ActivityLog();

        if ($username === '' || $password === '') {
            flash('danger', 'Informe usuário e senha.');
            redirect('login');
        }

        $userModel = new User();
        $user = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $logs->log('login_failed', null, 'user', null, 'Tentativa para: ' . $username);
            flash('danger', 'Usuário ou senha inválidos.');
            redirect('login');
        }

        if ($user['status'] !== 'active') {
            flash('danger', 'Esta conta está bloqueada.');
            redirect('login');
        }

        Auth::login($user);
        $logs->log('login', (int) $user['id'], 'user', (int) $user['id'], 'Login (' . $user['role'] . ')');
        flash('success', 'Bem-vindo, ' . $user['username'] . '!');
        redirect(Auth::home());
    }

    public function logout()
    {
        $this->guardPost();
        if (Auth::check()) {
            (new ActivityLog())->log('logout', Auth::id(), 'user', Auth::id(), 'Logout do painel');
        }
        Auth::logout();
        flash('success', 'Sessão encerrada.');
        redirect('login');
    }

    public function notFound()
    {
        $this->view('auth/404', ['title' => 'Não encontrado'], 'auth');
    }
}
