/* auctions.js — ensure images render in ALL .auction-module sections */
(function () {
  'use strict';

  function promoteLazyAttrs(root) {
    // 1) <source data-srcset> -> srcset
    root.querySelectorAll('picture source[data-srcset]').forEach(function (s) {
      s.setAttribute('srcset', s.getAttribute('data-srcset'));
    });

    // 2) <img data-src> -> src
    root.querySelectorAll('picture img[data-src], img[data-src]').forEach(function (img) {
      img.setAttribute('src', img.getAttribute('data-src'));
    });

    // 3) If <img> only has srcset (no src), set src to first candidate (safari-safe)
    root.querySelectorAll('picture img:not([src])').forEach(function (img) {
      var ss = img.getAttribute('srcset');
      if (!ss) return;
      var first = ss.split(',')[0].trim().split(' ')[0];
      if (first) img.setAttribute('src', first);
    });
  }

  function enforceRatio(root) {
    // Make .ratio boxes always own height
    root.querySelectorAll('.ratio').forEach(function (box) {
      // If ::before didn’t compute, set a safe default
      var pt = getComputedStyle(box, '::before').getPropertyValue('padding-top');
      if (!pt || pt === '0px') {
        box.style.setProperty('--ar', '56.25%'); // 16:9
        box.style.minHeight = '220px';
      }
      // If not using our var, still add a fallback element height
      if (!box.classList.contains('ratio-16x9') && !box.style.getPropertyValue('--ar')) {
        box.style.setProperty('--ar', '56.25%');
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    // Apply to ALL auction modules (previous version only hit the first one)
    var modules = document.querySelectorAll('.auction-module');
    if (!modules.length) return;

    modules.forEach(function (mod) {
      promoteLazyAttrs(mod);
      enforceRatio(mod);
    });
  });
})();