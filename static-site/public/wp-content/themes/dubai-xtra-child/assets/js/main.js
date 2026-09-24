/**
 * Dubai Xtra — Main JS
 * @package Dubai_Xtra
 */

(function () {
  'use strict';

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // Sticky header shadow on scroll
  var header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 10) {
        header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.04)';
      } else {
        header.style.boxShadow = 'none';
      }
    });
  }

  // Category card active state
  document.querySelectorAll('.dx-category-card').forEach(function (card) {
    card.addEventListener('click', function () {
      var wasActive = this.classList.contains('active');
      document.querySelectorAll('.dx-category-card').forEach(function (c) {
        c.classList.remove('active');
      });
      if (!wasActive) {
        this.classList.add('active');
      }
    });
  });

})();
