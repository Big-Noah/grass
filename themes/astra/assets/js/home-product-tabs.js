(function () {
  function initHomeTabs(root) {
    var tabButtons = root.querySelectorAll("[data-tab-target]");
    var panels = root.querySelectorAll("[data-tab-panel]");
    if (!tabButtons.length || !panels.length) {
      return;
    }

    function activate(tabId) {
      tabButtons.forEach(function (btn) {
        var isActive = btn.getAttribute("data-tab-target") === tabId;
        btn.classList.toggle("is-active", isActive);
        btn.setAttribute("aria-selected", isActive ? "true" : "false");
      });

      panels.forEach(function (panel) {
        var isActive = panel.getAttribute("data-tab-panel") === tabId;
        panel.classList.toggle("is-active", isActive);
        panel.hidden = !isActive;
      });
    }

    tabButtons.forEach(function (btn) {
      btn.addEventListener("click", function () {
        activate(btn.getAttribute("data-tab-target"));
      });
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[data-muukal-home-tabs]").forEach(initHomeTabs);
  });
})();

