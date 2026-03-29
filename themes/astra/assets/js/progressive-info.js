document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-progressive-info]').forEach(function (section) {
    var tabs = Array.prototype.slice.call(section.querySelectorAll('[data-progressive-tab]'));

    if (!tabs.length) {
      return;
    }

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var panelId = tab.getAttribute('aria-controls');
        var panel = panelId ? document.getElementById(panelId) : null;

        tabs.forEach(function (item) {
          item.classList.remove('is-active');
          item.setAttribute('aria-selected', 'false');
        });

        section.querySelectorAll('[role="tabpanel"]').forEach(function (item) {
          item.classList.remove('is-active');
          item.hidden = true;
        });

        tab.classList.add('is-active');
        tab.setAttribute('aria-selected', 'true');

        if (panel) {
          panel.hidden = false;
          panel.classList.add('is-active');
        }
      });
    });
  });
});
