<?php
defined('ORION') || exit('Acesso negado.');
$u       = Auth::user();
$active  = $active ?? '';
$siteNav = $siteNav ?? ($u ? 'app' : 'public');
$isAdmin = Auth::isAdmin();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? APP_NAME) ?> · <?= APP_NAME ?></title>
  <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('img/favicon.png') ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= asset('css/orion.css') ?>" rel="stylesheet">
</head>
<body class="site">

  <header class="site-nav">
    <div class="site-nav-inner">
      <a class="site-brand" href="<?= url($u ? Auth::home() : '') ?>" aria-label="Orion">
        <img class="brand-symbol" src="<?= asset('img/orion-symbol.png') ?>" alt="">
        <span class="logo-word"></span>
      </a>

      <button class="nav-burger" id="navBurger" aria-label="Menu"><i class="bi bi-list"></i></button>

      <nav class="site-menu" id="siteMenu">
        <?php if ($siteNav === 'app'): ?>
          <form class="nav-search" method="get" action="<?= base_path() ?>/index.php" role="search">
            <input type="hidden" name="url" value="search">
            <button type="submit" class="nav-search-btn" aria-label="Buscar"><i class="bi bi-search"></i></button>
            <input type="text" name="q" placeholder="Buscar filmes…" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Buscar">
          </form>

          <a class="nav-item <?= $active === 'browse' ? 'active' : '' ?>" href="<?= url('browse') ?>">Início</a>
          <?php if (!$isAdmin): ?>
            <a class="nav-item <?= $active === 'list' ? 'active' : '' ?>" href="<?= url('list') ?>">Minha Lista</a>
            <a class="nav-item <?= $active === 'rentals' ? 'active' : '' ?>" href="<?= url('rentals') ?>">Meus Aluguéis</a>
          <?php endif; ?>

          <details class="user-menu">
            <summary><i class="bi bi-person-circle"></i> <span class="hide-sm"><?= e($u['username'] ?? '') ?></span></summary>
            <div class="user-menu-pop">
              <?php if ($isAdmin): ?>
                <span class="user-menu-tag"><i class="bi bi-shield-lock-fill"></i> Modo administrador</span>
                <a href="<?= url('admin') ?>"><i class="bi bi-speedometer2"></i> Voltar ao painel</a>
              <?php else: ?>
                <a href="<?= url('account') ?>"><i class="bi bi-person-gear"></i> Minha conta</a>
                <a href="<?= url('pricing') ?>"><i class="bi bi-calculator"></i> Previsão de aluguel</a>
              <?php endif; ?>
              <form method="post" action="<?= url('logout') ?>">
                <?= csrf_field() ?>
                <button type="submit"><i class="bi bi-box-arrow-right"></i> Sair</button>
              </form>
            </div>
          </details>
        <?php else:  ?>
          <a class="nav-item" href="<?= url('login') ?>">Entrar</a>
          <a class="nav-cta" href="<?= url('register') ?>">Cadastre-se</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <?php $flashes = get_flashes(); if ($flashes): ?>
    <div class="site-flash-wrap">
      <?php foreach ($flashes as $f): ?>
        <div class="flash <?= e($f['type']) ?>">
          <i class="bi <?= $f['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?>"></i>
          <span><?= e($f['message']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <main class="site-main"><?= $content ?></main>

  <footer class="site-foot">
    <div class="site-foot-inner">
      <span class="logo-word" role="img" aria-label="Orion"></span>
      <span class="foot-note">&copy; Todos os direitos reservados</span>
    </div>
  </footer>

  <script src="<?= asset('js/site.js') ?>"></script>
</body>
</html>
