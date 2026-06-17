<?php defined('ORION') || exit('Acesso negado.'); $m = $movie; ?>

<div class="watch-wrap">
  <div class="watch-head">
    <a class="btn-ghost" href="<?= url('title/' . (int) $m['id']) ?>"><i class="bi bi-arrow-left"></i> Voltar ao filme</a>
    <h1 class="watch-title"><?= e($m['title']) ?></h1>
  </div>

  <div class="player-frame">
    <iframe src="<?= e($playerUrl) ?>?autoplay=1&rel=0" title="Reproduzindo <?= e($m['title']) ?>"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
  </div>

  <div class="watch-note">
    <?php if ($isSingle): ?>
      <i class="bi bi-1-circle-fill"></i>
      <span>Esta era a sua <b>visualização única</b>. Para assistir novamente, alugue o filme outra vez
            ou contrate um período de acesso ilimitado.</span>
    <?php else: ?>
      <i class="bi bi-infinity"></i>
      <span>Acesso ilimitado ativo até <b><?= dt($rental['due_date']) ?></b>. Bom filme!</span>
    <?php endif; ?>
  </div>

  <p class="watch-foot">
    <a href="<?= url('rentals') ?>"><i class="bi bi-collection-play"></i> Ver meus aluguéis</a>
  </p>
</div>
