(function () {
  function togglePanel(button, panel, expanded) {
    button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    panel.hidden = !expanded;
  }

  document.addEventListener('DOMContentLoaded', function () {
    var header = document.querySelector('[data-muukal-header]');
    if (!header) {
      return;
    }

    var searchButton = header.querySelector('[data-muukal-search-toggle]');
    var searchPanel = header.querySelector('#muukal-header-search');
    var menuButton = header.querySelector('[data-muukal-menu-toggle]');
    var menuPanel = header.querySelector('#muukal-header-mobile-menu');

    if (searchButton && searchPanel) {
      searchButton.addEventListener('click', function () {
        var expanded = searchButton.getAttribute('aria-expanded') !== 'true';
        togglePanel(searchButton, searchPanel, expanded);
      });
    }

    if (menuButton && menuPanel) {
      menuButton.addEventListener('click', function () {
        var expanded = menuButton.getAttribute('aria-expanded') !== 'true';
        togglePanel(menuButton, menuPanel, expanded);
      });
    }

    function syncScrollState() {
      header.classList.toggle('is-scrolled', window.scrollY > 8);
    }

    syncScrollState();
    window.addEventListener('scroll', syncScrollState, { passive: true });
  });
})();
