<?php defined('ORION') || exit('Acesso negado.'); ?>

<div class="page-head">
  <h1><i class="bi bi-people-fill"></i> Usuários</h1>
  <a class="btn-orion" href="<?= url('admin/users/create') ?>"><i class="bi bi-person-plus"></i> Novo usuário</a>
</div>

<form class="toolbar" method="get" action="<?= base_path() ?>/index.php">
  <input type="hidden" name="url" value="admin/users">
  <div class="grow">
    <input type="text" name="q" class="form-control-orion" placeholder="Buscar por username ou e-mail…" value="<?= e($search) ?>">
  </div>
  <select name="role" class="form-select-orion" style="max-width:180px">
    <option value="">Todos os perfis</option>
    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
    <option value="user"  <?= $role === 'user'  ? 'selected' : '' ?>>User</option>
  </select>
  <button class="btn-ghost" type="submit"><i class="bi bi-search"></i> Filtrar</button>
  <?php if ($search !== '' || $role !== ''): ?>
    <a class="btn-ghost" href="<?= url('admin/users') ?>"><i class="bi bi-x-lg"></i> Limpar</a>
  <?php endif; ?>
</form>

<div class="card-orion">
  <div class="card-body">
    <?php if (empty($users)): ?>
      <div class="empty"><i class="bi bi-people"></i> Nenhum usuário encontrado.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table-orion">
          <thead>
            <tr>
              <th>#</th><th>Username</th><th class="hide-sm">E-mail</th>
              <th>Perfil</th><th>Status</th><th class="hide-sm">Criado em</th>
              <th class="text-end">Ações</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($users as $usr): $isSelf = ((int) $usr['id'] === (int) Auth::id()); ?>
            <tr>
              <td style="color:var(--faint)"><?= (int) $usr['id'] ?></td>
              <td>
                <b><?= e($usr['username']) ?></b>
                <?php if ($isSelf): ?><span class="badge-orion badge-soft ms-1">você</span><?php endif; ?>
              </td>
              <td class="hide-sm" style="color:var(--muted)"><?= e($usr['email']) ?></td>
              <td>
                <span class="badge-orion <?= $usr['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                  <i class="bi <?= $usr['role'] === 'admin' ? 'bi-shield-lock-fill' : 'bi-person-fill' ?>"></i>
                  <?= e($usr['role']) ?>
                </span>
              </td>
              <td>
                <span class="badge-orion <?= $usr['status'] === 'active' ? 'badge-active' : 'badge-blocked' ?>">
                  <?= $usr['status'] === 'active' ? 'Ativo' : 'Bloqueado' ?>
                </span>
              </td>
              <td class="hide-sm" style="color:var(--faint);font-size:.82rem"><?= dt($usr['created_at']) ?></td>
              <td>
                <div class="d-flex gap-2 justify-content-end">
                  <a class="btn-icon" href="<?= url('admin/users/' . $usr['id'] . '/edit') ?>" title="Editar"><i class="bi bi-pencil"></i></a>
                  <?php if (!$isSelf): ?>
                    <form method="post" action="<?= url('admin/users/' . $usr['id'] . '/delete') ?>"
                          data-confirm="Excluir o usuário &quot;<?= e($usr['username']) ?>&quot;? Esta ação não pode ser desfeita.">
                      <?= csrf_field() ?>
                      <button class="btn-icon danger" type="submit" title="Excluir"><i class="bi bi-trash"></i></button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
