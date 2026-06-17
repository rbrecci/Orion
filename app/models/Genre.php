<?php
defined('ORION') || exit('Acesso negado.');

class Genre extends Model
{
    protected $table = 'genres';

    public function all()
    {
        return $this->select("SELECT id, name, slug FROM genres ORDER BY name ASC");
    }
}
