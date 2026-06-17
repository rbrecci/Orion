<?php defined('ORION') || exit('Acesso negado.'); ?>

<div class="site-page narrow">
  <div class="page-head">
    <h1><i class="bi bi-person-gear"></i> Minha conta</h1>
  </div>

  <?php require VIEW_PATH . '/partials/flash.php'; ?>

  <div class="card-orion mb-3">
    <div class="card-body account-summary">
      <div class="acc-avatar"><i class="bi bi-person-circle"></i></div>
      <div>
        <div class="acc-name orion-logo"><?= e($me['username']) ?></div>
        <div class="acc-sub">
          <span class="badge-orion <?= $me['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>"><?= e($me['role']) ?></span>
          Membro desde <?= dt($me['created_at']) ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card-orion">
    <div class="card-body">
      <h3 style="font-size:1rem"><i class="bi bi-pencil-square"></i> Editar dados</h3>
      <form method="post" action="<?= url('account') ?>" autocomplete="off" class="mt-3">
        <?= csrf_field() ?>

        <div class="mb-3">
          <label class="form-label">Usuário</label>
          <input type="text" class="form-control-orion" value="<?= e($me['username']) ?>" disabled>
          <div class="form-hint">O nome de usuário não pode ser alterado.</div>
        </div>

        <div class="mb-3">
          <label class="form-label" for="email">E-mail</label>
          <input type="email" id="email" name="email" class="form-control-orion"
                 value="<?= e(old('email', $me['email'])) ?>" maxlength="150" required>
        </div>

        <hr class="acc-divider">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="password">Nova senha</label>
            <input type="password" id="password" name="password" class="form-control-orion"
                   placeholder="deixe em branco para manter" minlength="6">
          </div>
          <div class="col-md-6">
            <label class="form-label" for="password_confirm">Confirmar nova senha</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-control-orion"
                   placeholder="••••••••" minlength="6">
          </div>
        </div>

        <button type="submit" class="btn-orion mt-4"><i class="bi bi-check-lg"></i> Salvar alterações</button>
      </form>
    </div>
  </div>
</div>
<?php clear_old(); ?>
