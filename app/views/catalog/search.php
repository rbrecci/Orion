<?php
defined('ORION') || exit('Acesso negado.');
$ageOptions = ['L' => 'Livre', '10' => '10+', '12' => '12+', '14' => '14+', '16' => '16+', '18' => '18+'];
$sortOptions = ['recent' => 'Mais recentes', 'title' => 'Título (A-Z)', 'year' => 'Ano (novos primeiro)'];
?>

<div class="site-page">
  <div class="search-head">
    <h1><i class="bi bi-search"></i> Buscar</h1>
  </div>

  <form class="search-filters" method="get" action="<?= base_path() ?>/index.php">
    <input type="hidden" name="url" value="search">

    <div class="sf-field sf-grow">
      <label class="form-label" for="q">Título</label>
      <input type="text" id="q" name="q" class="form-control-orion" placeholder="Buscar por nome do filme…"
             value="<?= e($q) ?>" autofocus>
    </div>

    <div class="sf-field">
      <label class="form-label" for="genre">Gênero</label>
      <select id="genre" name="genre" class="form-select-orion" onchange="this.form.submit()" style="min-width:170px">
        <option value="">Todos</option>
        <?php foreach ($genres as $g): ?>
          <option value="<?= (int) $g['id'] ?>" <?= (string) $genreId === (string) $g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="sf-field">
      <label class="form-label" for="age">Classificação</label>
      <select id="age" name="age" class="form-select-orion" onchange="this.form.submit()" style="min-width:120px">
        <option value="">Todas</option>
        <?php foreach ($ageOptions as $k => $lbl): ?>
          <option value="<?= $k ?>" <?= $age === (string) $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="sf-field">
      <label class="form-label" for="sort">Ordenar</label>
      <select id="sort" name="sort" class="form-select-orion" onchange="this.form.submit()" style="min-width:180px">
        <?php foreach ($sortOptions as $k => $lbl): ?>
          <option value="<?= $k ?>" <?= $sort === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="sf-field">
      <button type="submit" class="btn-orion"><i class="bi bi-search"></i> Buscar</button>
    </div>
    <?php if ($hasQuery): ?>
      <div class="sf-field">
        <a class="btn-ghost" href="<?= url('search') ?>"><i class="bi bi-x-lg"></i> Limpar</a>
      </div>
    <?php endif; ?>
  </form>

  <?php if ($hasQuery): ?>
    <p class="search-count"><?= count($results) ?> resultado<?= count($results) === 1 ? '' : 's' ?>
      <?php if ($q !== ''): ?> para “<?= e($q) ?>”<?php endif; ?>.</p>
  <?php endif; ?>

  <?php if (empty($results)): ?>
    <div class="empty">
      <i class="bi bi-film"></i>
      <?php if ($hasQuery): ?>Nenhum filme encontrado com esses critérios.<?php else: ?>Use os campos acima para encontrar filmes.<?php endif; ?>
    </div>
  <?php else: ?>
    <div class="poster-grid">
      <?php foreach ($results as $m): $card = $m; require VIEW_PATH . '/partials/poster_card.php'; endforeach; ?>
    </div>
  <?php endif; ?>
</div>
