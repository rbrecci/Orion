<?php defined('ORION') || exit('Acesso negado.'); $u = Auth::user(); $active = $active ?? ''; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? '') ?> · <?= APP_NAME ?></title>
  <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('img/favicon.png') ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= asset('css/orion.css') ?>" rel="stylesheet">
</head>
<body>
<div class="layout">

  <aside class="sidebar" id="sidebar">
    <div class="brand" role="img" aria-label="Orion">
      <img class="brand-symbol" src="<?= asset('img/orion-symbol.png') ?>" alt="">
      <span class="logo-word"></span>
    </div>

    <nav>
      <a class="nav-link-orion <?= $active === 'dashboard' ? 'active' : '' ?>" href="<?= url('admin') ?>">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
      </a>
      <a class="nav-link-orion <?= $active === 'users' ? 'active' : '' ?>" href="<?= url('admin/users') ?>">
        <i class="bi bi-people-fill"></i> Usuários
      </a>
      <a class="nav-link-orion <?= $active === 'movies' ? 'active' : '' ?>" href="<?= url('admin/movies') ?>">
        <i class="bi bi-film"></i> Filmes
      </a>

      <div class="nav-sep"></div>
      <a class="nav-link-orion <?= $active === 'browse' ? 'active' : '' ?>" href="<?= url('browse') ?>">
        <i class="bi bi-collection-play"></i> Catálogo
      </a>
    </nav>

    <div class="spacer"></div>

    <form method="post" action="<?= url('logout') ?>">
      <?= csrf_field() ?>
      <button type="submit" class="nav-link-orion" style="width:100%;border:none;background:none;cursor:pointer">
        <i class="bi bi-box-arrow-right"></i> Sair
      </button>
    </form>
  </aside>
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <div class="content">
    <header class="topbar">
      <button class="btn-icon sidebar-toggle" id="sidebarToggle" aria-label="Menu">
        <i class="bi bi-list"></i>
      </button>
      <span class="topbar-brand hide-sm" role="img" aria-label="Orion · Admin">
        <span class="logo-word"></span>
        <span class="topbar-admin">· ADMIN</span>
      </span>
      <span class="welcome">
        <i class="bi bi-person-circle"></i>
        Bem-vindo, <b><?= e($u['username'] ?? '') ?></b>
      </span>
    </header>

    <main class="page">
      <?php require VIEW_PATH . '/partials/flash.php'; ?>
      <?= $content ?>
    </main>
  </div>
</div>

<script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
