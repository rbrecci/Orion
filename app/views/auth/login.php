<?php defined('ORION') || exit('Acesso negado.'); ?>
<div class="auth-card">
  <div class="logo" role="img" aria-label="Orion">
    <img class="auth-symbol" src="<?= asset('img/orion-symbol.png') ?>" alt="">
    <span class="logo-word auth-word"></span>
  </div>

  <?php require VIEW_PATH . '/partials/flash.php'; ?>

  <form method="post" action="<?= url('login') ?>" autocomplete="off">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label class="form-label" for="username">Usuário</label>
      <input type="text" id="username" name="username" class="form-control-orion"
             placeholder="seu_usuario" value="<?= e(old('username')) ?>" autofocus required>
    </div>

    <div class="mb-3">
      <label class="form-label" for="password">Senha</label>
      <input type="password" id="password" name="password" class="form-control-orion"
             placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn-orion w-100 justify-content-center mt-2">
      <i class="bi bi-box-arrow-in-right"></i> Entrar
    </button>
  </form>

  <p class="auth-alt">Ainda não tem conta? <a href="<?= url('register') ?>">Cadastre-se</a></p>
</div>
<?php clear_old(); ?>
