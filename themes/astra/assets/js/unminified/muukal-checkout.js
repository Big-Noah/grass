(function ($) {
  const addressModalSelector = "#muukal-checkout-address-modal";
  const modalOpenClass = "is-open";
  const bodyModalOpenClass = "muukal-checkout-address-modal-open";

  function syncCheckedPaymentCards() {
    $(".muukal-checkout-payment .wc_payment_method").each(function () {
      const method = $(this);
      method.toggleClass(
        "is-checked",
        method.find('input[name="payment_method"]').is(":checked")
      );
    });
  }

  function updateShippingOption(label) {
    if (!window.muukalCheckoutConfig || !label) {
      return;
    }

    $.post(window.muukalCheckoutConfig.ajaxUrl, {
      action: "muukal_astra_set_checkout_shipping_option",
      nonce: window.muukalCheckoutConfig.nonce,
      label: label,
    }).always(function () {
      $(document.body).trigger("update_checkout");
    });
  }

  function getActiveAddressPrefix() {
    return $("#ship-to-different-address-checkbox").is(":checked")
      ? "shipping"
      : "billing";
  }

  function initAddressModalSelects() {
    const modal = $(addressModalSelector);
    const dialog = modal.find(".muukal-checkout-address-modal__dialog");

    if (!modal.length || !dialog.length || !$.fn.selectWoo) {
      return;
    }

    modal.find("select").each(function () {
      const select = $(this);
      const currentValue = select.val();

      if (select.hasClass("select2-hidden-accessible")) {
        select.selectWoo("destroy");
      }

      select.selectWoo({
        width: "100%",
        dropdownParent: dialog,
      });

      if (typeof currentValue !== "undefined") {
        select.val(currentValue).trigger("change.select2");
      }
    });
  }

  function getFieldValue(name) {
    const field = $('[name="' + name + '"]');

    if (!field.length) {
      return "";
    }

    if (field.is("select")) {
      return $.trim(field.find("option:selected").text());
    }

    return $.trim(field.val() || "");
  }

  function buildAddressSummary() {
    const prefix = getActiveAddressPrefix();
    const city = getFieldValue(prefix + "_city");
    const state = getFieldValue(prefix + "_state");
    const postcode = getFieldValue(prefix + "_postcode");
    const name = $.trim(
      [getFieldValue(prefix + "_first_name"), getFieldValue(prefix + "_last_name")]
        .filter(Boolean)
        .join(" ")
    );
    const addressOne = getFieldValue(prefix + "_address_1");
    const addressTwo = getFieldValue(prefix + "_address_2");
    const locality = $.trim(
      [city, state, postcode].filter(Boolean).join(", ")
    );
    const country = getFieldValue(prefix + "_country");
    const phone = getFieldValue("billing_phone");
    const email = getFieldValue("billing_email");

    if (![name, addressOne, addressTwo, city, postcode, phone, email].some(Boolean)) {
      return [];
    }

    return [name, addressOne, addressTwo, locality, country, phone, email].filter(
      Boolean
    );
  }

  function syncAddressSummary() {
    const summary = $("#muukal-checkout-address-summary");

    if (!summary.length) {
      return;
    }

    const lines = buildAddressSummary();
    const emptyText = summary.data("empty-text") || "";

    if (!lines.length) {
      summary.text(emptyText);
      return;
    }

    summary.html(
      $.map(lines, function (line) {
        return (
          '<span class="muukal-checkout-address-summary__line">' +
          $("<div>").text(line).html() +
          "</span>"
        );
      }).join("")
    );
  }

  function openAddressModal() {
    const modal = $(addressModalSelector);

    if (!modal.length) {
      return;
    }

    modal.addClass(modalOpenClass).attr("aria-hidden", "false");
    $("body").addClass(bodyModalOpenClass);
    window.setTimeout(function () {
      initAddressModalSelects();
      modal.find("input, select, textarea").filter(":visible").first().trigger("focus");
    }, 0);
  }

  function closeAddressModal() {
    const modal = $(addressModalSelector);

    if (!modal.length) {
      return;
    }

    modal.removeClass(modalOpenClass).attr("aria-hidden", "true");
    $("body").removeClass(bodyModalOpenClass);
  }

  $(document).on(
    "change",
    '.muukal-checkout-shipping-selector input[type="radio"]',
    function () {
      updateShippingOption($(this).val());
    }
  );

  $(document).on(
    "change",
    '.muukal-checkout-payment input[name="payment_method"]',
    function () {
      syncCheckedPaymentCards();
    }
  );

  $(document).on("click", "[data-muukal-address-open]", function () {
    openAddressModal();
  });

  $(document).on("click", "[data-muukal-address-close]", function () {
    closeAddressModal();
  });

  $(document).on("click", "[data-muukal-address-confirm]", function () {
    syncAddressSummary();
    closeAddressModal();
  });

  $(document).on(
    "input change",
    [
      '[name^="billing_"]',
      '[name^="shipping_"]',
      "#ship-to-different-address-checkbox",
    ].join(", "),
    function () {
      syncAddressSummary();
    }
  );

  $(document).on("keydown", function (event) {
    if (event.key === "Escape") {
      closeAddressModal();
    }
  });

  $(document.body).on("updated_checkout", function () {
    syncCheckedPaymentCards();
    syncAddressSummary();
  });

  $(function () {
    syncCheckedPaymentCards();
    syncAddressSummary();
    initAddressModalSelects();
  });
})(jQuery);
