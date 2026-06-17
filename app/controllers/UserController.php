<?php
defined('ORION') || exit('Acesso negado.');

class UserController extends Controller
{
    private $users;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->users = new User();
    }

    public function index()
    {
        $search = trim($_GET['q'] ?? '');
        $role   = $_GET['role'] ?? '';
        if (!in_array($role, ['admin', 'user'], true)) {
            $role = '';
        }

        $this->view('users/index', [
            'title'   => 'Usuários',
            'active'  => 'users',
            'users'   => $this->users->allFiltered($search, $role),
            'search'  => $search,
            'role'    => $role,
        ]);
    }

    public function create()
    {
        $this->view('users/form', [
            'title'  => 'Novo usuário',
            'active' => 'users',
            'user'   => null,
        ]);
        clear_old();
    }

    public function store()
    {
        $this->guardPost();
        list($errors, $data) = $this->validate($_POST, null);

        if ($errors) {
            flash('danger', implode(' ', $errors));
            flash_old($_POST);
            redirect('admin/users/create');
        }

        $id = $this->users->create($data);
        (new ActivityLog())->log('create_user', Auth::id(), 'user', $id, 'Criou usuário ' . $data['username']);
        clear_old();
        flash('success', 'Usuário "' . $data['username'] . '" criado com sucesso.');
        redirect('admin/users');
    }

    public function edit($id)
    {
        $user = $this->users->find($id);
        if (!$user) {
            flash('danger', 'Usuário não encontrado.');
            redirect('admin/users');
        }
        $this->view('users/form', [
            'title'  => 'Editar usuário',
            'active' => 'users',
            'user'   => $user,
        ]);
        clear_old();
    }

    public function update($id)
    {
        $this->guardPost();
        $user = $this->users->find($id);
        if (!$user) {
            flash('danger', 'Usuário não encontrado.');
            redirect('admin/users');
        }

        list($errors, $data) = $this->validate($_POST, (int) $id);

        $protect = $this->protectAdminChange($user, $data);
        if ($protect) {
            $errors[] = $protect;
        }

        if ($data['role'] === 'admin' && $user['role'] !== 'admin'
            && (new Rental())->userHasAny((int) $id)) {
            $errors[] = 'Este usuário possui aluguéis registrados e não pode ser promovido a administrador.';
        }

        if ($errors) {
            flash('danger', implode(' ', $errors));
            flash_old($_POST);
            redirect('admin/users/' . $id . '/edit');
        }

        $this->users->update($id, $data);
        (new ActivityLog())->log('update_user', Auth::id(), 'user', (int) $id, 'Editou usuário ' . $data['username']);

        if ((int) $id === (int) Auth::id()) {
            $fresh = $this->users->find($id);
            Auth::login($fresh);
        }

        clear_old();
        flash('success', 'Usuário atualizado com sucesso.');
        redirect('admin/users');
    }

    public function destroy($id)
    {
        $this->guardPost();
        $user = $this->users->find($id);
        if (!$user) {
            flash('danger', 'Usuário não encontrado.');
            redirect('admin/users');
        }

        if ((int) $id === (int) Auth::id()) {
            flash('danger', 'Você não pode excluir a própria conta.');
            redirect('admin/users');
        }

        if ($user['role'] === 'admin' && $user['status'] === 'active'
            && $this->users->countActiveAdmins() <= 1) {
            flash('danger', 'Não é possível excluir o último administrador ativo.');
            redirect('admin/users');
        }

        $this->users->delete($id);
        (new ActivityLog())->log('delete_user', Auth::id(), 'user', (int) $id, 'Excluiu usuário ' . $user['username']);
        flash('success', 'Usuário "' . $user['username'] . '" excluído.');
        redirect('admin/users');
    }

    private function validate(array $in, $id)
    {
        $errors = [];

        $username = trim($in['username'] ?? '');
        $email    = trim($in['email'] ?? '');
        $password = (string) ($in['password'] ?? '');
        $confirm  = (string) ($in['password_confirm'] ?? '');
        $role     = $in['role'] ?? '';
        $status   = $in['status'] ?? 'active';

        if ($username === '') {
            $errors[] = 'O username é obrigatório.';
        } elseif (mb_strlen($username) < 3 || mb_strlen($username) > 50) {
            $errors[] = 'O username deve ter de 3 a 50 caracteres.';
        } elseif (!preg_match('/^[A-Za-z0-9_.-]+$/', $username)) {
            $errors[] = 'O username aceita apenas letras, números, ponto, hífen e underline.';
        } elseif ($this->users->usernameExists($username, $id)) {
            $errors[] = 'Este username já está em uso.';
        }

        if ($email === '') {
            $errors[] = 'O e-mail é obrigatório.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            $errors[] = 'Informe um e-mail válido.';
        } elseif ($this->users->emailExists($email, $id)) {
            $errors[] = 'Este e-mail já está em uso.';
        }

        $passwordRequired = ($id === null);
        if ($passwordRequired || $password !== '') {
            if (mb_strlen($password) < 6) {
                $errors[] = 'A senha deve ter no mínimo 6 caracteres.';
            } elseif ($password !== $confirm) {
                $errors[] = 'A confirmação de senha não confere.';
            }
        }

        if (!in_array($role, ['admin', 'user'], true)) {
            $errors[] = 'Selecione um perfil válido (admin ou user).';
        }
        if (!in_array($status, ['active', 'blocked'], true)) {
            $status = 'active';
        }

        $data = [
            'username' => $username,
            'email'    => $email,
            'password' => $password,
            'role'     => $role,
            'status'   => $status,
        ];

        return [$errors, $data];
    }

    private function protectAdminChange(array $current, array $data)
    {
        $self = ((int) $current['id'] === (int) Auth::id());

        if ($self && ($data['role'] !== 'admin' || $data['status'] !== 'active')) {
            return 'Você não pode rebaixar ou bloquear a própria conta.';
        }

        $wasActiveAdmin = ($current['role'] === 'admin' && $current['status'] === 'active');
        $willStop       = ($data['role'] !== 'admin' || $data['status'] !== 'active');

        if ($wasActiveAdmin && $willStop && $this->users->countActiveAdmins() <= 1) {
            return 'O sistema precisa de pelo menos um administrador ativo.';
        }

        return null;
    }
}
