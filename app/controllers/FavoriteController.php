<?php
defined('ORION') || exit('Acesso negado.');

class FavoriteController extends Controller
{
    private $movies;
    private $favorites;

    public function __construct()
    {
        Auth::requireAuth();
        $this->movies    = new Movie();
        $this->favorites = new Favorite();
    }

    public function toggle($id)
    {
        $this->guardPost();

        $movie = $this->movies->find($id);
        if (!$movie) {
            flash('danger', 'Filme não encontrado.');
            redirect('browse');
        }

        $nowFav = $this->favorites->toggle(Auth::id(), (int) $movie['id']);
        (new ActivityLog())->log(
            'favorite', Auth::id(), 'movie', (int) $movie['id'],
            ($nowFav ? 'Adicionou à lista: ' : 'Removeu da lista: ') . $movie['title']
        );

        flash('success', $nowFav ? 'Adicionado à Minha Lista.' : 'Removido da Minha Lista.');
        redirect($this->backTo($_POST['back'] ?? '', 'title/' . $movie['id']));
    }

    public function index()
    {
        $this->view('list/index', [
            'title'     => 'Minha Lista',
            'active'    => 'list',
            'siteNav'   => 'app',
            'favorites' => $this->favorites->forUser(Auth::id()),
        ], 'site');
    }

    private function backTo($back, $default)
    {
        $back = trim((string) $back);

        if ($back === '' || strpbrk($back, "\r\n") !== false
            || strpos($back, '://') !== false || strpos($back, '//') === 0) {
            return $default;
        }
        return ltrim($back, '/');
    }
}
