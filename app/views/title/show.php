<?php
defined('ORION') || exit('Acesso negado.');
$m       = $movie;
$rate    = (float) $dailyRate;
$base    = (float) $baseEff;
$ageLbl  = $m['age_rating'] === 'L' ? 'Livre' : $m['age_rating'] . '+';
$entitled = !empty($entitlement);
?>

<section class="title-hero" <?= !empty($m['backdrop_url']) ? 'style="--hero-bg:url(\'' . e(media($m['backdrop_url'])) . '\')"' : '' ?>>
  <div class="title-hero-shade"></div>
  <a class="title-back" href="<?= url('browse') ?>"><i class="bi bi-arrow-left"></i> Catálogo</a>
</section>

<div class="title-wrap">
  <div class="title-poster">
    <?php if (!empty($m['poster_url'])): ?>
      <img src="<?= e(media($m['poster_url'])) ?>" alt="<?= e($m['title']) ?>">
    <?php else: ?>
      <div class="poster-fallback lg"><i class="bi bi-film"></i></div>
    <?php endif; ?>
  </div>

  <div class="title-info">
    <h1 class="title-name"><?= e($m['title']) ?></h1>

    <div class="title-meta">
      <?php if ($m['release_year']): ?><span><?= (int) $m['release_year'] ?></span><?php endif; ?>
      <?php if ($m['duration_min']): ?><span><i class="bi bi-clock"></i> <?= (int) $m['duration_min'] ?> min</span><?php endif; ?>
      <span class="age-badge"><?= e($ageLbl) ?></span>
      <?php if ((int) $m['available'] !== 1): ?><span class="badge-orion badge-blocked">Oculto no catálogo</span><?php endif; ?>
    </div>

    <?php if (!empty($genreList)): ?>
      <div class="title-genres">
        <?php foreach ($genreList as $gn): ?><span class="genre-chip"><?= e($gn) ?></span><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($m['synopsis'])): ?><p class="title-syn"><?= nl2br(e($m['synopsis'])) ?></p><?php endif; ?>

    <dl class="title-credits">
      <?php if (!empty($m['director'])): ?>
        <div><dt>Direção</dt><dd><?= e($m['director']) ?></dd></div>
      <?php endif; ?>
      <?php if (!empty($m['cast_list'])): ?>
        <div><dt>Elenco</dt><dd><?= e($m['cast_list']) ?></dd></div>
      <?php endif; ?>
    </dl>

    <div class="title-actions">
      <?php if ($entitled): ?>
        <a class="btn-orion btn-lg" href="<?= url('title/' . (int) $m['id'] . '/watch') ?>">
          <i class="bi bi-play-circle-fill"></i> Assistir agora
        </a>
        <span class="access-note">
          <i class="bi bi-unlock-fill"></i>
          <?php if ($entitlement['view_mode'] === 'single'): ?>
            Acesso liberado: <b>1 visualização</b>.
          <?php else: ?>
            Acesso ilimitado até <b><?= dt($entitlement['due_date']) ?></b>.
          <?php endif; ?>
        </span>
      <?php elseif ($canRent): ?>
        <div class="rent-box" id="rentBox" data-base="<?= number_format($base, 2, '.', '') ?>" data-rate="<?= number_format($rate, 2, '.', '') ?>">
          <?php if (!empty($returning)): ?>
            <p class="rent-loyalty">
              <i class="bi bi-patch-check-fill"></i> Cliente fiel:
              base <s><?= money($baseFull) ?></s> <b><?= money($base) ?></b>
              <span class="loyalty-tag">−30%</span>
            </p>
          <?php endif; ?>
          <form method="post" action="<?= url('title/' . (int) $m['id'] . '/rent') ?>" class="rent-form">
            <?= csrf_field() ?>
            <div class="rent-row">
              <label class="form-label" for="rentDays">Dias de acesso</label>
              <input type="number" id="rentDays" name="days" class="form-control-orion"
                     min="0" max="30" step="1" value="3" style="max-width:120px">
            </div>
            <p class="rent-mode" id="rentMode"></p>
            <div class="rent-price-line">
              <span class="rent-total" id="rentPrice"><?= money($base + $rate * 3) ?></span>
              <span class="rent-formula" id="rentFormula"></span>
            </div>
            <button type="submit" class="btn-orion btn-lg w-100 justify-content-center">
              <i class="bi bi-bag-check"></i> Alugar por <span id="rentBtnLabel"><?= money($base + $rate * 3) ?></span>
            </button>
          </form>
        </div>
      <?php else:  ?>
        <span class="access-note">
          <i class="bi bi-shield-lock-fill"></i> Modo administrador: visualização do catálogo (sem locação).
        </span>
      <?php endif; ?>

      <?php if ($canRent): ?>
        <form method="post" action="<?= url('title/' . (int) $m['id'] . '/favorite') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="back" value="title/<?= (int) $m['id'] ?>">
          <button type="submit" class="btn-ghost btn-lg">
            <i class="bi <?= $isFavorite ? 'bi-bookmark-heart-fill' : 'bi-bookmark-plus' ?>"></i>
            <?= $isFavorite ? 'Na Minha Lista' : '+ Minha Lista' ?>
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if (!empty($trailerEmbed)): ?>
  <section class="title-trailer">
    <h2 class="row-h"><i class="bi bi-film"></i> Trailer</h2>
    <div class="video-frame">
      <iframe src="<?= e($trailerEmbed) ?>" title="Trailer de <?= e($m['title']) ?>"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen loading="lazy"></iframe>
    </div>
  </section>
<?php endif; ?>
