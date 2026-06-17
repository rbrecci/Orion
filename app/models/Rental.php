<?php
defined('ORION') || exit('Acesso negado.');

class Rental extends Model
{
    protected $table = 'rentals';

    public function activeCount()
    {
        return (int) $this->selectOne(
            "SELECT COUNT(*) AS c FROM rentals WHERE status IN ('active','overdue')"
        )['c'];
    }

    public function totalRevenue()
    {
        return (float) $this->selectOne(
            "SELECT COALESCE(SUM(total_price),0) AS s FROM rentals
             WHERE status <> 'cancelled'"
        )['s'];
    }

    public function markOverdue()
    {
        return $this->run(
            "UPDATE rentals SET status = 'overdue'
             WHERE status = 'active' AND view_mode = 'period'
               AND return_date IS NULL AND due_date < CURDATE()"
        )->rowCount();
    }

    public function create(array $data)
    {
        $days = (int) $data['days'];
        $this->run(
            "INSERT INTO rentals
                (user_id, movie_id, rental_date, days, view_mode, views_count,
                 due_date, unit_price, base_price, total_price, status)
             VALUES (?, ?, CURDATE(), ?, ?, 0,
                     DATE_ADD(CURDATE(), INTERVAL ? DAY), ?, ?, ?, 'active')",
            [
                (int) $data['user_id'],
                (int) $data['movie_id'],
                $days,
                $data['view_mode'],
                $days,
                $data['unit_price'],
                $data['base_price'],
                $data['total_price'],
            ]
        );
        return $this->lastId();
    }

    public function entitlement($userId, $movieId)
    {
        return $this->selectOne(
            "SELECT * FROM rentals
             WHERE user_id = ? AND movie_id = ? AND status = 'active'
               AND ( (view_mode = 'single' AND views_count < 1)
                  OR (view_mode = 'period' AND due_date >= CURDATE()) )
             ORDER BY id DESC LIMIT 1",
            [(int) $userId, (int) $movieId]
        );
    }

    public function incrementViews($rentalId)
    {
        return $this->run(
            "UPDATE rentals SET views_count = views_count + 1 WHERE id = ?",
            [(int) $rentalId]
        );
    }

    public function forUser($userId)
    {
        return $this->select(
            "SELECT r.*, m.title, m.poster_url, m.slug
             FROM rentals r
             JOIN movies m ON m.id = r.movie_id
             WHERE r.user_id = ?
             ORDER BY (r.status = 'active') DESC, r.rental_date DESC, r.id DESC",
            [(int) $userId]
        );
    }

    public function findForUser($id, $userId)
    {
        return $this->selectOne(
            "SELECT * FROM rentals WHERE id = ? AND user_id = ? LIMIT 1",
            [(int) $id, (int) $userId]
        );
    }

    public function returnRental($id)
    {
        return $this->run(
            "UPDATE rentals SET return_date = CURDATE(), status = 'returned'
             WHERE id = ? AND status IN ('active','overdue')",
            [(int) $id]
        );
    }

    public function userHasAny($userId)
    {
        return $this->selectOne(
            "SELECT id FROM rentals WHERE user_id = ? LIMIT 1",
            [(int) $userId]
        ) !== null;
    }

    public function hasRentedBefore($userId, $movieId)
    {
        return $this->selectOne(
            "SELECT id FROM rentals WHERE user_id = ? AND movie_id = ? LIMIT 1",
            [(int) $userId, (int) $movieId]
        ) !== null;
    }
}
