<?php
defined('ORION') || exit('Acesso negado.');

class RegisterController extends Controller
{
    private $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function show()
    {
        if (Auth::check()) {
            redirect(Auth::home());
        }
        $this->view('auth/register', ['title' => 'Cadastre-se'], 'auth');
        clear_old();
    }

    public function store()
    {
        $this->guardPost();

        if (trim($_POST['website'] ?? '') !== '') {
            flash('danger', 'Não foi possível concluir o cadastro.');
            redirect('register');
        }

        list($errors, $data) = $this->validate($_POST);

        if ($errors) {
            flash('danger', implode(' ', $errors));
            flash_old($_POST);
            redirect('register');
        }

        $id = $this->users->create([
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'role'     => 'user',
            'status'   => 'active',
        ]);

        $fresh = $this->users->find($id);
        Auth::login($fresh);
        (new ActivityLog())->log('register', $id, 'user', $id, 'Novo cadastro: ' . $data['username']);

        clear_old();
        flash('success', 'Conta criada com sucesso. Bem-vindo(a) à Orion, ' . $data['username'] . '!');
        redirect('browse');
    }

    private function validate(array $in)
    {
        $errors = [];

        $username = trim($in['username'] ?? '');
        $email    = trim($in['email'] ?? '');
        $password = (string) ($in['password'] ?? '');
        $confirm  = (string) ($in['password_confirm'] ?? '');

        if ($username === '') {
            $errors[] = 'O username é obrigatório.';
        } elseif (mb_strlen($username) < 3 || mb_strlen($username) > 50) {
            $errors[] = 'O username deve ter de 3 a 50 caracteres.';
        } elseif (!preg_match('/^[A-Za-z0-9_.-]+$/', $username)) {
            $errors[] = 'O username aceita apenas letras, números, ponto, hífen e underline.';
        } elseif ($this->users->usernameExists($username)) {
            $errors[] = 'Este username já está em uso.';
        }

        if ($email === '') {
            $errors[] = 'O e-mail é obrigatório.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            $errors[] = 'Informe um e-mail válido.';
        } elseif ($this->users->emailExists($email)) {
            $errors[] = 'Este e-mail já está em uso.';
        }

        if (mb_strlen($password) < 6) {
            $errors[] = 'A senha deve ter no mínimo 6 caracteres.';
        } elseif ($password !== $confirm) {
            $errors[] = 'A confirmação de senha não confere.';
        }

        return [$errors, ['username' => $username, 'email' => $email, 'password' => $password]];
    }
}
