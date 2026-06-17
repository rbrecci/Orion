<?php
defined('ORION') || exit('Acesso negado.');
$isEdit = !empty($user);
$action = $isEdit ? url('admin/users/' . $user['id']) : url('admin/users');
$val = function ($key, $default = '') use ($user) {
    return old($key, $user[$key] ?? $default);
};
?>

<div class="page-head">
  <h1><i class="bi bi-person-<?= $isEdit ? 'gear' : 'plus' ?>"></i> <?= e($title) ?></h1>
  <a class="btn-ghost" href="<?= url('admin/users') ?>"><i class="bi bi-arrow-left"></i> Voltar</a>
</div>

<div class="card-orion" style="max-width:720px">
  <div class="card-body">
    <form method="post" action="<?= $action ?>" autocomplete="off">
      <?= csrf_field() ?>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="username">Username <span style="color:var(--danger)">*</span></label>
          <input type="text" id="username" name="username" class="form-control-orion"
                 value="<?= e($val('username')) ?>" maxlength="50" required>
          <div class="form-hint">Login é <b>case-sensitive</b> (3 a 50 caracteres).</div>
        </div>

        <div class="col-md-6">
          <label class="form-label" for="email">E-mail <span style="color:var(--danger)">*</span></label>
          <input type="email" id="email" name="email" class="form-control-orion"
                 value="<?= e($val('email')) ?>" maxlength="150" required>
        </div>

        <div class="col-md-6">
          <label class="form-label" for="password">
            Senha <?= $isEdit ? '' : '<span style="color:var(--danger)">*</span>' ?>
          </label>
          <input type="password" id="password" name="password" class="form-control-orion"
                 placeholder="<?= $isEdit ? 'Deixe em branco para manter' : 'Mínimo 6 caracteres' ?>"
                 <?= $isEdit ? '' : 'required' ?>>
        </div>

        <div class="col-md-6">
          <label class="form-label" for="password_confirm">Confirmar senha</label>
          <input type="password" id="password_confirm" name="password_confirm" class="form-control-orion"
                 placeholder="Repita a senha">
        </div>

        <div class="col-md-6">
          <label class="form-label" for="role">Perfil <span style="color:var(--danger)">*</span></label>
          <select id="role" name="role" class="form-select-orion">
            <?php $r = $val('role', 'user'); ?>
            <option value="user"  <?= $r === 'user'  ? 'selected' : '' ?>>User (usuário comum)</option>
            <option value="admin" <?= $r === 'admin' ? 'selected' : '' ?>>Admin (administrador)</option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label" for="status">Status</label>
          <select id="status" name="status" class="form-select-orion">
            <?php $s = $val('status', 'active'); ?>
            <option value="active"  <?= $s === 'active'  ? 'selected' : '' ?>>Ativo</option>
            <option value="blocked" <?= $s === 'blocked' ? 'selected' : '' ?>>Bloqueado</option>
          </select>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn-orion"><i class="bi bi-check-lg"></i> <?= $isEdit ? 'Salvar alterações' : 'Criar usuário' ?></button>
        <a class="btn-ghost" href="<?= url('admin/users') ?>">Cancelar</a>
      </div>
    </form>
  </div>
</div>
