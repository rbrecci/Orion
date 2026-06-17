<?php
defined('ORION') || exit('Acesso negado.');

class AccountController extends Controller
{
    private $users;

    public function __construct()
    {
        Auth::requireAuth();
        $this->users = new User();
    }

    public function profile()
    {
        $me = $this->users->find(Auth::id());
        if (!$me) {
            Auth::logout();
            redirect('login');
        }

        $this->view('account/profile', [
            'title'   => 'Minha conta',
            'active'  => 'account',
            'siteNav' => 'app',
            'me'      => $me,
        ], 'site');
        clear_old();
    }

    public function update()
    {
        $this->guardPost();

        $id       = Auth::id();
        $email    = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm  = (string) ($_POST['password_confirm'] ?? '');

        $errors = [];
        if ($email === '') {
            $errors[] = 'O e-mail é obrigatório.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            $errors[] = 'Informe um e-mail válido.';
        } elseif ($this->users->emailExists($email, $id)) {
            $errors[] = 'Este e-mail já está em uso.';
        }

        if ($password !== '') {
            if (mb_strlen($password) < 6) {
                $errors[] = 'A nova senha deve ter no mínimo 6 caracteres.';
            } elseif ($password !== $confirm) {
                $errors[] = 'A confirmação de senha não confere.';
            }
        }

        if ($errors) {
            flash('danger', implode(' ', $errors));
            flash_old($_POST);
            redirect('account');
        }

        $this->users->updateAccount($id, $email, $password !== '' ? $password : null);

        $fresh = $this->users->find($id);
        Auth::login($fresh);
        (new ActivityLog())->log('update_account', $id, 'user', $id, 'Atualizou a própria conta');

        clear_old();
        flash('success', 'Conta atualizada com sucesso.');
        redirect('account');
    }

    public function pricing()
    {
        $this->view('account/pricing', [
            'title'      => 'Previsão de aluguel',
            'active'     => 'pricing',
            'siteNav'    => 'app',
            'movies'    => (new Movie())->availableSimple(),
            'dailyRate' => DAILY_RATE,
        ], 'site');
    }
}
