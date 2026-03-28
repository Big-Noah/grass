(function () {
  const root = document.getElementById("muukal-shipping-admin-root");
  if (!root) {
    return;
  }

  const addButton = document.getElementById("muukal-shipping-admin-add-row");
  const rowsWrap = document.getElementById("muukal-shipping-admin-rows");
  const template = document.getElementById("tmpl-muukal-shipping-admin-row");

  if (!addButton || !rowsWrap || !template) {
    return;
  }

  function nextIndex() {
    return rowsWrap.querySelectorAll("tr").length;
  }

  addButton.addEventListener("click", function () {
    const html = template.innerHTML.replaceAll("{{{data.index}}}", String(nextIndex()));
    rowsWrap.insertAdjacentHTML("beforeend", html);
  });

  rowsWrap.addEventListener("click", function (event) {
    const removeButton = event.target.closest(".muukal-shipping-admin-remove-row");
    if (!removeButton) {
      return;
    }

    const row = removeButton.closest("tr");
    if (row) {
      row.remove();
    }
  });
})();
