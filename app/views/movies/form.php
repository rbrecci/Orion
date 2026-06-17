<?php
defined('ORION') || exit('Acesso negado.');
$isEdit = !empty($movie);
$action = $isEdit ? url('admin/movies/' . $movie['id']) : url('admin/movies');
$val = function ($key, $default = '') use ($movie) {
    return old($key, $movie[$key] ?? $default);
};
$selected         = old_array('genres', $selectedGenres ?? []);
$availableChecked = old_checked('available', $isEdit ? (bool) $movie['available'] : true);
$featuredChecked  = old_checked('featured',  $isEdit ? (bool) $movie['featured']  : false);
$ageOptions       = ['L' => 'Livre', '10' => '10 anos', '12' => '12 anos', '14' => '14 anos', '16' => '16 anos', '18' => '18 anos'];
?>

<div class="page-head">
  <h1><i class="bi bi-film"></i> <?= e($title) ?></h1>
  <a class="btn-ghost" href="<?= url('admin/movies') ?>"><i class="bi bi-arrow-left"></i> Voltar</a>
</div>

<form method="post" action="<?= $action ?>">
  <?= csrf_field() ?>
  <div class="row g-3">

    <div class="col-lg-8">
      <div class="card-orion mb-3">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="title">Título <span style="color:var(--danger)">*</span></label>
            <input type="text" id="title" name="title" class="form-control-orion" value="<?= e($val('title')) ?>" maxlength="200" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="synopsis">Sinopse</label>
            <textarea id="synopsis" name="synopsis" class="form-control-orion" rows="4" placeholder="Resumo do filme…"><?= e($val('synopsis')) ?></textarea>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="director">Diretor</label>
              <input type="text" id="director" name="director" class="form-control-orion" value="<?= e($val('director')) ?>" maxlength="150">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="cast_list">Elenco</label>
              <input type="text" id="cast_list" name="cast_list" class="form-control-orion" value="<?= e($val('cast_list')) ?>" placeholder="Atores principais">
            </div>
          </div>
        </div>
      </div>

      <div class="card-orion mb-3">
        <div class="card-body">
          <h3 style="font-size:1rem"><i class="bi bi-image"></i> Mídia (URLs)</h3>
          <div class="row g-3 mt-1">
            <div class="col-md-7">
              <label class="form-label" for="poster_url">Capa / Poster <span class="form-hint">(miniatura vertical)</span></label>
              <input type="url" id="poster_url" name="poster_url" class="form-control-orion" value="<?= e($val('poster_url')) ?>" placeholder="https://…/capa.jpg" maxlength="500">

              <label class="form-label mt-3" for="backdrop_url">Banner / Backdrop <span class="form-hint">(destaque no topo)</span></label>
              <input type="url" id="backdrop_url" name="backdrop_url" class="form-control-orion" value="<?= e($val('backdrop_url')) ?>" placeholder="https://…/banner.jpg" maxlength="500">

              <label class="form-label mt-3" for="trailer_url">Trailer <span class="form-hint">(YouTube/MP4)</span></label>
              <input type="url" id="trailer_url" name="trailer_url" class="form-control-orion" value="<?= e($val('trailer_url')) ?>" placeholder="https://…/trailer" maxlength="500">
            </div>
            <div class="col-md-5">
              <label class="form-label">Pré-visualização da capa</label>
              <div style="border:1px dashed var(--line);border-radius:12px;padding:10px;text-align:center;min-height:180px;display:grid;place-items:center">
                <img id="posterPreview" src="<?= e(media($val('poster_url'))) ?>" alt="" style="max-width:100%;max-height:240px;border-radius:8px;display:none">
                <span class="form-hint" style="position:absolute"><i class="bi bi-image"></i></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-orion">
        <div class="card-body">
          <h3 style="font-size:1rem"><i class="bi bi-tags"></i> Gêneros</h3>
          <div class="d-flex flex-wrap gap-2 mt-2">
            <?php foreach ($genres as $g): ?>
              <label class="chip-check">
                <input type="checkbox" name="genres[]" value="<?= (int) $g['id'] ?>" <?= in_array((int) $g['id'], $selected, true) ? 'checked' : '' ?>>
                <?= e($g['name']) ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card-orion mb-3">
        <div class="card-body">
          <h3 style="font-size:1rem"><i class="bi bi-cash-coin"></i> Locação</h3>
          <div class="mb-3 mt-2">
            <label class="form-label" for="base_price">Valor base (R$) <span style="color:var(--danger)">*</span></label>
            <input type="number" step="0.01" min="0" id="base_price" name="base_price" class="form-control-orion" value="<?= e($val('base_price', '0.00')) ?>" required>
            <div class="form-hint">Preço base do filme. A diária por dia adicional é fixa (R$ 0,99).</div>
          </div>
        </div>
      </div>

      <div class="card-orion mb-3">
        <div class="card-body">
          <h3 style="font-size:1rem"><i class="bi bi-sliders"></i> Detalhes</h3>
          <div class="row g-3 mt-1">
            <div class="col-6">
              <label class="form-label" for="release_year">Ano</label>
              <input type="number" id="release_year" name="release_year" class="form-control-orion" value="<?= e($val('release_year')) ?>" placeholder="2024">
            </div>
            <div class="col-6">
              <label class="form-label" for="duration_min">Duração (min)</label>
              <input type="number" id="duration_min" name="duration_min" class="form-control-orion" value="<?= e($val('duration_min')) ?>" placeholder="120">
            </div>
            <div class="col-12">
              <label class="form-label" for="age_rating">Classificação</label>
              <select id="age_rating" name="age_rating" class="form-select-orion">
                <?php $age = $val('age_rating', 'L'); foreach ($ageOptions as $k => $label): ?>
                  <option value="<?= $k ?>" <?= (string) $age === (string) $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="card-orion mb-3">
        <div class="card-body d-flex flex-column gap-2">
          <label class="chip-check <?= $availableChecked ? 'checked' : '' ?>">
            <input type="checkbox" name="available" value="1" <?= $availableChecked ? 'checked' : '' ?>>
            <i class="bi bi-eye"></i> Disponível no catálogo
          </label>
          <label class="chip-check <?= $featuredChecked ? 'checked' : '' ?>">
            <input type="checkbox" name="featured" value="1" <?= $featuredChecked ? 'checked' : '' ?>>
            <i class="bi bi-star"></i> Destaque (banner)
          </label>
        </div>
      </div>

      <button type="submit" class="btn-orion w-100 justify-content-center">
        <i class="bi bi-check-lg"></i> <?= $isEdit ? 'Salvar alterações' : 'Cadastrar filme' ?>
      </button>
    </div>
  </div>
</form>
