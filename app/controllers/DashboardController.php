<?php
defined('ORION') || exit('Acesso negado.');

class DashboardController extends Controller
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    public function index()
    {
        $users   = new User();
        $movies  = new Movie();
        $rentals = new Rental();
        $logs    = new ActivityLog();

        $rentals->markOverdue();

        $stats = [
            'users'         => $users->count(),
            'movies'        => $movies->count(),
            'admins'        => $users->countByRole('admin'),
            'activeRentals' => $rentals->activeCount(),
        ];

        $this->view('dashboard/index', [
            'title'        => 'Dashboard',
            'active'       => 'dashboard',
            'stats'        => $stats,
            'topMovies'    => $movies->topRented(5),
            'recentUsers'  => $users->recent(5),
            'recentMovies' => $movies->recent(5),
            'activity'     => $logs->recent(8),
        ]);
    }
}
