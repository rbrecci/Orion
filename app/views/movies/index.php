<?php defined('ORION') || exit('Acesso negado.'); ?>

<div class="page-head">
  <h1><i class="bi bi-film"></i> Filmes</h1>
  <a class="btn-orion" href="<?= url('admin/movies/create') ?>"><i class="bi bi-plus-lg"></i> Novo filme</a>
</div>

<form class="toolbar" method="get" action="<?= base_path() ?>/index.php">
  <input type="hidden" name="url" value="admin/movies">
  <div class="grow">
    <input type="text" name="q" class="form-control-orion" placeholder="Buscar por título…" value="<?= e($search) ?>">
  </div>
  <select name="genre" class="form-select-orion" style="max-width:200px">
    <option value="">Todos os gêneros</option>
    <?php foreach ($genres as $g): ?>
      <option value="<?= (int) $g['id'] ?>" <?= (string) $genreId === (string) $g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="available" class="form-select-orion" style="max-width:180px">
    <option value="">Disponibilidade</option>
    <option value="1" <?= $available === '1' ? 'selected' : '' ?>>Disponível</option>
    <option value="0" <?= $available === '0' ? 'selected' : '' ?>>Indisponível</option>
  </select>
  <button class="btn-ghost" type="submit"><i class="bi bi-search"></i> Filtrar</button>
  <?php if ($search !== '' || $genreId !== '' || $available !== ''): ?>
    <a class="btn-ghost" href="<?= url('admin/movies') ?>"><i class="bi bi-x-lg"></i> Limpar</a>
  <?php endif; ?>
</form>

<div class="card-orion">
  <div class="card-body">
    <?php if (empty($movies)): ?>
      <div class="empty"><i class="bi bi-film"></i> Nenhum filme encontrado.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table-orion">
          <thead>
            <tr>
              <th></th><th>Título</th><th class="hide-sm">Gêneros</th>
              <th>Valor base</th><th>Status</th>
              <th class="text-end">Ações</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($movies as $m): ?>
            <tr>
              <td>
                <?php if (!empty($m['poster_url'])): ?>
                  <img class="thumb" src="<?= e(media($m['poster_url'])) ?>" alt="" loading="lazy">
                <?php else: ?>
                  <div class="thumb d-grid" style="place-items:center"><i class="bi bi-film" style="color:var(--faint)"></i></div>
                <?php endif; ?>
              </td>
              <td>
                <b><?= e($m['title']) ?></b>
                <?php if ($m['featured']): ?><i class="bi bi-star-fill ms-1" style="color:var(--warning)" title="Destaque"></i><?php endif; ?>
                <div style="color:var(--faint);font-size:.8rem"><?php
                  $meta = array_filter([
                    $m['release_year'] ? e($m['release_year']) : null,
                    $m['duration_min'] ? (int) $m['duration_min'] . ' min' : null,
                  ]);
                  echo $meta ? implode(' · ', $meta) : 'Sem dados';
                ?></div>
              </td>
              <td class="hide-sm" style="color:var(--muted);font-size:.85rem"><?= $m['genres'] ? e($m['genres']) : 'Sem gênero' ?></td>
              <td><?= money($m['base_price']) ?></td>
              <td>
                <span class="badge-orion <?= $m['available'] ? 'badge-active' : 'badge-blocked' ?>">
                  <?= $m['available'] ? 'Disponível' : 'Oculto' ?>
                </span>
              </td>
              <td>
                <div class="d-flex gap-2 justify-content-end">
                  <a class="btn-icon" href="<?= url('admin/movies/' . $m['id'] . '/edit') ?>" title="Editar"><i class="bi bi-pencil"></i></a>
                  <form method="post" action="<?= url('admin/movies/' . $m['id'] . '/delete') ?>"
                        data-confirm="Excluir o filme &quot;<?= e($m['title']) ?>&quot;? (Filmes já alugados não podem ser excluídos.)">
                    <?= csrf_field() ?>
                    <button class="btn-icon danger" type="submit" title="Excluir"><i class="bi bi-trash"></i></button>
                  </form>
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
