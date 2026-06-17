<?php defined('ORION') || exit('Acesso negado.'); ?>

<div class="site-page narrow">
  <div class="page-head">
    <h1><i class="bi bi-calculator"></i> Previsão de aluguel</h1>
    <a class="btn-ghost" href="<?= url('browse') ?>"><i class="bi bi-grid"></i> Catálogo</a>
  </div>

  <div class="card-orion">
    <div class="card-body">
      <p class="pricing-intro">
        Estime o custo antes de alugar. O valor é o
        <b>valor base do filme</b> (direito a 1 visualização) <b>+ R$ 0,99 por dia</b>
        de acesso ilimitado. Não considera o desconto de fidelidade.
      </p>

      <?php if (empty($movies)): ?>
        <div class="empty"><i class="bi bi-film"></i> Não há filmes disponíveis para simular.</div>
      <?php else: ?>
        <div class="calc" id="calc" data-rate="<?= number_format((float) $dailyRate, 2, '.', '') ?>">
          <div class="row g-3">
            <div class="col-md-7">
              <label class="form-label" for="calcMovie">Filme</label>
              <select id="calcMovie" class="form-select-orion">
                <?php foreach ($movies as $mv): ?>
                  <option value="<?= number_format((float) $mv['base_price'], 2, '.', '') ?>">
                    <?= e($mv['title']) ?> (base <?= money($mv['base_price']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-5">
              <label class="form-label" for="calcDays">Dias de acesso</label>
              <input type="number" id="calcDays" class="form-control-orion" min="0" max="30" step="1" value="3">
            </div>
          </div>

          <p class="calc-mode" id="calcMode"></p>

          <div class="calc-result">
            <div class="calc-break">
              <span>Valor base</span><span id="calcBase">R$ 0,00</span>
            </div>
            <div class="calc-break">
              <span id="calcDailyLbl">Diária × dias</span><span id="calcDaily">R$ 0,00</span>
            </div>
            <div class="calc-total">
              <span>Total estimado</span><span id="calcTotal" class="rent-total">R$ 0,00</span>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
