<?php
defined('ORION') || exit('Acesso negado.');

class Movie extends Model
{
    protected $table = 'movies';

    public function allFiltered($search = '', $genreId = '', $available = '')
    {
        $sql = "SELECT m.*,
                       GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') AS genres
                FROM movies m
                LEFT JOIN movie_genres mg ON mg.movie_id = m.id
                LEFT JOIN genres g        ON g.id = mg.genre_id";
        $where  = [];
        $params = [];

        if ($search !== '') {
            $where[]  = "m.title LIKE ?";
            $params[] = "%{$search}%";
        }
        if ($available !== '') {
            $where[]  = "m.available = ?";
            $params[] = (int) $available;
        }
        if ($genreId !== '') {
            $where[]  = "m.id IN (SELECT movie_id FROM movie_genres WHERE genre_id = ?)";
            $params[] = (int) $genreId;
        }

        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " GROUP BY m.id ORDER BY m.created_at DESC";

        return $this->select($sql, $params);
    }

    public function create(array $data)
    {
        $this->run(
            "INSERT INTO movies
                (title, slug, synopsis, director, cast_list, release_year, duration_min,
                 age_rating, poster_url, backdrop_url, trailer_url, base_price,
                 available, featured)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [
                $data['title'], $data['slug'], $data['synopsis'], $data['director'],
                $data['cast_list'], $data['release_year'], $data['duration_min'],
                $data['age_rating'], $data['poster_url'], $data['backdrop_url'],
                $data['trailer_url'], $data['base_price'],
                $data['available'], $data['featured'],
            ]
        );
        $id = $this->lastId();
        $this->syncGenres($id, $data['genres']);
        return $id;
    }

    public function update($id, array $data)
    {
        $this->run(
            "UPDATE movies SET
                title = ?, slug = ?, synopsis = ?, director = ?, cast_list = ?,
                release_year = ?, duration_min = ?, age_rating = ?, poster_url = ?,
                backdrop_url = ?, trailer_url = ?, base_price = ?,
                available = ?, featured = ?
             WHERE id = ?",
            [
                $data['title'], $data['slug'], $data['synopsis'], $data['director'],
                $data['cast_list'], $data['release_year'], $data['duration_min'],
                $data['age_rating'], $data['poster_url'], $data['backdrop_url'],
                $data['trailer_url'], $data['base_price'],
                $data['available'], $data['featured'], $id,
            ]
        );
        $this->syncGenres($id, $data['genres']);
    }

    private function syncGenres($movieId, array $genreIds)
    {
        $this->run("DELETE FROM movie_genres WHERE movie_id = ?", [$movieId]);
        foreach (array_unique($genreIds) as $gid) {
            $gid = (int) $gid;
            if ($gid > 0) {
                $this->run(
                    "INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)",
                    [$movieId, $gid]
                );
            }
        }
    }

    public function makeUniqueSlug($title, $ignoreId = null)
    {
        $base = slugify($title);
        if ($base === '') {
            $base = 'filme';
        }
        $slug = $base;
        $i = 2;
        while (true) {
            $sql    = "SELECT id FROM movies WHERE slug = ?";
            $params = [$slug];
            if ($ignoreId) {
                $sql .= " AND id <> ?";
                $params[] = $ignoreId;
            }
            if ($this->selectOne($sql, $params) === null) {
                return $slug;
            }
            $slug = $base . '-' . $i;
            $i++;
        }
    }

    public function genreIds($movieId)
    {
        $rows = $this->select(
            "SELECT genre_id FROM movie_genres WHERE movie_id = ?",
            [$movieId]
        );
        return array_map(function ($r) { return (int) $r['genre_id']; }, $rows);
    }

    public function hasRentals($movieId)
    {
        return $this->selectOne(
            "SELECT id FROM rentals WHERE movie_id = ? LIMIT 1",
            [$movieId]
        ) !== null;
    }

    public function countAvailable()
    {
        return (int) $this->selectOne(
            "SELECT COUNT(*) AS c FROM movies WHERE available = 1"
        )['c'];
    }

    public function topRented($limit = 5)
    {
        $limit = (int) $limit;
        return $this->select(
            "SELECT m.id, m.title, m.poster_url, COUNT(r.id) AS rentals_count
             FROM movies m
             JOIN rentals r ON r.movie_id = m.id
             GROUP BY m.id
             ORDER BY rentals_count DESC, m.title ASC
             LIMIT {$limit}"
        );
    }

    public function recent($limit = 5)
    {
        $limit = (int) $limit;
        return $this->select(
            "SELECT id, title, created_at FROM movies ORDER BY created_at DESC LIMIT {$limit}"
        );
    }

    public function featuredOne()
    {
        $sql = "SELECT m.*, GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') AS genres
                FROM movies m
                LEFT JOIN movie_genres mg ON mg.movie_id = m.id
                LEFT JOIN genres g        ON g.id = mg.genre_id
                WHERE m.available = 1 AND m.featured = 1
                  AND m.backdrop_url IS NOT NULL AND m.backdrop_url <> ''
                GROUP BY m.id
                ORDER BY m.updated_at DESC LIMIT 1";
        $row = $this->selectOne($sql);
        if ($row) {
            return $row;
        }

        return $this->selectOne(
            "SELECT m.*, GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') AS genres
             FROM movies m
             LEFT JOIN movie_genres mg ON mg.movie_id = m.id
             LEFT JOIN genres g        ON g.id = mg.genre_id
             WHERE m.available = 1
             GROUP BY m.id
             ORDER BY (m.backdrop_url IS NOT NULL AND m.backdrop_url <> '') DESC, m.created_at DESC
             LIMIT 1"
        );
    }

    public function availableByGenre($genreId, $limit = 20)
    {
        $limit = (int) $limit;
        return $this->select(
            "SELECT m.id, m.title, m.slug, m.poster_url, m.release_year, m.age_rating, m.base_price
             FROM movies m
             JOIN movie_genres mg ON mg.movie_id = m.id
             WHERE m.available = 1 AND mg.genre_id = ?
             ORDER BY m.featured DESC, m.title ASC
             LIMIT {$limit}",
            [(int) $genreId]
        );
    }

    public function trendingAvailable($limit = 12)
    {
        $limit = (int) $limit;
        return $this->select(
            "SELECT m.id, m.title, m.slug, m.poster_url, m.release_year, m.age_rating, m.base_price,
                    COUNT(r.id) AS rentals_count
             FROM movies m
             LEFT JOIN rentals r ON r.movie_id = m.id
             WHERE m.available = 1
             GROUP BY m.id
             ORDER BY rentals_count DESC, m.featured DESC, m.created_at DESC
             LIMIT {$limit}"
        );
    }

    public function posterWall($limit = 24)
    {
        $limit = (int) $limit;
        return $this->select(
            "SELECT poster_url FROM movies
             WHERE available = 1 AND poster_url IS NOT NULL AND poster_url <> ''
             ORDER BY featured DESC, created_at DESC
             LIMIT {$limit}"
        );
    }

    public function findWithGenres($id)
    {
        return $this->selectOne(
            "SELECT m.*, GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') AS genres
             FROM movies m
             LEFT JOIN movie_genres mg ON mg.movie_id = m.id
             LEFT JOIN genres g        ON g.id = mg.genre_id
             WHERE m.id = ?
             GROUP BY m.id LIMIT 1",
            [(int) $id]
        );
    }

    public function availableSimple()
    {
        return $this->select(
            "SELECT id, title, base_price FROM movies
             WHERE available = 1 ORDER BY title ASC"
        );
    }

    public function topGenresForUser($userId, $limit = 3)
    {
        $limit = (int) $limit;
        $rows  = $this->select(
            "SELECT mg.genre_id
             FROM movie_genres mg
             WHERE mg.movie_id IN (
                 SELECT movie_id FROM favorites WHERE user_id = ?
                 UNION
                 SELECT movie_id FROM rentals   WHERE user_id = ?
             )
             GROUP BY mg.genre_id
             ORDER BY COUNT(*) DESC, mg.genre_id ASC
             LIMIT {$limit}",
            [(int) $userId, (int) $userId]
        );
        return array_map(function ($r) { return (int) $r['genre_id']; }, $rows);
    }

    public function recommendedForUser(array $genreIds, $userId, $limit = 18)
    {
        $genreIds = array_values(array_unique(array_map('intval', $genreIds)));
        if (!$genreIds) {
            return [];
        }
        $limit    = (int) $limit;
        $minMatch = min(2, count($genreIds));
        $place    = implode(',', array_fill(0, count($genreIds), '?'));

        $params = array_merge(
            $genreIds,
            [(int) $userId, (int) $userId],
            [$minMatch]
        );

        return $this->select(
            "SELECT m.id, m.title, m.slug, m.poster_url, m.release_year, m.age_rating, m.base_price,
                    SUM(mg.genre_id IN ({$place})) AS match_count
             FROM movies m
             JOIN movie_genres mg ON mg.movie_id = m.id
             WHERE m.available = 1
               AND m.id NOT IN (
                   SELECT movie_id FROM favorites WHERE user_id = ?
                   UNION
                   SELECT movie_id FROM rentals   WHERE user_id = ?
               )
             GROUP BY m.id
             HAVING match_count >= ?
             ORDER BY match_count DESC, m.featured DESC, m.title ASC
             LIMIT {$limit}",
            $params
        );
    }

    public function searchAvailable($search = '', $genreId = '', $age = '', $sort = 'recent')
    {
        $sql = "SELECT m.*, GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') AS genres
                FROM movies m
                LEFT JOIN movie_genres mg ON mg.movie_id = m.id
                LEFT JOIN genres g        ON g.id = mg.genre_id";
        $where  = ['m.available = 1'];
        $params = [];

        if ($search !== '') {
            $where[]  = "m.title LIKE ?";
            $params[] = "%{$search}%";
        }
        if ($age !== '') {
            $where[]  = "m.age_rating = ?";
            $params[] = $age;
        }
        if ($genreId !== '') {
            $where[]  = "m.id IN (SELECT movie_id FROM movie_genres WHERE genre_id = ?)";
            $params[] = (int) $genreId;
        }

        $sql .= " WHERE " . implode(' AND ', $where) . " GROUP BY m.id";

        switch ($sort) {
            case 'title': $sql .= " ORDER BY m.title ASC"; break;
            case 'year':  $sql .= " ORDER BY m.release_year DESC, m.title ASC"; break;
            default:      $sql .= " ORDER BY m.created_at DESC"; break;
        }

        return $this->select($sql, $params);
    }
}
