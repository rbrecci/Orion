<?php
defined('ORION') || exit('Acesso negado.');

$cardFav = isset($favIds) && in_array((int) $card['id'], $favIds, true);
$ageLbl  = ($card['age_rating'] ?? 'L') === 'L' ? 'L' : ($card['age_rating'] . '+');
?>
<a class="poster-card" href="<?= url('title/' . (int) $card['id']) ?>" title="<?= e($card['title']) ?>">
  <div class="poster-img">
    <?php if (!empty($card['poster_url'])): ?>
      <img src="<?= e(media($card['poster_url'])) ?>" alt="<?= e($card['title']) ?>" loading="lazy">
    <?php else: ?>
      <div class="poster-fallback"><i class="bi bi-film"></i></div>
    <?php endif; ?>
    <?php if ($cardFav): ?>
      <span class="fav-flag" title="Na sua lista"><i class="bi bi-bookmark-heart-fill"></i></span>
    <?php endif; ?>
    <div class="poster-hover">
      <div class="ph-title"><?= e($card['title']) ?></div>
      <div class="ph-meta">
        <?= !empty($card['release_year']) ? (int) $card['release_year'] : '' ?>
        <span class="ph-age"><?= e($ageLbl) ?></span>
      </div>
    </div>
  </div>
</a>
