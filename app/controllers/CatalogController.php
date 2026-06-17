<?php
defined('ORION') || exit('Acesso negado.');

class CatalogController extends Controller
{
    private $movies;
    private $genres;

    public function __construct()
    {
        Auth::requireAuth();
        $this->movies = new Movie();
        $this->genres = new Genre();
    }

    public function index()
    {
        $uid    = Auth::id();
        $favIds = (new Favorite())->idsForUser($uid);

        $forYou = [];
        if (!Auth::isAdmin()) {
            $top = $this->movies->topGenresForUser($uid, 3);
            if ($top) {
                $forYou = $this->movies->recommendedForUser($top, $uid, 18);
            }
        }

        $rows = [];
        foreach ($this->genres->all() as $g) {
            $items = $this->movies->availableByGenre((int) $g['id'], 20);
            if ($items) {
                $rows[] = ['genre' => $g, 'items' => $items];
            }
        }

        $this->view('catalog/index', [
            'title'    => 'Catálogo',
            'active'   => 'browse',
            'siteNav'  => 'app',
            'favIds'   => $favIds,
            'forYou'   => $forYou,
            'hero'     => $this->movies->featuredOne(),
            'trending' => $this->movies->trendingAvailable(12),
            'rows'     => $rows,
        ], 'site');
    }

    public function search()
    {
        $q       = trim($_GET['q'] ?? '');
        $genreId = $_GET['genre'] ?? '';
        if ($genreId !== '' && !ctype_digit((string) $genreId)) {
            $genreId = '';
        }
        $age = $_GET['age'] ?? '';
        if (!in_array($age, ['L', '10', '12', '14', '16', '18'], true)) {
            $age = '';
        }
        $sort = $_GET['sort'] ?? '';
        if (!in_array($sort, ['recent', 'title', 'year'], true)) {
            $sort = 'recent';
        }

        $hasQuery = ($q !== '' || $genreId !== '' || $age !== '');

        $this->view('catalog/search', [
            'title'    => 'Buscar',
            'active'   => 'search',
            'siteNav'  => 'app',
            'genres'   => $this->genres->all(),
            'favIds'   => (new Favorite())->idsForUser(Auth::id()),
            'q'        => $q,
            'genreId'  => $genreId,
            'age'      => $age,
            'sort'     => $sort,
            'hasQuery' => $hasQuery,
            'results'  => $this->movies->searchAvailable($q, $genreId, $age, $sort),
        ], 'site');
    }
}
