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

    function slugify(text) {
      return (text || '')
        .toLowerCase()
        .replace(/[\u2018\u2019']/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    }

    function decorateDesktopMegaMenu() {
      var desktopMenu = header.querySelector('.muukal-header__nav .muukal-header__menu');
      if (!desktopMenu) {
        return;
      }

      Array.prototype.forEach.call(desktopMenu.children, function (topLevelItem) {
        if (!topLevelItem.classList.contains('menu-item-has-children')) {
          return;
        }

        topLevelItem.classList.add('muukal-header__menu-item--mega');

        var topLevelSubmenu = topLevelItem.querySelector(':scope > .sub-menu');
        if (!topLevelSubmenu) {
          return;
        }

        Array.prototype.forEach.call(topLevelSubmenu.children, function (columnItem) {
          if (!columnItem.matches('.menu-item')) {
            return;
          }

          columnItem.classList.add('muukal-header__mega-column');

          var columnLink = columnItem.querySelector(':scope > a');
          var columnLabel = columnLink ? columnLink.textContent.trim().toLowerCase() : '';

          if ('shop by color' === columnLabel) {
            columnItem.classList.add('muukal-header__mega-column--colors');

            var colorList = columnItem.querySelector(':scope > .sub-menu');
            if (!colorList) {
              return;
            }

            Array.prototype.forEach.call(colorList.children, function (colorItem) {
              if (!colorItem.matches('.menu-item')) {
                return;
              }

              var colorLink = colorItem.querySelector(':scope > a');
              var colorSlug = slugify(colorLink ? colorLink.textContent.trim() : '');

              colorItem.classList.add('muukal-header__color-item');

              if (colorSlug) {
                colorItem.classList.add('muukal-header__color-item--' + colorSlug);
              }
            });
          }
        });
      });
    }

    var searchButton = header.querySelector('[data-muukal-search-toggle]');
    var searchPanel = header.querySelector('#muukal-header-search');
    var menuButton = header.querySelector('[data-muukal-menu-toggle]');
    var menuPanel = header.querySelector('#muukal-header-mobile-menu');

    decorateDesktopMegaMenu();

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
