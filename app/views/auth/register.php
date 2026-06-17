<?php defined('ORION') || exit('Acesso negado.'); ?>
<div class="auth-card">
  <div class="logo" role="img" aria-label="Orion">
    <img class="auth-symbol" src="<?= asset('img/orion-symbol.png') ?>" alt="">
    <span class="logo-word auth-word"></span>
  </div>
  <p class="subtitle">Crie sua conta gratuita</p>

  <?php require VIEW_PATH . '/partials/flash.php'; ?>

  <form method="post" action="<?= url('register') ?>" autocomplete="off">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label class="form-label" for="username">Usuário</label>
      <input type="text" id="username" name="username" class="form-control-orion"
             placeholder="seu_usuario" value="<?= e(old('username')) ?>"
             maxlength="50" autofocus required>
      <div class="form-hint">3 a 50 caracteres. Diferencia maiúsculas de minúsculas.</div>
    </div>

    <div class="mb-3">
      <label class="form-label" for="email">E-mail</label>
      <input type="email" id="email" name="email" class="form-control-orion"
             placeholder="voce@email.com" value="<?= e(old('email')) ?>"
             maxlength="150" required>
    </div>

    <div class="mb-3">
      <label class="form-label" for="password">Senha</label>
      <input type="password" id="password" name="password" class="form-control-orion"
             placeholder="••••••••" minlength="6" required>
      <div class="form-hint">Mínimo de 6 caracteres.</div>
    </div>

    <div class="mb-3">
      <label class="form-label" for="password_confirm">Confirmar senha</label>
      <input type="password" id="password_confirm" name="password_confirm" class="form-control-orion"
             placeholder="••••••••" minlength="6" required>
    </div>

    <div class="hp-field" aria-hidden="true">
      <label>Não preencha este campo
        <input type="text" name="website" tabindex="-1" autocomplete="off">
      </label>
    </div>

    <button type="submit" class="btn-orion w-100 justify-content-center mt-2">
      <i class="bi bi-person-plus"></i> Criar conta
    </button>
  </form>

  <p class="auth-alt">Já tem conta? <a href="<?= url('login') ?>">Entrar</a></p>
</div>
<?php clear_old(); ?>
