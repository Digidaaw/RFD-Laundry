document.addEventListener("DOMContentLoaded", () => {
  const servicesData = {
    stone: {
      title: "Premium Stone Washing",
      desc:
        "Our stone washing service utilizes high-quality pumice stones to accelerate the fading process and increase the softness of denim and heavy cottons. This classic technique provides a naturally worn, vintage appearance that consumers love, carefully monitored to maintain fabric integrity.",
      img: "https://images.unsplash.com/photo-1444312645910-ffa973656eba?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      features: [
        "Authentic Vintage Fades",
        "Fabric Softening",
        "Custom Abrasion Levels",
        "Pumice Stone Sourcing",
      ],
    },
    sand: {
      title: "Specialized Sand Washing",
      desc:
        "By substituting stones with fine sand or micro-abrasives, sand washing delivers an exceptionally soft, peach-skin finish. Perfect for delicate fabrics like silks or fine cottons where stones would be too harsh, giving a subtle, dusty appearance.",
      img: "https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      features: [
        "Peach-Skin Softness",
        "Safe for Delicates",
        "Micro-Abrasion",
        "Subtle Distressing",
      ],
    },
    enzyme: {
      title: "Biowashing & Enzyme Treatment",
      desc:
        "Using organic cellulase enzymes, we digest protruding fibers to create a smoother fabric surface, prevent pilling, and enhance color retention. An eco-friendly alternative to aggressive stone washing that preserves the strength of the garment.",
      img: "https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      features: [
        "Prevents Pilling",
        "Eco-Friendly Process",
        "Maintains Fabric Strength",
        "Enhances Color",
      ],
    },
    bleach: {
      title: "Industrial Bleach Washing",
      desc:
        "Controlled chemical washing using oxidative agents to dramatically lighten fabrics or create distinct high-contrast fades. Our process is strictly neutralized post-wash to ensure the garment remains safe and tear-resistant.",
      img: "https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      features: [
        "High-Contrast Fading",
        "Uniform Lightening",
        "Strict Neutralization",
        "Color Stripping",
      ],
    },
    garment: {
      title: "Standard Garment Washing",
      desc:
        "A fundamental wet processing step that removes sizing, dirt, and shrinkage from manufactured garments. We use specialized industrial detergents to pre-shrink and soften apparel, preparing it for retail distribution.",
      img: "https://images.unsplash.com/photo-1444312645910-ffa973656eba?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      features: [
        "Sizing Removal",
        "Pre-shrinking",
        "Bulk Processing",
        "Retail Ready",
      ],
    },
  };

  const switchTab = (type, event) => {
    const clicked = event?.currentTarget;
    if (!clicked) return;

    document.querySelectorAll(".tab-btn").forEach((btn) => {
      btn.classList.remove("bg-brand-dark", "text-white", "border-brand-dark");
      btn.classList.add("bg-white", "text-brand-dark", "border-gray-200");
    });

    clicked.classList.remove("bg-white", "border-gray-200");
    clicked.classList.add("bg-brand-dark", "text-white", "border-brand-dark");

    const contentDiv = document.getElementById("service-content");
    const imgEl = document.getElementById("service-img");
    if (!contentDiv || !imgEl) return;

    contentDiv.style.transition = "opacity 0.3s ease";
    imgEl.style.transition = "opacity 0.3s ease";
    contentDiv.style.opacity = "0";
    imgEl.style.opacity = "0";

    setTimeout(() => {
      const data = servicesData[type];
      if (!data) return;

      document.getElementById("service-title").textContent = data.title;
      document.getElementById("service-desc").textContent = data.desc;
      imgEl.src = data.img;

      const featuresContainer = document.querySelector("#service-content .grid");
      if (featuresContainer) {
        featuresContainer.innerHTML = "";
        data.features.forEach((feature) => {
          const item = document.createElement("div");
          item.className = "flex items-center gap-3";
          item.innerHTML =
            '<div class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark"><i class="ph ph-check font-bold"></i></div>' +
            `<span class="text-sm font-medium text-gray-700">${feature}</span>`;
          featuresContainer.appendChild(item);
        });
      }

      contentDiv.style.opacity = "1";
      imgEl.style.opacity = "1";
    }, 300);
  };

  const tabs = document.querySelectorAll("#service-tabs .tab-btn");
  tabs.forEach((button) => {
    const type = button.dataset.service;
    if (!type) return;
    button.addEventListener("click", (event) => switchTab(type, event));
  });

  const menuBtn = document.getElementById("menuBtn");
  const mobileMenu = document.getElementById("mobileMenu");

  if (menuBtn && mobileMenu) {
    menuBtn.addEventListener("click", () => {
      const expanded = menuBtn.getAttribute("aria-expanded") === "true";
      menuBtn.setAttribute("aria-expanded", String(!expanded));
      mobileMenu.classList.toggle("hidden");
    });

    mobileMenu.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        mobileMenu.classList.add("hidden");
        menuBtn.setAttribute("aria-expanded", "false");
      });
    });
  }
});