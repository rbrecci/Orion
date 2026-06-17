<?php defined('ORION') || exit('Acesso negado.'); ?>

<div class="site-page">
  <div class="page-head">
    <h1><i class="bi bi-bookmark-heart"></i> Minha Lista</h1>
    <a class="btn-ghost" href="<?= url('browse') ?>"><i class="bi bi-grid"></i> Ir ao catálogo</a>
  </div>

  <?php if (empty($favorites)): ?>
    <div class="card-orion"><div class="card-body">
      <div class="empty"><i class="bi bi-bookmark-heart"></i>
        Sua lista está vazia.<br>
        Salve filmes para assistir depois pelo botão <b>+ Minha Lista</b>.
        <div><a class="btn-orion mt-3" href="<?= url('browse') ?>"><i class="bi bi-play-fill"></i> Explorar catálogo</a></div>
      </div>
    </div></div>
  <?php else: ?>
    <div class="poster-grid">
      <?php foreach ($favorites as $m): ?>
        <div class="fav-item">
          <?php $card = $m; require VIEW_PATH . '/partials/poster_card.php'; ?>
          <form method="post" action="<?= url('title/' . (int) $m['id'] . '/favorite') ?>" class="fav-remove">
            <?= csrf_field() ?>
            <input type="hidden" name="back" value="list">
            <button type="submit" class="btn-ghost btn-sm w-100 justify-content-center"
                    data-confirm="Remover “<?= e($m['title']) ?>” da sua lista?">
              <i class="bi bi-trash"></i> Remover
            </button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
