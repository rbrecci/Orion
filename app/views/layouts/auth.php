<?php defined('ORION') || exit('Acesso negado.'); ?>
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= asset('css/orion.css') ?>" rel="stylesheet">
</head>
<body>
  <div class="auth-wrap">
    <a class="auth-back" href="<?= url('') ?>"><i class="bi bi-arrow-left"></i> Voltar ao início</a>
    <?= $content ?>
  </div>
</body>
</html>
