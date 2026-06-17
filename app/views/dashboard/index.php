<?php
defined('ORION') || exit('Acesso negado.');

$actionLabels = [
    'login'        => ['bi-box-arrow-in-right', 'entrou no painel'],
    'logout'       => ['bi-box-arrow-right', 'saiu do painel'],
    'login_failed' => ['bi-shield-exclamation', 'tentativa de login'],
    'login_denied' => ['bi-shield-lock', 'acesso negado'],
    'create_user'  => ['bi-person-plus', 'criou usuário'],
    'update_user'  => ['bi-person-gear', 'editou usuário'],
    'delete_user'  => ['bi-person-x', 'excluiu usuário'],
    'create_movie' => ['bi-film', 'cadastrou filme'],
    'update_movie' => ['bi-pencil-square', 'editou filme'],
    'delete_movie' => ['bi-trash', 'excluiu filme'],
];
?>

<div class="page-head">
  <h1><i class="bi bi-grid-1x2-fill"></i> Dashboard</h1>
</div>

<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['Usuários',         $stats['users'],         'bi-people-fill'],
    ['Filmes',           $stats['movies'],        'bi-film'],
    ['Administradores',  $stats['admins'],        'bi-shield-lock-fill'],
    ['Aluguéis ativos',  $stats['activeRentals'], 'bi-bag-check-fill'],
  ];
  foreach ($cards as $c): ?>
    <div class="col-6 col-xl-3">
      <div class="card-orion stat-card h-100">
        <div class="stat-icon"><i class="bi <?= $c[2] ?>"></i></div>
        <div class="stat-label"><?= e($c[0]) ?></div>
        <div class="stat-value"><?= (int) $c[1] ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-3">

  <div class="col-12 col-lg-7">
    <div class="card-orion h-100">
      <div class="card-body">
        <h3 style="font-size:1.1rem"><i class="bi bi-trophy-fill" style="color:var(--warning)"></i> Top filmes alugados</h3>
        <?php if (empty($topMovies)): ?>
          <div class="empty"><i class="bi bi-film"></i> Nenhuma locação registrada ainda.</div>
        <?php else: ?>
          <table class="table-orion mt-2">
            <thead><tr><th>#</th><th>Filme</th><th class="text-end">Aluguéis</th></tr></thead>
            <tbody>
            <?php foreach ($topMovies as $i => $m): ?>
              <tr>
                <td class="orion-logo" style="font-size:1.1rem"><?= $i + 1 ?></td>
                <td class="d-flex align-items-center gap-2">
                  <?php if (!empty($m['poster_url'])): ?>
                    <img class="thumb" src="<?= e(media($m['poster_url'])) ?>" alt="">
                  <?php endif; ?>
                  <span><?= e($m['title']) ?></span>
                </td>
                <td class="text-end"><span class="badge-orion badge-admin"><?= (int) $m['rentals_count'] ?></span></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="card-orion h-100">
      <div class="card-body">
        <h3 style="font-size:1.1rem"><i class="bi bi-activity"></i> Atividade recente</h3>
        <?php if (empty($activity)): ?>
          <div class="empty"><i class="bi bi-clock-history"></i> Sem atividade recente.</div>
        <?php else: ?>
          <ul class="list-unstyled mt-2 mb-0">
            <?php foreach ($activity as $a):
              $meta = $actionLabels[$a['action']] ?? ['bi-dot', $a['action']]; ?>
              <li class="d-flex align-items-start gap-2 py-2" style="border-bottom:1px solid rgba(255,255,255,.05)">
                <i class="bi <?= $meta[0] ?>" style="color:var(--brand-400);font-size:1.1rem"></i>
                <div class="flex-grow-1">
                  <div style="font-size:.9rem">
                    <b><?= e($a['username'] ?? 'sistema') ?></b> <?= e($meta[1]) ?>
                  </div>
                  <?php if (!empty($a['description'])): ?>
                    <div style="color:var(--faint);font-size:.78rem"><?= e($a['description']) ?></div>
                  <?php endif; ?>
                </div>
                <span style="color:var(--faint);font-size:.74rem;white-space:nowrap"><?= dt($a['created_at']) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="card-orion">
      <div class="card-body">
        <h3 style="font-size:1.1rem"><i class="bi bi-stars"></i> Filmes recém-cadastrados</h3>
        <?php if (empty($recentMovies)): ?>
          <div class="empty"><i class="bi bi-film"></i> Nenhum filme cadastrado.</div>
        <?php else: ?>
          <table class="table-orion mt-2">
            <tbody>
            <?php foreach ($recentMovies as $m): ?>
              <tr>
                <td><i class="bi bi-film" style="color:var(--brand-400)"></i> <?= e($m['title']) ?></td>
                <td class="text-end" style="color:var(--faint);font-size:.8rem"><?= dt($m['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="card-orion">
      <div class="card-body">
        <h3 style="font-size:1.1rem"><i class="bi bi-person-plus-fill"></i> Novos usuários</h3>
        <?php if (empty($recentUsers)): ?>
          <div class="empty"><i class="bi bi-people"></i> Nenhum usuário.</div>
        <?php else: ?>
          <table class="table-orion mt-2">
            <tbody>
            <?php foreach ($recentUsers as $usr): ?>
              <tr>
                <td>
                  <?= e($usr['username']) ?>
                  <span class="badge-orion <?= $usr['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?> ms-1"><?= e($usr['role']) ?></span>
                </td>
                <td class="text-end" style="color:var(--faint);font-size:.8rem"><?= dt($usr['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
