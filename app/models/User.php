<?php
defined('ORION') || exit('Acesso negado.');

class User extends Model
{
    protected $table = 'users';

    public function findByUsername($username)
    {
        return $this->selectOne(
            "SELECT * FROM users WHERE username = ? LIMIT 1",
            [$username]
        );
    }

    public function allFiltered($search = '', $role = '')
    {
        $sql    = "SELECT id, username, email, role, status, created_at FROM users WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (username LIKE ? OR email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($role !== '') {
            $sql .= " AND role = ?";
            $params[] = $role;
        }
        $sql .= " ORDER BY created_at DESC";

        return $this->select($sql, $params);
    }

    public function usernameExists($username, $ignoreId = null)
    {
        $sql    = "SELECT id FROM users WHERE username = ?";
        $params = [$username];
        if ($ignoreId) {
            $sql .= " AND id <> ?";
            $params[] = $ignoreId;
        }
        return $this->selectOne($sql, $params) !== null;
    }

    public function emailExists($email, $ignoreId = null)
    {
        $sql    = "SELECT id FROM users WHERE email = ?";
        $params = [$email];
        if ($ignoreId) {
            $sql .= " AND id <> ?";
            $params[] = $ignoreId;
        }
        return $this->selectOne($sql, $params) !== null;
    }

    public function create(array $data)
    {
        $this->run(
            "INSERT INTO users (username, email, password_hash, role, status)
             VALUES (?, ?, ?, ?, ?)",
            [
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['role'],
                $data['status'],
            ]
        );
        return $this->lastId();
    }

    public function update($id, array $data)
    {
        $sql    = "UPDATE users SET username = ?, email = ?, role = ?, status = ?";
        $params = [$data['username'], $data['email'], $data['role'], $data['status']];

        if (!empty($data['password'])) {
            $sql .= ", password_hash = ?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $sql .= " WHERE id = ?";
        $params[] = $id;

        return $this->run($sql, $params);
    }

    public function updateAccount($id, $email, $password = null)
    {
        $sql    = "UPDATE users SET email = ?";
        $params = [$email];
        if ($password !== null && $password !== '') {
            $sql .= ", password_hash = ?";
            $params[] = password_hash($password, PASSWORD_BCRYPT);
        }
        $sql .= " WHERE id = ?";
        $params[] = (int) $id;

        return $this->run($sql, $params);
    }

    public function countActiveAdmins()
    {
        return (int) $this->selectOne(
            "SELECT COUNT(*) AS c FROM users WHERE role = 'admin' AND status = 'active'"
        )['c'];
    }

    public function countByRole($role)
    {
        return (int) $this->selectOne(
            "SELECT COUNT(*) AS c FROM users WHERE role = ?",
            [$role]
        )['c'];
    }

    public function recent($limit = 5)
    {
        $limit = (int) $limit;
        return $this->select(
            "SELECT id, username, email, role, created_at
             FROM users ORDER BY created_at DESC LIMIT {$limit}"
        );
    }
}
