document.addEventListener('DOMContentLoaded', function () {
  var menu = document.querySelector('#menu-muukal-header-menu-1');

  if (!menu) {
    return;
  }

  menu.addEventListener('click', function (event) {
    var toggleButton = event.target.closest('.ast-menu-toggle');

    if (!toggleButton || !menu.contains(toggleButton)) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    var menuItem = toggleButton.closest('li.menu-item-has-children');
    if (!menuItem) {
      return;
    }

    var subMenu = menuItem.querySelector(':scope > .sub-menu');
    if (!subMenu) {
      return;
    }

    var isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';
    var nextState = !isExpanded;
    var linkButton = menuItem.querySelector(':scope > a.menu-link');
    var arrowButton = linkButton ? linkButton.querySelector('.dropdown-menu-toggle') : null;

    toggleButton.setAttribute('aria-expanded', String(nextState));
    menuItem.classList.toggle('ast-submenu-expanded', nextState);
    subMenu.style.display = nextState ? 'block' : 'none';

    if (linkButton) {
      linkButton.setAttribute('aria-expanded', String(nextState));
    }

    if (arrowButton) {
      arrowButton.setAttribute('aria-expanded', String(nextState));
    }
  });
});
