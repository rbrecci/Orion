(function () {
  'use strict';

  function brl(value) {
    return 'R$ ' + Number(value).toFixed(2)
      .replace('.', ',')
      .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }
  function intVal(el, min, max) {
    var n = parseInt(el.value, 10);
    if (isNaN(n) || n < min) n = min;
    if (n > max) n = max;
    return n;
  }

  var burger = document.getElementById('navBurger');
  var menu   = document.getElementById('siteMenu');
  if (burger && menu) {
    burger.addEventListener('click', function () { menu.classList.toggle('open'); });
  }

  document.querySelectorAll('.poster-strip').forEach(function (strip) {
    var vp = document.createElement('div');
    vp.className = 'strip-viewport';
    strip.parentNode.insertBefore(vp, strip);
    vp.appendChild(strip);

    function makeBtn(dir, icon) {
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'strip-nav ' + dir;
      b.setAttribute('aria-label', dir === 'prev' ? 'Anterior' : 'Próximo');
      b.innerHTML = '<i class="bi ' + icon + '"></i>';
      vp.appendChild(b);
      return b;
    }
    var prev = makeBtn('prev', 'bi-chevron-left');
    var next = makeBtn('next', 'bi-chevron-right');
    var step = function () { return Math.max(200, Math.round(strip.clientWidth * 0.8)); };

    var anim = null;
    var easeInOutCubic = function (t) {
      return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    };
    var glide = function (delta) {
      var start   = strip.scrollLeft;
      var maxLeft = strip.scrollWidth - strip.clientWidth;
      var target  = Math.max(0, Math.min(start + delta, maxLeft));
      var change  = target - start;
      if (Math.abs(change) < 1) return;
      if (anim) cancelAnimationFrame(anim);
      var dur = 520, t0 = null;
      var frame = function (now) {
        if (t0 === null) t0 = now;
        var p = Math.min(1, (now - t0) / dur);
        strip.scrollLeft = start + change * easeInOutCubic(p);
        if (p < 1) { anim = requestAnimationFrame(frame); }
        else       { anim = null; update(); }
      };
      anim = requestAnimationFrame(frame);
    };

    prev.addEventListener('click', function () { glide(-step()); });
    next.addEventListener('click', function () { glide(step()); });

    function update() {
      var max = strip.scrollWidth - strip.clientWidth - 2;
      prev.disabled = strip.scrollLeft <= 2;
      next.disabled = strip.scrollLeft >= max;
    }
    strip.addEventListener('scroll', update);
    window.addEventListener('resize', update);
    strip.addEventListener('wheel', function (e) {
      if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) { strip.scrollLeft += e.deltaY; e.preventDefault(); }
    }, { passive: false });
    update();
  });

  document.addEventListener('click', function (e) {
    document.querySelectorAll('details.user-menu[open]').forEach(function (d) {
      if (!d.contains(e.target)) d.removeAttribute('open');
    });
  });

  document.querySelectorAll('form[data-confirm], button[data-confirm]').forEach(function (el) {
    var form = el.tagName === 'FORM' ? el : el.closest('form');
    var msg  = el.getAttribute('data-confirm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      if (!window.confirm(msg)) e.preventDefault();
    });
  });

  var rentBox = document.getElementById('rentBox');
  if (rentBox) {
    var base   = parseFloat(rentBox.dataset.base) || 0;
    var rate   = parseFloat(rentBox.dataset.rate) || 0;
    var days   = document.getElementById('rentDays');
    var price  = document.getElementById('rentPrice');
    var label  = document.getElementById('rentBtnLabel');
    var mode   = document.getElementById('rentMode');
    var formula= document.getElementById('rentFormula');

    var updateRent = function () {
      var d = intVal(days, 0, 30);
      var total = base + rate * d;
      if (price) price.textContent = brl(total);
      if (label) label.textContent = brl(total);
      if (mode) {
        mode.innerHTML = d === 0
          ? '<i class="bi bi-1-circle"></i> Visualização única: assista 1 vez.'
          : '<i class="bi bi-infinity"></i> Acesso ilimitado por ' + d + ' dia' + (d > 1 ? 's' : '') + '.';
      }
      if (formula) {
        formula.textContent = d === 0
          ? '(' + brl(base) + ' valor base)'
          : '(' + brl(base) + ' + ' + brl(rate) + ' × ' + d + ')';
      }
    };
    days.addEventListener('input', updateRent);
    days.addEventListener('change', updateRent);
    updateRent();
  }

  var calc = document.getElementById('calc');
  if (calc) {
    var cRate  = parseFloat(calc.dataset.rate) || 0;
    var movie  = document.getElementById('calcMovie');
    var cDays  = document.getElementById('calcDays');
    var elBase = document.getElementById('calcBase');
    var elDaily= document.getElementById('calcDaily');
    var elLbl  = document.getElementById('calcDailyLbl');
    var elTotal= document.getElementById('calcTotal');
    var elMode = document.getElementById('calcMode');

    var updateCalc = function () {
      var mBase = parseFloat(movie.value) || 0;
      var d = intVal(cDays, 0, 30);
      var sub = cRate * d;
      var total = mBase + sub;
      elBase.textContent  = brl(mBase);
      elDaily.textContent = brl(sub);
      elLbl.textContent   = 'Diária (' + brl(cRate) + ') × ' + d + ' dia' + (d === 1 ? '' : 's');
      elTotal.textContent = brl(total);
      elMode.innerHTML = d === 0
        ? '<i class="bi bi-1-circle"></i> <b>Visualização única</b>: paga só o valor base e assiste 1 vez.'
        : '<i class="bi bi-infinity"></i> <b>Acesso ilimitado</b> por ' + d + ' dia' + (d > 1 ? 's' : '') + '.';
    };
    movie.addEventListener('change', updateCalc);
    cDays.addEventListener('input', updateCalc);
    cDays.addEventListener('change', updateCalc);
    updateCalc();
  }

  setTimeout(function () {
    document.querySelectorAll('.flash').forEach(function (el) {
      el.style.transition = 'opacity .4s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    });
  }, 5000);
})();
