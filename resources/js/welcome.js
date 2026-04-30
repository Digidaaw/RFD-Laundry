document.addEventListener("DOMContentLoaded", function () {
    // Mobile Menu Toggle
    const tabButtons = document.querySelectorAll(".tab-btn");
    const servicesData = window.layanans || [];

    const serviceTitle = document.getElementById("service-title");
    const serviceDesc = document.getElementById("service-desc");
    const serviceFeatures = document.getElementById("service-features");
    const serviceImg = document.getElementById("service-img");

    const menuBtn = document.getElementById('menuBtn');
    const mobileMenu = document.getElementById('mobileMenu');

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');

            const isOpen = menuBtn.getAttribute('aria-expanded') === 'true';
            menuBtn.setAttribute('aria-expanded', !isOpen);
        });
    }

    tabButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const index = this.getAttribute("data-index"); // ✅ FIX
            const service = servicesData[index];

            if (!service) return;

            // Reset semua tab
            tabButtons.forEach((btn) => {
                btn.classList.remove(
                    "active",
                    "bg-brand-dark",
                    "text-white",
                    "border-brand-dark",
                );
                btn.classList.add(
                    "bg-white",
                    "text-brand-dark",
                    "border-gray-200",
                );
            });

            // Aktifkan tab ini
            this.classList.add(
                "active",
                "bg-brand-dark",
                "text-white",
                "border-brand-dark",
            );
            this.classList.remove(
                "bg-white",
                "text-brand-dark",
                "border-gray-200",
            );

            // Update konten
            serviceTitle.textContent = service.name;
            serviceDesc.textContent = service.deskripsi ?? "-";

            // Features sementara
            serviceFeatures.innerHTML = `
                <div class="text-gray-500 text-sm">
                    Layanan profesional dengan kualitas terbaik
                </div>
            `;

            // Update gambar
            if (service.gambar && service.gambar.length > 0) {
                serviceImg.src = `/images/layanan/${service.gambar[0]}`;
            }
        });
    });

    // Smooth Scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });

                // Close mobile menu if open
                if (mobileMenu && !mobileMenu.classList.contains("hidden")) {
                    mobileMenu.classList.add("hidden");
                    menuBtn.setAttribute("aria-expanded", "false");
                    const icon = menuBtn.querySelector("i");
                    icon.classList.remove("ph-x");
                    icon.classList.add("ph-list");
                }
            }
        });
    });

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("animate-in");
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll(".stat-number, .group").forEach((el) => {
        observer.observe(el);
    });

    // Stats counter animation
    const statsSection = document.querySelector(".stat-number");
    if (statsSection) {
        const statsObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        statsObserver.disconnect();
                    }
                });
            },
            { threshold: 0.5 },
        );

        statsObserver.observe(statsSection.parentElement.parentElement);
    }

    function animateCounters() {
        const counters = document.querySelectorAll(".stat-number");
        counters.forEach((counter) => {
            const target = parseInt(counter.textContent);
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 30);
        });
    }
});
