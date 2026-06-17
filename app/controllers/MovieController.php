<?php
defined('ORION') || exit('Acesso negado.');

class MovieController extends Controller
{
    private $movies;

    private $genres;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->movies = new Movie();
        $this->genres = new Genre();
    }

    public function index()
    {
        $search    = trim($_GET['q'] ?? '');
        $genreId   = $_GET['genre'] ?? '';
        $available = $_GET['available'] ?? '';
        if (!in_array($available, ['0', '1'], true)) {
            $available = '';
        }

        $this->view('movies/index', [
            'title'     => 'Filmes',
            'active'    => 'movies',
            'movies'    => $this->movies->allFiltered($search, $genreId, $available),
            'genres'    => $this->genres->all(),
            'search'    => $search,
            'genreId'   => $genreId,
            'available' => $available,
        ]);
    }

    public function create()
    {
        $this->view('movies/form', [
            'title'        => 'Novo filme',
            'active'       => 'movies',
            'movie'        => null,
            'genres'       => $this->genres->all(),
            'selectedGenres' => [],
        ]);
        clear_old();
    }

    public function store()
    {
        $this->guardPost();
        list($errors, $data) = $this->validate($_POST);

        if ($errors) {
            flash('danger', implode(' ', $errors));
            flash_old($_POST);
            redirect('admin/movies/create');
        }

        $data['slug'] = $this->movies->makeUniqueSlug($data['title']);
        $id = $this->movies->create($data);
        (new ActivityLog())->log('create_movie', Auth::id(), 'movie', $id, 'Criou filme ' . $data['title']);
        clear_old();
        flash('success', 'Filme "' . $data['title'] . '" cadastrado.');
        redirect('admin/movies');
    }

    public function edit($id)
    {
        $movie = $this->movies->find($id);
        if (!$movie) {
            flash('danger', 'Filme não encontrado.');
            redirect('admin/movies');
        }
        $this->view('movies/form', [
            'title'          => 'Editar filme',
            'active'         => 'movies',
            'movie'          => $movie,
            'genres'         => $this->genres->all(),
            'selectedGenres' => $this->movies->genreIds($id),
        ]);
        clear_old();
    }

    public function update($id)
    {
        $this->guardPost();
        $movie = $this->movies->find($id);
        if (!$movie) {
            flash('danger', 'Filme não encontrado.');
            redirect('admin/movies');
        }

        list($errors, $data) = $this->validate($_POST);
        if ($errors) {
            flash('danger', implode(' ', $errors));
            flash_old($_POST);
            redirect('admin/movies/' . $id . '/edit');
        }

        $data['slug'] = $this->movies->makeUniqueSlug($data['title'], (int) $id);
        $this->movies->update($id, $data);
        (new ActivityLog())->log('update_movie', Auth::id(), 'movie', (int) $id, 'Editou filme ' . $data['title']);
        clear_old();
        flash('success', 'Filme atualizado.');
        redirect('admin/movies');
    }

    public function destroy($id)
    {
        $this->guardPost();
        $movie = $this->movies->find($id);
        if (!$movie) {
            flash('danger', 'Filme não encontrado.');
            redirect('admin/movies');
        }

        if ($this->movies->hasRentals($id)) {
            flash('danger', 'Este filme possui locações registradas e não pode ser excluído. '
                . 'Para retirá-lo do catálogo, marque-o como indisponível.');
            redirect('admin/movies');
        }

        $this->movies->delete($id);
        (new ActivityLog())->log('delete_movie', Auth::id(), 'movie', (int) $id, 'Excluiu filme ' . $movie['title']);
        flash('success', 'Filme "' . $movie['title'] . '" excluído.');
        redirect('admin/movies');
    }

    private function validate(array $in)
    {
        $errors = [];

        $title       = trim($in['title'] ?? '');
        $synopsis    = trim($in['synopsis'] ?? '');
        $director    = trim($in['director'] ?? '');
        $castList    = trim($in['cast_list'] ?? '');
        $year        = trim($in['release_year'] ?? '');
        $duration    = trim($in['duration_min'] ?? '');
        $ageRating   = $in['age_rating'] ?? 'L';
        $posterUrl   = trim($in['poster_url'] ?? '');
        $backdropUrl = trim($in['backdrop_url'] ?? '');
        $trailerUrl  = trim($in['trailer_url'] ?? '');
        $basePrice   = trim($in['base_price'] ?? '');
        $genres      = isset($in['genres']) && is_array($in['genres']) ? $in['genres'] : [];

        if ($title === '' || mb_strlen($title) > 200) {
            $errors[] = 'O título é obrigatório (até 200 caracteres).';
        }

        $currentYear = (int) date('Y');
        if ($year !== '') {
            if (!ctype_digit($year) || (int) $year < 1888 || (int) $year > $currentYear + 5) {
                $errors[] = 'Ano de lançamento inválido.';
            }
        }

        if ($duration !== '' && (!ctype_digit($duration) || (int) $duration < 1 || (int) $duration > 1000)) {
            $errors[] = 'Duração inválida (1 a 1000 minutos).';
        }

        if (!in_array($ageRating, ['L', '10', '12', '14', '16', '18'], true)) {
            $ageRating = 'L';
        }

        foreach (['Capa' => $posterUrl, 'Banner' => $backdropUrl, 'Trailer' => $trailerUrl] as $label => $u) {
            if ($u !== '' && (!filter_var($u, FILTER_VALIDATE_URL) || mb_strlen($u) > 500)) {
                $errors[] = "URL de {$label} inválida.";
            }
        }

        if ($basePrice === '' || !is_numeric($basePrice) || (float) $basePrice < 0) {
            $errors[] = 'Informe um valor base válido (R$ ≥ 0).';
        }

        $data = [
            'title'        => $title,
            'synopsis'     => $synopsis !== '' ? $synopsis : null,
            'director'     => $director !== '' ? $director : null,
            'cast_list'    => $castList !== '' ? $castList : null,
            'release_year' => $year !== '' ? (int) $year : null,
            'duration_min' => $duration !== '' ? (int) $duration : null,
            'age_rating'   => $ageRating,
            'poster_url'   => $posterUrl !== '' ? $posterUrl : null,
            'backdrop_url' => $backdropUrl !== '' ? $backdropUrl : null,
            'trailer_url'  => $trailerUrl !== '' ? $trailerUrl : null,
            'base_price'   => $basePrice !== '' ? number_format((float) $basePrice, 2, '.', '') : '0.00',
            'available'    => isset($in['available']) ? 1 : 0,
            'featured'     => isset($in['featured']) ? 1 : 0,
            'genres'       => array_map('intval', $genres),
        ];

        return [$errors, $data];
    }
}
