<?php
defined('ORION') || exit('Acesso negado.');

class TitleController extends Controller
{
    private $movies;
    private $rentals;

    public function __construct()
    {
        Auth::requireAuth();
        $this->movies  = new Movie();
        $this->rentals = new Rental();
    }

    public function show($id)
    {
        $movie = $this->loadVisible($id);

        $entitlement = $this->rentals->entitlement(Auth::id(), $movie['id']);

        $returning = !Auth::isAdmin() && $this->rentals->hasRentedBefore(Auth::id(), $movie['id']);
        $baseFull  = (float) $movie['base_price'];
        $baseEff   = $returning ? round($baseFull * (1 - LOYALTY_DISCOUNT), 2) : $baseFull;

        $this->view('title/show', [
            'title'       => $movie['title'],
            'active'      => 'browse',
            'siteNav'     => 'app',
            'movie'       => $movie,
            'genreList'   => $movie['genres'] ? explode(', ', $movie['genres']) : [],
            'isFavorite'  => (new Favorite())->isFavorite(Auth::id(), $movie['id']),
            'entitlement' => $entitlement,
            'canRent'     => !Auth::isAdmin(),
            'dailyRate'   => DAILY_RATE,
            'baseFull'    => $baseFull,
            'baseEff'     => $baseEff,
            'returning'   => $returning,
            'trailerEmbed'=> $this->embedUrl($movie['trailer_url'] ?? ''),
        ], 'site');
    }

    public function watch($id)
    {
        $movie = $this->loadVisible($id);

        $rental = $this->rentals->entitlement(Auth::id(), $movie['id']);
        if (!$rental) {
            flash('danger', 'Você precisa alugar este filme para assisti-lo.');
            redirect('title/' . $movie['id']);
        }

        $this->rentals->incrementViews($rental['id']);
        (new ActivityLog())->log('watch', Auth::id(), 'movie', (int) $movie['id'], 'Assistiu ' . $movie['title']);

        $isSingle = ($rental['view_mode'] === 'single');

        $this->view('title/watch', [
            'title'    => 'Assistindo: ' . $movie['title'],
            'siteNav'  => 'app',
            'movie'    => $movie,
            'rental'   => $rental,
            'isSingle' => $isSingle,
            'playerUrl'=> PLAYER_PLACEHOLDER_URL,
        ], 'site');
    }

    private function loadVisible($id)
    {
        $movie = $this->movies->findWithGenres($id);
        if (!$movie || ((int) $movie['available'] !== 1 && !Auth::isAdmin())) {
            http_response_code(404);
            flash('danger', 'Filme não encontrado no catálogo.');
            redirect('browse');
        }
        return $movie;
    }

    private function embedUrl($url)
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        if (preg_match('~[?&]v=([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('~youtube\.com/embed/[A-Za-z0-9_-]{6,}~', $url)) {
            return $url;
        }
        return '';
    }
}
