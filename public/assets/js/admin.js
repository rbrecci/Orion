(function () {
  'use strict';

  var sidebar  = document.getElementById('sidebar');
  var toggle   = document.getElementById('sidebarToggle');
  var backdrop = document.getElementById('sidebarBackdrop');

  function openSidebar()  { if (sidebar) { sidebar.classList.add('open'); backdrop.classList.add('show'); } }
  function closeSidebar() { if (sidebar) { sidebar.classList.remove('open'); backdrop.classList.remove('show'); } }

  if (toggle)   toggle.addEventListener('click', openSidebar);
  if (backdrop) backdrop.addEventListener('click', closeSidebar);

  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) {
        e.preventDefault();
      }
    });
  });

  document.querySelectorAll('.chip-check').forEach(function (chip) {
    var input = chip.querySelector('input[type="checkbox"]');
    if (!input) return;
    var sync = function () { chip.classList.toggle('checked', input.checked); };
    sync();
    input.addEventListener('change', sync);
  });

  var posterInput   = document.getElementById('poster_url');
  var posterPreview = document.getElementById('posterPreview');
  if (posterInput && posterPreview) {
    var updatePreview = function () {
      var url = posterInput.value.trim();
      if (url) { posterPreview.src = url; posterPreview.style.display = 'block'; }
      else { posterPreview.style.display = 'none'; }
    };
    posterInput.addEventListener('input', updatePreview);
    posterPreview.addEventListener('error', function () { posterPreview.style.display = 'none'; });
    updatePreview();
  }

  setTimeout(function () {
    document.querySelectorAll('.flash').forEach(function (el) {
      el.style.transition = 'opacity .4s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    });
  }, 5000);
})();
