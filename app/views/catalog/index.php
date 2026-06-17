<?php defined('ORION') || exit('Acesso negado.'); $isAdmin = Auth::isAdmin(); ?>

<?php if (!empty($hero)): $h = $hero; ?>

  <section class="cat-hero" <?= !empty($h['backdrop_url']) ? 'style="--hero-bg:url(\'' . e(media($h['backdrop_url'])) . '\')"' : '' ?>>
    <div class="cat-hero-shade"></div>
    <div class="cat-hero-body">
      <?php if ($h['featured']): ?><span class="hero-flag"><i class="bi bi-star-fill"></i> Destaque</span><?php endif; ?>
      <h1 class="cat-hero-title"><?= e($h['title']) ?></h1>
      <div class="cat-hero-meta">
        <?php if ($h['release_year']): ?><span><?= (int) $h['release_year'] ?></span><?php endif; ?>
        <?php if ($h['duration_min']): ?><span><?= (int) $h['duration_min'] ?> min</span><?php endif; ?>
        <span class="age-badge"><?= $h['age_rating'] === 'L' ? 'Livre' : $h['age_rating'] . '+' ?></span>
        <?php if (!empty($h['genres'])): ?><span class="hero-genres"><?= e($h['genres']) ?></span><?php endif; ?>
      </div>
      <?php if (!empty($h['synopsis'])): ?>
        <p class="cat-hero-syn"><?= e(mb_strimwidth($h['synopsis'], 0, 220, '…')) ?></p>
      <?php endif; ?>
      <div class="cat-hero-cta">
        <a class="btn-orion btn-lg" href="<?= url('title/' . (int) $h['id']) ?>">
          <i class="bi bi-play-fill"></i> Ver e alugar
        </a>
        <?php if (!$isAdmin): ?>
          <form method="post" action="<?= url('title/' . (int) $h['id'] . '/favorite') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="back" value="browse">
            <button type="submit" class="btn-ghost btn-lg">
              <i class="bi <?= in_array((int) $h['id'], $favIds, true) ? 'bi-bookmark-heart-fill' : 'bi-plus-lg' ?>"></i>
              Minha Lista
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<div class="cat-body">
  <?php if (!empty($forYou)): ?>
    <section class="row-section">
      <h2 class="row-h"><i class="bi bi-stars"></i> Para você</h2>
      <div class="poster-strip">
        <?php foreach ($forYou as $m): $card = $m; require VIEW_PATH . '/partials/poster_card.php'; endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!empty($trending)): ?>
    <section class="row-section">
      <h2 class="row-h"><i class="bi bi-fire"></i> Em alta</h2>
      <div class="poster-strip">
        <?php foreach ($trending as $m): $card = $m; require VIEW_PATH . '/partials/poster_card.php'; endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php foreach ($rows as $row): ?>
    <section class="row-section">
      <h2 class="row-h"><?= e($row['genre']['name']) ?></h2>
      <div class="poster-strip">
        <?php foreach ($row['items'] as $m): $card = $m; require VIEW_PATH . '/partials/poster_card.php'; endforeach; ?>
      </div>
    </section>
  <?php endforeach; ?>

  <?php if (empty($rows) && empty($trending)): ?>
    <div class="empty"><i class="bi bi-film"></i> O catálogo ainda não tem filmes disponíveis.</div>
  <?php endif; ?>
</div>
