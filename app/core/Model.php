<?php
defined('ORION') || exit('Acesso negado.');

abstract class Model
{
    protected $db;

    protected $table = '';

    public function __construct()
    {
        $this->db = Database::connection();
    }

    protected function select($sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function selectOne($sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    protected function run($sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function count()
    {
        return (int) $this->selectOne("SELECT COUNT(*) AS c FROM `{$this->table}`")['c'];
    }

    public function find($id)
    {
        return $this->selectOne(
            "SELECT * FROM `{$this->table}` WHERE id = ? LIMIT 1",
            [$id]
        );
    }

    public function delete($id)
    {
        return $this->run("DELETE FROM `{$this->table}` WHERE id = ?", [$id]);
    }

    public function lastId()
    {
        return (int) $this->db->lastInsertId();
    }
}
