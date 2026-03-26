(() => {
  const parseItem = (value) => {
    try {
      return JSON.parse(value || "{}");
    } catch (error) {
      return {};
    }
  };

  const initShowcase = (root) => {
    const modal = root.querySelector(".muukal-buyer-showcase__modal");
    const title = modal?.querySelector("h3");
    const image = modal?.querySelector(".muukal-buyer-showcase__modal-media img");
    const price = modal?.querySelector(".muukal-buyer-showcase__price");
    const description = modal?.querySelector(".muukal-buyer-showcase__description");
    const button = modal?.querySelector(".muukal-buyer-showcase__button");

    if (!modal || !title || !image || !price || !description || !button) {
      return;
    }

    const closeModal = () => {
      modal.hidden = true;
      document.body.classList.remove("muukal-buyer-showcase-modal-open");
    };

    const openModal = (item) => {
      image.src = item.image_url || "";
      image.alt = item.title || "";
      title.textContent = item.title || "";
      price.textContent = item.price || "";
      price.hidden = !item.price;
      description.textContent = item.description || "";
      button.textContent = item.button_label || "View Product";

      if (item.product_url) {
        button.href = item.product_url;
        button.hidden = false;
      } else {
        button.href = "#";
        button.hidden = true;
      }

      modal.hidden = false;
      document.body.classList.add("muukal-buyer-showcase-modal-open");
    };

    root.querySelectorAll(".muukal-buyer-showcase__card").forEach((card) => {
      card.addEventListener("click", () => {
        openModal(parseItem(card.dataset.item));
      });
    });

    modal.addEventListener("click", (event) => {
      const target = event.target;
      if (target instanceof HTMLElement && target.dataset.close === "1") {
        closeModal();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && !modal.hidden) {
        closeModal();
      }
    });
  };

  document.querySelectorAll(".muukal-buyer-showcase").forEach(initShowcase);
})();
