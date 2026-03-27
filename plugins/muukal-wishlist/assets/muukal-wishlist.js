(function () {
  function getConfig() {
    return window.muukalWishlist || null;
  }

  function getSavedItems() {
    var config = getConfig();
    return config && Array.isArray(config.items) ? config.items.map(Number) : [];
  }

  function setSavedItems(items) {
    var config = getConfig();
    if (config) {
      config.items = items.map(Number);
    }
  }

  function updateCount(count) {
    document.querySelectorAll("[data-muukal-wishlist-count]").forEach(function (node) {
      node.textContent = String(count);
    });
  }

  function resolveProductId(button) {
    if (!button) {
      return 0;
    }

    var ownId = Number(button.getAttribute("data-product-id") || 0);
    if (ownId > 0) {
      return ownId;
    }

    var scope = button.closest("[data-product-id]");
    return scope ? Number(scope.getAttribute("data-product-id") || 0) : 0;
  }

  function setButtonState(button, productId, isSaved) {
    if (!button) {
      return;
    }

    button.classList.toggle("is-saved", isSaved);
    button.setAttribute("aria-pressed", isSaved ? "true" : "false");

    if (!button.getAttribute("data-product-id") && productId > 0) {
      button.setAttribute("data-product-id", String(productId));
    }

    if (button.matches(".add-wishlist-btn")) {
      var config = getConfig();
      if (!button.dataset.defaultLabel) {
        button.dataset.defaultLabel = button.textContent.trim() || (config && config.labels ? config.labels.add : "ADD TO WISHLIST");
      }

      button.textContent = isSaved && config && config.labels ? config.labels.added : button.dataset.defaultLabel;
      return;
    }

    if (button.matches("[data-muukal-wishlist-button='manual']")) {
      var label = button.querySelector(".muukal-wishlist-manual-button__label");
      if (label) {
        var addLabel = button.getAttribute("data-label-add") || "ADD TO WISHLIST";
        var addedLabel = button.getAttribute("data-label-added") || "SAVED";
        label.textContent = isSaved ? addedLabel : addLabel;
      }
    }
  }

  function syncProductButtons(productId, isSaved) {
    document.querySelectorAll(".add-wishlist-btn, .muukal-card-wishlist, [data-muukal-wishlist-button='manual']").forEach(function (button) {
      if (resolveProductId(button) === productId) {
        setButtonState(button, productId, isSaved);
      }
    });
  }

  function hydrateButtons() {
    var savedItems = getSavedItems();
    document.querySelectorAll(".add-wishlist-btn, .muukal-card-wishlist, [data-muukal-wishlist-button='manual']").forEach(function (button) {
      var productId = resolveProductId(button);
      if (productId > 0) {
        setButtonState(button, productId, savedItems.indexOf(productId) !== -1);
      }
    });
    updateCount(savedItems.length);
  }

  function isRegionEnabled(button) {
    var config = getConfig();
    if (!config || !config.regions) {
      return true;
    }

    if (button.matches(".add-wishlist-btn")) {
      return !!config.regions.productDetail;
    }

    if (button.matches(".muukal-card-wishlist")) {
      return !!config.regions.productCards;
    }

    if (button.matches("[data-muukal-wishlist-button='manual']")) {
      return !!config.regions.shortcodes;
    }

    return true;
  }

  function removeCardIfNeeded(productId, isSaved) {
    if (isSaved) {
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
    var button = event.target.closest(".add-wishlist-btn, .muukal-card-wishlist, [data-muukal-wishlist-button='manual']");
    var config = getConfig();

    if (!button || !config || button.disabled || !isRegionEnabled(button)) {
      return;
    }

    var productId = resolveProductId(button);
    if (productId < 1) {
      return;
    }

    event.preventDefault();

    button.disabled = true;
    button.classList.add("is-loading");

    var body = new URLSearchParams();
    body.set("action", "muukal_wishlist_toggle");
    body.set("nonce", config.nonce);
    body.set("product_id", String(productId));

    fetch(config.ajaxUrl, {
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

        setSavedItems(payload.data.items || []);
        syncProductButtons(productId, !!payload.data.added);
        updateCount(Number(payload.data.count || 0));
        removeCardIfNeeded(productId, !!payload.data.added);
      })
      .finally(function () {
        button.disabled = false;
        button.classList.remove("is-loading");
      });
  });

  document.addEventListener("DOMContentLoaded", hydrateButtons);
})();
