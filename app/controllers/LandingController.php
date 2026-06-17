<?php
defined('ORION') || exit('Acesso negado.');

class LandingController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            redirect(Auth::home());
        }

        $movies = new Movie();

        $this->view('landing/index', [
            'title'      => 'Streaming & locação de filmes',
            'posterWall' => $movies->posterWall(24),
            'trending'   => $movies->trendingAvailable(6),
            'siteNav'    => 'public',
        ], 'site');
    }
}
