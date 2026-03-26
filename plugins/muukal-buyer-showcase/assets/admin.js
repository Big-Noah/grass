jQuery(($) => {
  const tableBody = $("#muukal-buyer-showcase-rows");
  const template = wp.template("muukal-buyer-showcase-row");

  const refreshPreview = ($row, url) => {
    const $preview = $row.find(".muukal-buyer-showcase-admin__preview");
    const safeUrl = url || "";
    if (!safeUrl) {
      $preview.empty();
      return;
    }

    $preview.html(`<img src="${safeUrl}" alt="" />`);
  };

  $("#muukal-buyer-showcase-add-row").on("click", () => {
    const nextIndex = tableBody.children().length;
    tableBody.append(template({ index: nextIndex }));
  });

  tableBody.on("click", ".muukal-buyer-showcase-remove-row", function () {
    $(this).closest("tr").remove();
  });

  tableBody.on("input", ".muukal-buyer-showcase-image-field", function () {
    refreshPreview($(this).closest("tr"), $(this).val());
  });

  tableBody.on("click", ".muukal-buyer-showcase-upload", function () {
    const $row = $(this).closest("tr");
    const $field = $row.find(".muukal-buyer-showcase-image-field");
    const frame = wp.media({
      title: "Select showcase image",
      multiple: false,
      library: { type: "image" },
      button: { text: "Use this image" },
    });

    frame.on("select", () => {
      const attachment = frame.state().get("selection").first().toJSON();
      $field.val(attachment.url).trigger("input");
    });

    frame.open();
  });
});
