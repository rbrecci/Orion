<?php defined('ORION') || exit('Acesso negado.'); ?>
<?php foreach (get_flashes() as $f): ?>
  <div class="flash <?= e($f['type']) ?>">
    <i class="bi <?= $f['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?>"></i>
    <span><?= e($f['message']) ?></span>
  </div>
<?php endforeach; ?>
