<?php
defined('ORION') || exit('Acesso negado.');

class Favorite extends Model
{
    protected $table = 'favorites';

    public function isFavorite($userId, $movieId)
    {
        return $this->selectOne(
            "SELECT 1 FROM favorites WHERE user_id = ? AND movie_id = ? LIMIT 1",
            [(int) $userId, (int) $movieId]
        ) !== null;
    }

    public function toggle($userId, $movieId)
    {
        if ($this->isFavorite($userId, $movieId)) {
            $this->run(
                "DELETE FROM favorites WHERE user_id = ? AND movie_id = ?",
                [(int) $userId, (int) $movieId]
            );
            return false;
        }
        $this->run(
            "INSERT INTO favorites (user_id, movie_id) VALUES (?, ?)",
            [(int) $userId, (int) $movieId]
        );
        return true;
    }

    public function forUser($userId)
    {
        return $this->select(
            "SELECT m.id, m.title, m.slug, m.poster_url, m.release_year, m.age_rating, m.base_price,
                    f.created_at AS faved_at
             FROM favorites f
             JOIN movies m ON m.id = f.movie_id
             WHERE f.user_id = ?
             ORDER BY f.created_at DESC",
            [(int) $userId]
        );
    }

    public function idsForUser($userId)
    {
        $rows = $this->select(
            "SELECT movie_id FROM favorites WHERE user_id = ?",
            [(int) $userId]
        );
        return array_map(function ($r) { return (int) $r['movie_id']; }, $rows);
    }

    public function countForUser($userId)
    {
        return (int) $this->selectOne(
            "SELECT COUNT(*) AS c FROM favorites WHERE user_id = ?",
            [(int) $userId]
        )['c'];
    }
}
