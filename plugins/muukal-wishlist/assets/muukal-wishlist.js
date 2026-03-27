(function () {
  function updateCount(count) {
    document.querySelectorAll("[data-muukal-wishlist-count]").forEach(function (node) {
      node.textContent = String(count);
    });
  }

  function syncButtons(productId, added) {
    document.querySelectorAll('.muukal-wishlist-button[data-product-id="' + productId + '"]').forEach(function (button) {
      var addLabel = button.getAttribute("data-label-add") || "Add to Wishlist";
      var addedLabel = button.getAttribute("data-label-added") || "Saved";
      button.classList.toggle("is-saved", added);
      button.setAttribute("aria-pressed", added ? "true" : "false");
      var label = button.querySelector(".muukal-wishlist-button__label");
      if (label) {
        label.textContent = added ? addedLabel : addLabel;
      }
    });
  }

  function removeCardIfNeeded(productId, added) {
    if (added) {
      return;
    }

    var page = document.querySelector("[data-muukal-wishlist-page]");
    if (!page) {
      return;
    }

    var card = page.querySelector('.muukal-wishlist-card[data-product-id="' + productId + '"]');
    if (card) {
      card.remove();
    }

    if (!page.querySelector(".muukal-wishlist-card")) {
      window.location.reload();
    }
  }

  document.addEventListener("click", function (event) {
    var button = event.target.closest(".muukal-wishlist-button");
    if (!button || button.disabled || !window.muukalWishlist) {
      return;
    }

    event.preventDefault();

    var productId = button.getAttribute("data-product-id");
    if (!productId) {
      return;
    }

    button.disabled = true;
    button.classList.add("is-loading");

    var body = new URLSearchParams();
    body.set("action", "muukal_wishlist_toggle");
    body.set("nonce", window.muukalWishlist.nonce);
    body.set("product_id", productId);

    fetch(window.muukalWishlist.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
      },
      body: body.toString()
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (payload) {
        if (!payload || !payload.success || !payload.data) {
          throw new Error("Wishlist update failed");
        }

        syncButtons(productId, payload.data.added);
        updateCount(payload.data.count || 0);
        removeCardIfNeeded(productId, payload.data.added);
      })
      .catch(function () {
        button.classList.remove("is-loading");
      })
      .finally(function () {
        button.disabled = false;
        button.classList.remove("is-loading");
      });
  });
})();

