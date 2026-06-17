<?php defined('ORION') || exit('Acesso negado.'); ?>

<section class="hero">
  <div class="poster-wall" aria-hidden="true">
    <?php if (!empty($posterWall)):

      $passes = (int) ceil(60 / max(1, count($posterWall)));
      for ($pass = 0; $pass < $passes; $pass++): ?>
        <?php foreach ($posterWall as $p): if (empty($p['poster_url'])) continue; ?>
          <div class="pw-cell"><img src="<?= e(media($p['poster_url'])) ?>" alt="" loading="lazy"></div>
        <?php endforeach; ?>
      <?php endfor; ?>
    <?php endif; ?>
  </div>
  <div class="hero-overlay"></div>

  <div class="hero-content">
    <div class="hero-logo" role="img" aria-label="Orion">
      <img class="hero-symbol" src="<?= asset('img/orion-symbol.png') ?>" alt="">
      <span class="logo-word hero-word"></span>
    </div>

    <h1 class="hero-tagline">Seu universo de filmes, sob demanda.</h1>
    <p class="hero-sub">Alugue, assista quantas vezes quiser e explore um catálogo
       que vai do clássico ao blockbuster, tudo num só lugar.</p>

    <div class="hero-cta">
      <a class="btn-orion btn-lg" href="<?= url('register') ?>"><i class="bi bi-rocket-takeoff"></i> Cadastre-se</a>
      <a class="btn-ghost btn-lg" href="<?= url('login') ?>"><i class="bi bi-box-arrow-in-right"></i> Entrar</a>
    </div>
  </div>
</section>

<?php if (!empty($trending)): ?>
<section class="land-section">
  <h2 class="land-h">Em alta na Orion</h2>
  <div class="poster-strip">
    <?php foreach ($trending as $m): ?>
      <div class="poster-card poster-card--static" title="<?= e($m['title']) ?>">
        <div class="poster-img">
          <?php if (!empty($m['poster_url'])): ?>
            <img src="<?= e(media($m['poster_url'])) ?>" alt="<?= e($m['title']) ?>" loading="lazy">
          <?php else: ?>
            <div class="poster-fallback"><i class="bi bi-film"></i></div>
          <?php endif; ?>
        </div>
        <div class="poster-meta"><?= e($m['title']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <p class="land-cta-line">
    <a class="btn-orion" href="<?= url('register') ?>">Criar conta para assistir</a>
  </p>
</section>
<?php endif; ?>

<section class="land-section how">
  <h2 class="land-h">Como funciona</h2>
  <div class="how-grid">
    <div class="how-card">
      <div class="how-ico"><i class="bi bi-collection-play"></i></div>
      <h3>Alugue</h3>
      <p>Escolha 1 visualização avulsa ou um período com acesso ilimitado.</p>
    </div>
    <div class="how-card">
      <div class="how-ico"><i class="bi bi-play-circle"></i></div>
      <h3>Assista</h3>
      <p>Liberação imediata no player assim que o aluguel é confirmado.</p>
    </div>
    <div class="how-card">
      <div class="how-ico"><i class="bi bi-arrow-counterclockwise"></i></div>
      <h3>Devolva</h3>
      <p>Acompanhe seus aluguéis e devolva quando quiser, sem complicação.</p>
    </div>
  </div>
</section>
