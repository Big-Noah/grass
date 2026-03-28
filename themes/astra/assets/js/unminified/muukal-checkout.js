(function ($) {
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

  $(document.body).on("updated_checkout", function () {
    syncCheckedPaymentCards();
  });

  $(function () {
    syncCheckedPaymentCards();
  });
})(jQuery);
