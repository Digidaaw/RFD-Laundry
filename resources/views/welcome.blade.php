<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RFD Laundry - Industrial Garment Washing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600&display=swap"
      rel="stylesheet" />
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              "brand-yellow": "#FCE844",
              "brand-dark": "#0F1115",
              "brand-gray": "#F8F9FA",
            },
            fontFamily: {
                      heading: ["Poppins", "sans-serif"],
              body: ["Inter", "sans-serif"],
            },
          },
        },
      };
    </script>
    @vite(['resources/css/welcome.css', 'resources/js/welcome.js'])
  </head>
  <body class="bg-white text-gray-800 antialiased overflow-x-hidden">
    <nav
      class="bg-brand-dark text-white px-6 md:px-12 py-4 flex justify-between items-center sticky top-0 z-50">
      <div class="flex items-center gap-8">
        <a
          href="#"
          class="text-2xl font-black tracking-tighter uppercase font-heading flex flex-col leading-none">
          <span>RFD</span>
          <span class="text-xs tracking-widest text-brand-yellow">Laundry</span>
        </a>
        <div class="hidden md:flex items-center gap-6 text-sm font-medium ml-8">
          <a href="#" class="hover:text-brand-yellow transition-colors">Home</a>
          <a
            href="#services"
            class="hover:text-brand-yellow transition-colors flex items-center gap-1"
            >Services <i class="ph ph-caret-down"></i
          ></a>
          <a href="#about" class="hover:text-brand-yellow transition-colors"
            >About Us</a
          >
        </div>
      </div>
      <div class="hidden md:flex items-center gap-4">
        <a
          href="#footer"
          class="bg-white text-brand-dark px-6 py-2 rounded-full font-bold text-sm hover:bg-brand-yellow transition-colors"
          >Contact Us</a
        >
      </div>
      <button id="menuBtn" aria-expanded="false" aria-controls="mobileMenu" class="md:hidden text-2xl focus:outline-none">
        <i class="ph ph-list"></i>
      </button>
      <div id="mobileMenu" class="hidden md:hidden absolute inset-x-0 top-full bg-brand-dark text-white border-t border-gray-700 shadow-lg">
        <div class="flex flex-col px-6 py-4 gap-3">
          <a href="#" class="py-2 hover:text-brand-yellow transition-colors">Home</a>
          <a href="#services" class="py-2 hover:text-brand-yellow transition-colors">Services</a>
          <a href="#about" class="py-2 hover:text-brand-yellow transition-colors">About Us</a>
          <a href="#footer" class="py-2 bg-white text-brand-dark rounded-full text-center font-bold hover:bg-brand-yellow transition-colors">Contact Us</a>
        </div>
      </div>
    </nav>
    <header class="relative min-h-screen flex items-center">
      <div class="absolute inset-0">
        <img
          src="{{ asset('images/Gemini_Generated_Image_f3wfemf3wfemf3wf.png') }}"
          alt="Laundry Machine"
          class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-black/50"></div>
      </div>
      <div class="relative z-10 max-w-8xl mx-auto px-6 md:px-12 w-full">
        <div class="max-w-2xl">
          <h1
            class="text-5xl md:text-7xl lg:text-9xl font-black leading-tight uppercase text-white mb-6">
            Do Your <br />
            <span class="text-yellow-400">Laundry</span> <br />
            Smartly
          </h1>
          <p
            class="text-gray-200 text-lg md:text-xl mb-10 max-w-lg leading-relaxed">
            Welcome to RFD Laundry Services where we transform your laundry into
            a cleaner and more professional result using modern washing
            technology.
          </p>
          <div class="flex items-center gap-4">
            <a
              href="#services"
              class="bg-yellow-400 text-black px-8 py-4 rounded-full font-bold text-lg hover:bg-yellow-300 transition shadow-lg">
              Start Cleaning
            </a>
          </div>
        </div>
      </div>
    </header>
    <!-- Trust / Features Section -->
    <section class="bg-brand-gray py-20 px-6 md:px-12" id="about">
      <div class="max-w-7xl mx-auto">
        <div
          class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-6">
          <h2
            class="text-3xl md:text-4xl font-black font-heading uppercase max-w-lg leading-tight text-brand-dark">
            Your Trusted Partner In Achieving Pristine Finishes.
          </h2>
          <p class="text-gray-500 max-w-sm text-sm">
            Established with the mission to elevate your apparel's look and
            feel, we bring a blend of modern technology, natural elements, and
            industrial expertise.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div
            class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-brand-yellow transition-colors group">
            <div
              class="w-12 h-12 bg-gray-100 group-hover:bg-brand-yellow transition-colors rounded-xl flex items-center justify-center mb-6">
              <i class="ph ph-truck text-2xl font-bold"></i>
            </div>
            <h3
              class="font-bold text-sm tracking-widest uppercase mb-3 text-brand-dark">
              Large Scale Capacity
            </h3>
            <p class="text-gray-500 text-sm leading-relaxed">
              Equipped with industrial-grade machinery to handle bulk orders
              seamlessly, ensuring your production timeline is met with
              efficiency.
            </p>
          </div>
          <!-- Feature 2 -->
          <div
            class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-brand-yellow transition-colors group">
            <div
              class="w-12 h-12 bg-gray-100 group-hover:bg-brand-yellow transition-colors rounded-xl flex items-center justify-center mb-6">
              <i class="ph ph-leaf text-2xl font-bold"></i>
            </div>
            <h3
              class="font-bold text-sm tracking-widest uppercase mb-3 text-brand-dark">
              Eco-Friendly Bio-Agents
            </h3>
            <p class="text-gray-500 text-sm leading-relaxed">
              We prioritize environmental health by utilizing advanced enzymes
              and eco-friendly bleaching agents that reduce water contamination.
            </p>
          </div>
          <!-- Feature 3 -->
          <div
            class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-brand-yellow transition-colors group">
            <div
              class="w-12 h-12 bg-gray-100 group-hover:bg-brand-yellow transition-colors rounded-xl flex items-center justify-center mb-6">
              <i class="ph ph-drop text-2xl font-bold"></i>
            </div>
            <h3
              class="font-bold text-sm tracking-widest uppercase mb-3 text-brand-dark">
              Specialized Techniques
            </h3>
            <p class="text-gray-500 text-sm leading-relaxed">
              From pumice stone abrasion to delicate sand washes, our experts
              apply precise methodologies to achieve the exact texture and fade
              required.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section class="py-24 px-6 md:px-12 bg-white" id="services">
      <div class="max-w-7xl mx-auto text-center mb-16">
        <h2
          class="text-4xl md:text-5xl font-black font-heading uppercase text-brand-dark mb-4">
          Our Services
        </h2>
        <p class="text-gray-500 max-w-2xl mx-auto">
          Specialized finishing processes designed to give your garments the
          perfect look, feel, and character. We blend natural abrasives with
          cutting-edge chemistry.
        </p>
      </div>

      <div class="max-w-6xl mx-auto">
        <!-- Tabs -->
        <div
          class="flex flex-wrap justify-center gap-3 mb-12"
          id="service-tabs">
          <button
            data-service="stone"
            class="tab-btn active bg-brand-dark text-white px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-brand-dark hover:bg-brand-dark hover:text-white transition-all">
            Stone Washing
          </button>
          <button
            data-service="sand"
            class="tab-btn bg-white text-brand-dark px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-gray-200 hover:border-brand-dark transition-all">
            Sand Washing
          </button>
          <button
            data-service="enzyme"
            class="tab-btn bg-white text-brand-dark px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-gray-200 hover:border-brand-dark transition-all">
            Enzyme & Bio
          </button>
          <button
            data-service="bleach"
            class="tab-btn bg-white text-brand-dark px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-gray-200 hover:border-brand-dark transition-all">
            Bleach Wash
          </button>
          <button
            data-service="garment"
            class="tab-btn bg-white text-brand-dark px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-gray-200 hover:border-brand-dark transition-all">
            Garment Wash
          </button>
        </div>

        <!-- Content Area -->
        <div
          class="bg-brand-gray p-8 md:p-12 rounded-3xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center min-h-[500px]">
          <!-- Dynamic Content Container -->
          <div id="service-content">
            <h3
              class="text-3xl font-black font-heading uppercase mb-4 text-brand-dark"
              id="service-title">
              Premium Stone Washing
            </h3>
            <p class="text-gray-600 mb-8 leading-relaxed" id="service-desc">
              Our stone washing service utilizes high-quality pumice stones to
              accelerate the fading process and increase the softness of denim
              and heavy cottons. This classic technique provides a naturally
              worn, vintage appearance that consumers love, carefully monitored
              to maintain fabric integrity.
            </p>

            <h4
              class="font-bold text-sm tracking-widest uppercase mb-4 text-brand-dark">
              What We Offer
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
              <div class="flex items-center gap-3">
                <div
                  class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark">
                  <i class="ph ph-check font-bold"></i>
                </div>
                <span class="text-sm font-medium text-gray-700"
                  >Authentic Vintage Fades</span
                >
              </div>
              <div class="flex items-center gap-3">
                <div
                  class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark">
                  <i class="ph ph-check font-bold"></i>
                </div>
                <span class="text-sm font-medium text-gray-700"
                  >Fabric Softening</span
                >
              </div>
              <div class="flex items-center gap-3">
                <div
                  class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark">
                  <i class="ph ph-check font-bold"></i>
                </div>
                <span class="text-sm font-medium text-gray-700"
                  >Custom Abrasion Levels</span
                >
              </div>
              <div class="flex items-center gap-3">
                <div
                  class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark">
                  <i class="ph ph-check font-bold"></i>
                </div>
                <span class="text-sm font-medium text-gray-700"
                  >Pumice Stone Sourcing</span
                >
              </div>
            </div>

            <a
              href="#"
              class="inline-block bg-brand-yellow text-brand-dark px-8 py-3.5 rounded-full font-bold hover:bg-yellow-300 transition-colors"
              >Request Sample</a
            >
          </div>

          <!-- Dynamic Image -->
          <div
            class="relative h-full min-h-[300px] md:min-h-[400px] rounded-2xl overflow-hidden border-4 border-white shadow-xl">
            <img
              id="service-img"
              src="https://images.unsplash.com/photo-1444312645910-ffa973656eba?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
              alt="Pumice Stones for Washing"
              class="absolute inset-0 w-full h-full object-cover" />
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 border-y border-gray-100 bg-white">
      <div
        class="max-w-6xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-12 text-center md:text-left">
        <div>
          <h3 class="text-5xl font-black font-heading mb-2 text-brand-dark">
            15<span class="text-brand-yellow">+</span>
          </h3>
          <p class="text-gray-500 text-sm">
            Years of industrial washing experience.
          </p>
        </div>
        <div>
          <h3 class="text-5xl font-black font-heading mb-2 text-brand-dark">
            99<span class="text-brand-yellow">%</span>
          </h3>
          <p class="text-gray-500 text-sm">
            Customer satisfaction rate from apparel brands.
          </p>
        </div>
        <div>
          <h3 class="text-5xl font-black font-heading mb-2 text-brand-dark">
            100<span class="text-brand-yellow">%</span>
          </h3>
          <p class="text-gray-500 text-sm">
            Eco-friendly compliance on bio-agents.
          </p>
        </div>
        <div>
          <h3 class="text-5xl font-black font-heading mb-2 text-brand-dark">
            50<span class="text-brand-yellow">k</span>
          </h3>
          <p class="text-gray-500 text-sm">
            Garments processed weekly capacity.
          </p>
        </div>
      </div>
    </section>

<section class="py-24 px-6 md:px-12 bg-gray-50" id="facility">
  <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
    <div class="flex justify-center lg:justify-start w-full h-full">
      <img
        src="{{ asset('images/c880f6b57501b35c61a9f31b18333552.jpg') }}"
        alt="Laundry Machine"
        class="w-full max-w-md rounded-2xl object-cover shadow-lg"
      >
    </div>
    <div>
      <h2 class="text-4xl md:text-5xl font-black uppercase text-gray-900 mb-4">
        Why Choose Us?
      </h2>
      <p class="text-gray-500 mb-10 max-w-lg">
        Experience the ultimate convenience and quality with Rubobas Laundry
        Services. Wash & Fold services designed to save your time and care for
        your garments.
      </p>
      <div class="space-y-6">
        <div class="flex items-start gap-4 bg-white p-6 rounded-xl shadow-sm">
          <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center shrink-0">
            ●
          </div>
          <div>
            <h4 class="font-bold uppercase text-gray-900 mb-1">
              Time-Saving
            </h4>
            <p class="text-sm text-gray-500">
              From pickup to delivery, our process is designed to save your
              valuable time while ensuring clean results.
            </p>
          </div>
        </div>
        <div class="flex items-start gap-4 bg-white p-6 rounded-xl shadow-sm">
          <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center shrink-0">
            ●
          </div>
          <div>
            <h4 class="font-bold uppercase text-gray-900 mb-1">
              High-Quality Care
            </h4>
            <p class="text-sm text-gray-500">
              Our professionals handle your clothes with premium detergents
              ensuring they look and feel their best.
            </p>
          </div>
        </div>
        <div class="flex items-start gap-4 bg-white p-6 rounded-xl shadow-sm">
          <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center shrink-0">
            ●
          </div>
          <div>
            <h4 class="font-bold uppercase text-gray-900 mb-1">
              Eco-Friendly Products
            </h4>
            <p class="text-sm text-gray-500">
              We use safe and environmentally friendly detergents that are
              tough on stains but gentle on fabrics.
            </p>
          </div>
        </div>
      </div>
      <div class="mt-10">
        <a
          href="#"
          class="inline-block bg-yellow-400 text-black px-8 py-3 rounded-full font-semibold hover:bg-yellow-300 transition"
        >
          Start Cleaning
        </a>
      </div>
    </div>
  </div>
</section>

    <!-- Testimonial -->
    <section id="sustainability"
      class="bg-brand-dark text-white py-24 px-6 md:px-12 relative overflow-hidden">
      <!-- Decoration -->
      <div
        class="absolute top-0 right-0 w-64 h-64 bg-brand-yellow/10 rounded-full blur-3xl"></div>

      <div class="max-w-4xl mx-auto text-center relative z-10">
        <i
          class="ph-fill ph-quotes text-5xl text-brand-yellow mb-8 block opacity-50 mx-auto"></i>
        <h3
          class="text-2xl md:text-3xl font-heading font-medium leading-snug mb-10 uppercase tracking-wide">
          "We've been using RFD Laundry for our denim line's stone washing for
          months now, and I couldn't be happier. The texture, the fade
          consistency, and their respect for the fabric integrity is
          consistently excellent."
        </h3>
        <div class="flex items-center justify-center gap-4">
          <img
            src="{{ asset('images/Dance Sunglasses GIF - Find & Share on GIPHY.gif') }}"
            alt="Client"
            class="w-12 h-12 rounded-full border-2 border-brand-yellow object-cover" />
          <div class="text-left">
            <p class="font-bold uppercase tracking-wider text-sm">
              Mark Thompson
            </p>
            <p class="text-xs text-gray-400">
              Production Manager, Urban Denim Co.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section
      class="relative py-24 px-6 md:px-12 bg-black overflow-hidden flex items-center justify-center min-h-[400px]">
      <!-- Background Image with Overlay -->
      <img
        src="{{ asset('images/1111225ccfad56d7152ab875eb710239.jpg') }}"
        alt="Industrial Washers"
        class="absolute inset-0 w-full h-full object-cover opacity-30 grayscale" />

      <div class="relative z-10 text-center max-w-2xl">
        <h2
          class="text-4xl md:text-5xl font-black font-heading text-white uppercase mb-6 leading-tight">
          Ready to Experience the
          <span class="text-brand-yellow">Premium Wash</span> Difference?
        </h2>
        <p class="text-gray-300 mb-8">
          Send us a sample batch or discuss your next production run with our
          textile finishing specialists.
        </p>
        <a
          href="#"
          class="inline-block bg-brand-yellow text-brand-dark px-10 py-4 rounded-full font-bold hover:bg-yellow-300 transition-colors uppercase tracking-wide">
          Get a Custom Quote
        </a>
      </div>
    </section>

    <!-- Footer -->
    <footer id="footer" class="bg-white border-t border-gray-100 pt-16 pb-8 px-6 md:px-12">
      <div
        class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
        <div class="col-span-1 md:col-span-1">
          <a
            href="#"
            class="text-2xl font-black tracking-tighter uppercase font-heading flex flex-col leading-none mb-6">
            <span class="text-brand-dark">RFD</span>
            <span class="text-xs tracking-widest text-gray-500">Laundry</span>
          </a>
          <p class="text-sm text-gray-500 leading-relaxed mb-6">
            Specialized industrial garment washing services providing stone,
            sand, enzyme, and bleach finishing for the apparel industry.
          </p>
          <div class="flex gap-4">
            <a
              href="https://www.instagram.com/rfdlaundry/"
              class="w-10 h-10 rounded-full bg-brand-gray flex items-center justify-center text-brand-dark hover:bg-brand-yellow transition-colors"
              ><i class="ph-fill ph-instagram-logo text-xl"></i
            ></a>
            <a
              href="https://www.instagram.com/rfdlaundry/"
              class="w-10 h-10 rounded-full bg-brand-gray flex items-center justify-center text-brand-dark hover:bg-brand-yellow transition-colors"
              ><i class="ph-fill ph-facebook-logo text-xl"></i
            ></a>
            <a
              href="https://www.instagram.com/rfdlaundry/"
              class="w-10 h-10 rounded-full bg-brand-gray flex items-center justify-center text-brand-dark hover:bg-brand-yellow transition-colors"
              ><i class="ph-fill ph-linkedin-logo text-xl"></i
            ></a>
          </div>
        </div>

        <div>
          <h4
            class="font-bold text-brand-dark uppercase tracking-widest text-sm mb-6">
            Services
          </h4>
          <ul class="space-y-3 text-sm text-gray-500">
            <li>
              <a href="#services" class="hover:text-brand-yellow transition-colors"
                >Stone Washing</a
              >
            </li>
            <li>
              <a href="#services" class="hover:text-brand-yellow transition-colors"
                >Sand Washing</a
              >
            </li>
            <li>
              <a href="#services" class="hover:text-brand-yellow transition-colors"
                >Enzyme Washing</a
              >
            </li>
            <li>
              <a href="#services" class="hover:text-brand-yellow transition-colors"
                >Bleach Washing</a
              >
            </li>
            <li>
              <a href="#services" class="hover:text-brand-yellow transition-colors"
                >Garment Washing</a
              >
            </li>
          </ul>
        </div>

        <div>
          <h4
            class="font-bold text-brand-dark uppercase tracking-widest text-sm mb-6">
            Company
          </h4>
          <ul class="space-y-3 text-sm text-gray-500">
            <li>
              <a href="#about" class="hover:text-brand-yellow transition-colors"
                >About Us</a
              >
            </li>
            <li>
              <a href="#facility" class="hover:text-brand-yellow transition-colors"
                >Facility</a
              >
            </li>
            <li>
              <a href="#sustainability" class="hover:text-brand-yellow transition-colors"
                >Sustainability</a
              >
            </li>
            <li>
              <a href="#footer" class="hover:text-brand-yellow transition-colors"
                >Contact</a
              >
            </li>
          </ul>
        </div>

        <div>
          <h4
            class="font-bold text-brand-dark uppercase tracking-widest text-sm mb-6">
            Newsletter
          </h4>
          <p class="text-sm text-gray-500 mb-4">
            Subscribe for updates on our latest finishing techniques.
          </p>
          <div class="flex">
            <input
              type="email"
              placeholder="Email Address"
              class="bg-brand-gray px-4 py-3 rounded-l-lg outline-none w-full text-sm border border-gray-200" />
            <button
              class="bg-brand-dark text-white px-4 py-3 rounded-r-lg hover:bg-gray-800 transition-colors">
              <i class="ph ph-paper-plane-right"></i>
            </button>
          </div>
        </div>
      </div>

      <div
        class="max-w-7xl mx-auto border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-400 font-medium uppercase tracking-wider">
        <p>&copy; 2026 RFD Laundry Services. All Rights Reserved.</p>
        <div class="flex gap-6">
          <a href="#" class="hover:text-brand-dark transition-colors"
            >Terms of Service</a
          >
          <a href="#" class="hover:text-brand-dark transition-colors"
            >Privacy Policy</a
          >
        </div>
      </div>
    </footer>

  </body>
</html>
