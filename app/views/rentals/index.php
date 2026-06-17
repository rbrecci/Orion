<?php
defined('ORION') || exit('Acesso negado.');
$today = date('Y-m-d');
$statusMap = [
    'active'    => ['Ativo',     'badge-active'],
    'overdue'   => ['Vencido',   'badge-blocked'],
    'returned'  => ['Devolvido', 'badge-soft'],
    'cancelled' => ['Cancelado', 'badge-soft'],
];
?>

<div class="site-page">
  <div class="page-head">
    <h1><i class="bi bi-collection-play"></i> Meus aluguéis</h1>
    <a class="btn-ghost" href="<?= url('browse') ?>"><i class="bi bi-grid"></i> Ir ao catálogo</a>
  </div>

  <?php if (empty($rentals)): ?>
    <div class="card-orion"><div class="card-body">
      <div class="empty"><i class="bi bi-collection-play"></i>
        Você ainda não alugou nenhum filme.<br>
        <a class="btn-orion mt-3" href="<?= url('browse') ?>"><i class="bi bi-play-fill"></i> Explorar catálogo</a>
      </div>
    </div></div>
  <?php else: ?>
    <div class="rental-list">
      <?php foreach ($rentals as $r):
        $isSingle = $r['view_mode'] === 'single';
        $canWatch = $r['status'] === 'active'
            && (($isSingle && (int) $r['views_count'] < 1) || (!$isSingle && $r['due_date'] >= $today));
        $isOpen = in_array($r['status'], ['active', 'overdue'], true);
        list($stLabel, $stClass) = $statusMap[$r['status']] ?? [$r['status'], 'badge-soft'];
      ?>
        <div class="rental-card">
          <a class="rental-thumb" href="<?= url('title/' . (int) $r['movie_id']) ?>">
            <?php if (!empty($r['poster_url'])): ?>
              <img src="<?= e(media($r['poster_url'])) ?>" alt="<?= e($r['title']) ?>" loading="lazy">
            <?php else: ?>
              <div class="poster-fallback"><i class="bi bi-film"></i></div>
            <?php endif; ?>
          </a>

          <div class="rental-main">
            <div class="rental-top">
              <a class="rental-title" href="<?= url('title/' . (int) $r['movie_id']) ?>"><?= e($r['title']) ?></a>
              <span class="badge-orion <?= $stClass ?>"><?= e($stLabel) ?></span>
            </div>
            <div class="rental-info">
              <span><i class="bi <?= $isSingle ? 'bi-1-circle' : 'bi-infinity' ?>"></i>
                <?= $isSingle ? '1 visualização' : 'Ilimitado' ?></span>
              <span><i class="bi bi-calendar-event"></i> Alugado em <?= dt($r['rental_date']) ?></span>
              <?php if (!$isSingle): ?>
                <span><i class="bi bi-hourglass-split"></i> Vence em <?= dt($r['due_date']) ?></span>
              <?php endif; ?>
              <span><i class="bi bi-cash-coin"></i> <?= money($r['total_price']) ?></span>
            </div>
          </div>

          <div class="rental-actions">
            <?php if ($canWatch): ?>
              <a class="btn-orion" href="<?= url('title/' . (int) $r['movie_id'] . '/watch') ?>"><i class="bi bi-play-fill"></i> Assistir</a>
            <?php endif; ?>
            <?php if ($isOpen): ?>
              <form method="post" action="<?= url('rentals/' . (int) $r['id'] . '/return') ?>"
                    data-confirm="Devolver “<?= e($r['title']) ?>”? Você perderá o acesso a este filme.">
                <?= csrf_field() ?>
                <button type="submit" class="btn-ghost"><i class="bi bi-arrow-counterclockwise"></i> Devolver</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
