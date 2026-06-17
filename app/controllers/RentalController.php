<?php
defined('ORION') || exit('Acesso negado.');

class RentalController extends Controller
{
    const MAX_DAYS = 30;

    private $movies;
    private $rentals;

    public function __construct()
    {
        Auth::requireAuth();
        $this->movies  = new Movie();
        $this->rentals = new Rental();
    }

    public function store($id)
    {
        $this->guardPost();

        if (Auth::isAdmin()) {
            flash('danger', 'Contas administrativas não alugam filmes.');
            redirect('title/' . (int) $id);
        }

        $movie = $this->movies->find($id);
        if (!$movie || (int) $movie['available'] !== 1) {
            flash('danger', 'Filme indisponível para locação.');
            redirect('browse');
        }

        if ($this->rentals->entitlement(Auth::id(), $movie['id'])) {
            flash('success', 'Você já tem acesso a este filme. Boa sessão!');
            redirect('title/' . $movie['id']);
        }

        $days = $_POST['days'] ?? '0';
        if (!ctype_digit((string) $days)) {
            flash('danger', 'Quantidade de dias inválida.');
            redirect('title/' . $movie['id']);
        }
        $days = (int) $days;
        if ($days > self::MAX_DAYS) {
            $days = self::MAX_DAYS;
        }

        $rate      = (float) DAILY_RATE;
        $baseFull  = (float) $movie['base_price'];
        $returning = $this->rentals->hasRentedBefore(Auth::id(), (int) $movie['id']);
        $base      = $returning ? round($baseFull * (1 - LOYALTY_DISCOUNT), 2) : $baseFull;
        $total     = $base + ($rate * $days);

        $rentalId = $this->rentals->create([
            'user_id'     => Auth::id(),
            'movie_id'    => (int) $movie['id'],
            'days'        => $days,
            'view_mode'   => $days === 0 ? 'single' : 'period',
            'unit_price'  => number_format($rate, 2, '.', ''),
            'base_price'  => number_format($base, 2, '.', ''),
            'total_price' => number_format($total, 2, '.', ''),
        ]);

        (new ActivityLog())->log(
            'rent', Auth::id(), 'movie', (int) $movie['id'],
            'Alugou ' . $movie['title'] . ' (' . ($days === 0 ? '1 visualização' : $days . ' dia(s)') . ')'
        );

        flash('success', 'Aluguel confirmado! Total de ' . money($total) . '. Bom filme!');
        redirect('title/' . $movie['id']);
    }

    public function index()
    {
        $this->rentals->markOverdue();

        $this->view('rentals/index', [
            'title'   => 'Meus aluguéis',
            'active'  => 'rentals',
            'siteNav' => 'app',
            'rentals' => $this->rentals->forUser(Auth::id()),
        ], 'site');
    }

    public function returnIt($id)
    {
        $this->guardPost();

        $rental = $this->rentals->findForUser($id, Auth::id());
        if (!$rental) {
            flash('danger', 'Locação não encontrada.');
            redirect('rentals');
        }
        if (!in_array($rental['status'], ['active', 'overdue'], true)) {
            flash('danger', 'Esta locação já foi encerrada.');
            redirect('rentals');
        }

        $this->rentals->returnRental((int) $rental['id']);
        (new ActivityLog())->log('return', Auth::id(), 'movie', (int) $rental['movie_id'], 'Devolveu locação #' . $rental['id']);

        flash('success', 'Filme devolvido. Obrigado!');
        redirect('rentals');
    }
}
