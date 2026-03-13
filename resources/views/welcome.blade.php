<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFD Laundry - Industrial Garment Washing</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-yellow': '#FCE844',
                        'brand-dark': '#0F1115',
                        'brand-gray': '#F8F9FA'
                    },
                    fontFamily: {
                        heading: ['Montserrat', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Montserrat', sans-serif; }
        .clip-diagonal { clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%); }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased overflow-x-hidden">

    <!-- Navigation -->
    <nav class="bg-brand-dark text-white px-6 md:px-12 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <a href="#" class="text-2xl font-black tracking-tighter uppercase font-heading flex flex-col leading-none">
                <span>RFD</span>
                <span class="text-xs tracking-widest text-brand-yellow">Laundry</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium ml-8">
                <a href="#" class="hover:text-brand-yellow transition-colors">Home</a>
                <a href="#services" class="hover:text-brand-yellow transition-colors flex items-center gap-1">Services <i class="ph ph-caret-down"></i></a>
                <a href="#about" class="hover:text-brand-yellow transition-colors">About Us</a>
            </div>
        </div>
        <div class="hidden md:flex items-center gap-4">
            <a href="#" class="bg-white text-brand-dark px-6 py-2 rounded-full font-bold text-sm hover:bg-brand-yellow transition-colors">Contact Us</a>
        </div>
        <!-- Mobile Menu Button -->
        <button class="md:hidden text-2xl"><i class="ph ph-list"></i></button>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-12 pb-24 md:pt-20 md:pb-32 px-6 md:px-12 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="z-10">
            <h1 class="text-5xl md:text-7xl font-black font-heading leading-[1.05] tracking-tight text-brand-dark mb-6 uppercase">
                Elevate Your <br>
                <span class="text-brand-yellow">Garments</span> <br>
                Smartly
            </h1>
            <p class="text-gray-500 max-w-md mb-8 leading-relaxed">
                Welcome to RFD Laundry Services, where we transform raw textiles into premium finished garments. Specializing in stone, sand, and enzyme washing.
            </p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="#services" class="bg-brand-yellow text-brand-dark px-8 py-3.5 rounded-full font-bold hover:bg-yellow-300 transition-colors shadow-lg shadow-yellow-200">Explore Services</a>
                <a href="#" class="flex items-center gap-3 font-semibold text-brand-dark hover:text-gray-600 transition-colors border border-gray-200 px-6 py-3 rounded-full">
                    <i class="ph-fill ph-play-circle text-2xl"></i>
                    Watch Video
                </a>
            </div>
        </div>
        <div class="relative z-0 mt-8 lg:mt-0">
            <!-- Decorative Yellow Accent -->
            <div class="absolute -bottom-6 -left-6 w-full h-full bg-brand-yellow z-0 border-4 border-brand-dark"></div>
            <!-- Main Hero Image -->
            <img src="https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Denim Garment Washing" class="relative z-10 w-full h-[500px] object-cover border-4 border-brand-dark grayscale-[20%]">
        </div>
    </header>

    <!-- Trust / Features Section -->
    <section class="bg-brand-gray py-20 px-6 md:px-12" id="about">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-6">
                <h2 class="text-3xl md:text-4xl font-black font-heading uppercase max-w-lg leading-tight text-brand-dark">
                    Your Trusted Partner In Achieving Pristine Finishes.
                </h2>
                <p class="text-gray-500 max-w-sm text-sm">
                    Established with the mission to elevate your apparel's look and feel, we bring a blend of modern technology, natural elements, and industrial expertise.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-brand-yellow transition-colors group">
                    <div class="w-12 h-12 bg-gray-100 group-hover:bg-brand-yellow transition-colors rounded-xl flex items-center justify-center mb-6">
                        <i class="ph ph-truck text-2xl font-bold"></i>
                    </div>
                    <h3 class="font-bold text-sm tracking-widest uppercase mb-3 text-brand-dark">Large Scale Capacity</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Equipped with industrial-grade machinery to handle bulk orders seamlessly, ensuring your production timeline is met with efficiency.
                    </p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-brand-yellow transition-colors group">
                    <div class="w-12 h-12 bg-gray-100 group-hover:bg-brand-yellow transition-colors rounded-xl flex items-center justify-center mb-6">
                        <i class="ph ph-leaf text-2xl font-bold"></i>
                    </div>
                    <h3 class="font-bold text-sm tracking-widest uppercase mb-3 text-brand-dark">Eco-Friendly Bio-Agents</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        We prioritize environmental health by utilizing advanced enzymes and eco-friendly bleaching agents that reduce water contamination.
                    </p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-brand-yellow transition-colors group">
                    <div class="w-12 h-12 bg-gray-100 group-hover:bg-brand-yellow transition-colors rounded-xl flex items-center justify-center mb-6">
                        <i class="ph ph-drop text-2xl font-bold"></i>
                    </div>
                    <h3 class="font-bold text-sm tracking-widest uppercase mb-3 text-brand-dark">Specialized Techniques</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        From pumice stone abrasion to delicate sand washes, our experts apply precise methodologies to achieve the exact texture and fade required.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-24 px-6 md:px-12 bg-white" id="services">
        <div class="max-w-7xl mx-auto text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black font-heading uppercase text-brand-dark mb-4">Our Services</h2>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Specialized finishing processes designed to give your garments the perfect look, feel, and character. We blend natural abrasives with cutting-edge chemistry.
            </p>
        </div>

        <div class="max-w-6xl mx-auto">
            <!-- Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mb-12" id="service-tabs">
                <button onclick="switchTab('stone')" class="tab-btn active bg-brand-dark text-white px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-brand-dark hover:bg-brand-dark hover:text-white transition-all">Stone Washing</button>
                <button onclick="switchTab('sand')" class="tab-btn bg-white text-brand-dark px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-gray-200 hover:border-brand-dark transition-all">Sand Washing</button>
                <button onclick="switchTab('enzyme')" class="tab-btn bg-white text-brand-dark px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-gray-200 hover:border-brand-dark transition-all">Enzyme & Bio</button>
                <button onclick="switchTab('bleach')" class="tab-btn bg-white text-brand-dark px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-gray-200 hover:border-brand-dark transition-all">Bleach Wash</button>
                <button onclick="switchTab('garment')" class="tab-btn bg-white text-brand-dark px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 border-gray-200 hover:border-brand-dark transition-all">Garment Wash</button>
            </div>

            <!-- Content Area -->
            <div class="bg-brand-gray p-8 md:p-12 rounded-3xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center min-h-[500px]">
                
                <!-- Dynamic Content Container -->
                <div id="service-content">
                    <h3 class="text-3xl font-black font-heading uppercase mb-4 text-brand-dark" id="service-title">Premium Stone Washing</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed" id="service-desc">
                        Our stone washing service utilizes high-quality pumice stones to accelerate the fading process and increase the softness of denim and heavy cottons. This classic technique provides a naturally worn, vintage appearance that consumers love, carefully monitored to maintain fabric integrity.
                    </p>
                    
                    <h4 class="font-bold text-sm tracking-widest uppercase mb-4 text-brand-dark">What We Offer</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark"><i class="ph ph-check font-bold"></i></div>
                            <span class="text-sm font-medium text-gray-700">Authentic Vintage Fades</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark"><i class="ph ph-check font-bold"></i></div>
                            <span class="text-sm font-medium text-gray-700">Fabric Softening</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark"><i class="ph ph-check font-bold"></i></div>
                            <span class="text-sm font-medium text-gray-700">Custom Abrasion Levels</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark"><i class="ph ph-check font-bold"></i></div>
                            <span class="text-sm font-medium text-gray-700">Pumice Stone Sourcing</span>
                        </div>
                    </div>
                    
                    <a href="#" class="inline-block bg-brand-yellow text-brand-dark px-8 py-3.5 rounded-full font-bold hover:bg-yellow-300 transition-colors">Request Sample</a>
                </div>

                <!-- Dynamic Image -->
                <div class="relative h-full min-h-[300px] md:min-h-[400px] rounded-2xl overflow-hidden border-4 border-white shadow-xl">
                    <img id="service-img" src="https://images.unsplash.com/photo-1444312645910-ffa973656eba?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Pumice Stones for Washing" class="absolute inset-0 w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 border-y border-gray-100 bg-white">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-12 text-center md:text-left">
            <div>
                <h3 class="text-5xl font-black font-heading mb-2 text-brand-dark">15<span class="text-brand-yellow">+</span></h3>
                <p class="text-gray-500 text-sm">Years of industrial washing experience.</p>
            </div>
            <div>
                <h3 class="text-5xl font-black font-heading mb-2 text-brand-dark">99<span class="text-brand-yellow">%</span></h3>
                <p class="text-gray-500 text-sm">Customer satisfaction rate from apparel brands.</p>
            </div>
            <div>
                <h3 class="text-5xl font-black font-heading mb-2 text-brand-dark">100<span class="text-brand-yellow">%</span></h3>
                <p class="text-gray-500 text-sm">Eco-friendly compliance on bio-agents.</p>
            </div>
            <div>
                <h3 class="text-5xl font-black font-heading mb-2 text-brand-dark">50<span class="text-brand-yellow">k</span></h3>
                <p class="text-gray-500 text-sm">Garments processed weekly capacity.</p>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-24 px-6 md:px-12 bg-white">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            
            <!-- Left: Industrial Porthole Image -->
            <div class="relative flex justify-center">
                <!-- Outer Ring mimicking a washing machine -->
                <div class="w-[320px] h-[320px] md:w-[450px] md:h-[450px] rounded-full border-[16px] border-brand-gray shadow-2xl relative flex items-center justify-center p-4 bg-gray-300">
                    <div class="absolute inset-0 rounded-full border-4 border-gray-400 m-2"></div>
                    <div class="w-full h-full rounded-full overflow-hidden relative">
                        <!-- Inner image of industrial textures (Sand/Water/Denim) -->
                        <img src="https://i.pinimg.com/736x/d0/b1/37/d0b137870a56ef57ed7986dfcb638b9c.jpg" alt="Sand Texture" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-blue-900/20 mix-blend-overlay"></div>
                    </div>
                    <!-- Handle Detail -->
                    <div class="absolute -right-6 top-1/2 -translate-y-1/2 w-8 h-24 bg-gray-400 rounded-l-none rounded-r-xl border-y-4 border-r-4 border-gray-300 shadow-lg"></div>
                </div>
            </div>

            <!-- Right: Content -->
            <div>
                <h2 class="text-4xl md:text-5xl font-black font-heading uppercase text-brand-dark mb-4">Why Choose Us?</h2>
                <p class="text-gray-500 mb-10">
                    Experience the ultimate in garment finishing quality with RFD Laundry. We combine raw natural materials with advanced machinery.
                </p>

                <div class="space-y-6">
                    <div class="flex gap-6 p-6 rounded-2xl border border-gray-100 hover:shadow-md transition-shadow bg-white">
                        <div class="w-12 h-12 bg-brand-dark text-white rounded-full flex items-center justify-center shrink-0">
                            <i class="ph ph-clock text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold uppercase tracking-wide text-brand-dark mb-1">Time-Saving Turnaround</h4>
                            <p class="text-sm text-gray-500">Our streamlined facility ensures bulk orders are processed swiftly without sacrificing finish quality.</p>
                        </div>
                    </div>

                    <div class="flex gap-6 p-6 rounded-2xl border border-gray-100 hover:shadow-md transition-shadow bg-white">
                        <div class="w-12 h-12 bg-brand-dark text-white rounded-full flex items-center justify-center shrink-0">
                            <i class="ph ph-medal text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold uppercase tracking-wide text-brand-dark mb-1">High-Quality Abrasives</h4>
                            <p class="text-sm text-gray-500">We source premium pumice stones and specific silica sands to guarantee the exact abrasion and distressing desired.</p>
                        </div>
                    </div>

                    <div class="flex gap-6 p-6 rounded-2xl border border-gray-100 hover:shadow-md transition-shadow bg-white">
                        <div class="w-12 h-12 bg-brand-dark text-white rounded-full flex items-center justify-center shrink-0">
                            <i class="ph ph-flask text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold uppercase tracking-wide text-brand-dark mb-1">Advanced Enzyme Tech</h4>
                            <p class="text-sm text-gray-500">Utilizing biowashing techniques (cellulase enzymes) to clean and soften fabrics, preventing pilling and preserving color.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="#" class="inline-block bg-brand-yellow text-brand-dark px-8 py-3.5 rounded-full font-bold hover:bg-yellow-300 transition-colors">Contact Our Experts</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial -->
    <section class="bg-brand-dark text-white py-24 px-6 md:px-12 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-yellow/10 rounded-full blur-3xl"></div>
        
        <div class="max-w-4xl mx-auto text-center relative z-10">
            <i class="ph-fill ph-quotes text-5xl text-brand-yellow mb-8 block opacity-50 mx-auto"></i>
            <h3 class="text-2xl md:text-3xl font-heading font-medium leading-snug mb-10 uppercase tracking-wide">
                "We've been using RFD Laundry for our denim line's stone washing for months now, and I couldn't be happier. The texture, the fade consistency, and their respect for the fabric integrity is consistently excellent."
            </h3>
            <div class="flex items-center justify-center gap-4">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="Client" class="w-12 h-12 rounded-full border-2 border-brand-yellow object-cover">
                <div class="text-left">
                    <p class="font-bold uppercase tracking-wider text-sm">Mark Thompson</p>
                    <p class="text-xs text-gray-400">Production Manager, Urban Denim Co.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 px-6 md:px-12 bg-black overflow-hidden flex items-center justify-center min-h-[400px]">
        <!-- Background Image with Overlay -->
        <img src="https://images.unsplash.com/photo-1610384462486-05a53a251786?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Industrial Washers" class="absolute inset-0 w-full h-full object-cover opacity-30 grayscale">
        
        <div class="relative z-10 text-center max-w-2xl">
            <h2 class="text-4xl md:text-5xl font-black font-heading text-white uppercase mb-6 leading-tight">
                Ready to Experience the <span class="text-brand-yellow">Premium Wash</span> Difference?
            </h2>
            <p class="text-gray-300 mb-8">
                Send us a sample batch or discuss your next production run with our textile finishing specialists.
            </p>
            <a href="#" class="inline-block bg-brand-yellow text-brand-dark px-10 py-4 rounded-full font-bold hover:bg-yellow-300 transition-colors uppercase tracking-wide">
                Get a Custom Quote
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 pt-16 pb-8 px-6 md:px-12">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <div class="col-span-1 md:col-span-1">
                <a href="#" class="text-2xl font-black tracking-tighter uppercase font-heading flex flex-col leading-none mb-6">
                    <span class="text-brand-dark">RFD</span>
                    <span class="text-xs tracking-widest text-gray-500">Laundry</span>
                </a>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">
                    Specialized industrial garment washing services providing stone, sand, enzyme, and bleach finishing for the apparel industry.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-brand-gray flex items-center justify-center text-brand-dark hover:bg-brand-yellow transition-colors"><i class="ph-fill ph-instagram-logo text-xl"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-brand-gray flex items-center justify-center text-brand-dark hover:bg-brand-yellow transition-colors"><i class="ph-fill ph-facebook-logo text-xl"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-brand-gray flex items-center justify-center text-brand-dark hover:bg-brand-yellow transition-colors"><i class="ph-fill ph-linkedin-logo text-xl"></i></a>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-brand-dark uppercase tracking-widest text-sm mb-6">Services</h4>
                <ul class="space-y-3 text-sm text-gray-500">
                    <li><a href="#" class="hover:text-brand-yellow transition-colors">Stone Washing</a></li>
                    <li><a href="#" class="hover:text-brand-yellow transition-colors">Sand Washing</a></li>
                    <li><a href="#" class="hover:text-brand-yellow transition-colors">Enzyme Washing</a></li>
                    <li><a href="#" class="hover:text-brand-yellow transition-colors">Bleach Washing</a></li>
                    <li><a href="#" class="hover:text-brand-yellow transition-colors">Garment Washing</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-brand-dark uppercase tracking-widest text-sm mb-6">Company</h4>
                <ul class="space-y-3 text-sm text-gray-500">
                    <li><a href="#" class="hover:text-brand-yellow transition-colors">About Us</a></li>
                    <li><a href="#" class="hover:text-brand-yellow transition-colors">Facility</a></li>
                    <li><a href="#" class="hover:text-brand-yellow transition-colors">Sustainability</a></li>
                    <li><a href="#" class="hover:text-brand-yellow transition-colors">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-brand-dark uppercase tracking-widest text-sm mb-6">Newsletter</h4>
                <p class="text-sm text-gray-500 mb-4">Subscribe for updates on our latest finishing techniques.</p>
                <div class="flex">
                    <input type="email" placeholder="Email Address" class="bg-brand-gray px-4 py-3 rounded-l-lg outline-none w-full text-sm border border-gray-200">
                    <button class="bg-brand-dark text-white px-4 py-3 rounded-r-lg hover:bg-gray-800 transition-colors">
                        <i class="ph ph-paper-plane-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-400 font-medium uppercase tracking-wider">
            <p>&copy; 2026 RFD Laundry Services. All Rights Reserved.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-brand-dark transition-colors">Terms of Service</a>
                <a href="#" class="hover:text-brand-dark transition-colors">Privacy Policy</a>
            </div>
        </div>
    </footer>

    <!-- Simple Script for Service Tabs Interactivity -->
    <script>
        const servicesData = {
            stone: {
                title: 'Premium Stone Washing',
                desc: 'Our stone washing service utilizes high-quality pumice stones to accelerate the fading process and increase the softness of denim and heavy cottons. This classic technique provides a naturally worn, vintage appearance that consumers love, carefully monitored to maintain fabric integrity.',
                img: 'https://images.unsplash.com/photo-1444312645910-ffa973656eba?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                features: ['Authentic Vintage Fades', 'Fabric Softening', 'Custom Abrasion Levels', 'Pumice Stone Sourcing']
            },
            sand: {
                title: 'Specialized Sand Washing',
                desc: 'By substituting stones with fine sand or micro-abrasives, sand washing delivers an exceptionally soft, peach-skin finish. Perfect for delicate fabrics like silks or fine cottons where stones would be too harsh, giving a subtle, dusty appearance.',
                img: 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                features: ['Peach-Skin Softness', 'Safe for Delicates', 'Micro-Abrasion', 'Subtle Distressing']
            },
            enzyme: {
                title: 'Biowashing & Enzyme Treatment',
                desc: 'Using organic cellulase enzymes, we digest protruding fibers to create a smoother fabric surface, prevent pilling, and enhance color retention. An eco-friendly alternative to aggressive stone washing that preserves the strength of the garment.',
                img: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                features: ['Prevents Pilling', 'Eco-Friendly Process', 'Maintains Fabric Strength', 'Enhances Color']
            },
            bleach: {
                title: 'Industrial Bleach Washing',
                desc: 'Controlled chemical washing using oxidative agents to dramatically lighten fabrics or create distinct high-contrast fades. Our process is strictly neutralized post-wash to ensure the garment remains safe and tear-resistant.',
                img: 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', // denim close up
                features: ['High-Contrast Fading', 'Uniform Lightening', 'Strict Neutralization', 'Color Stripping']
            },
            garment: {
                title: 'Standard Garment Washing',
                desc: 'A fundamental wet processing step that removes sizing, dirt, and shrinkage from manufactured garments. We use specialized industrial detergents to pre-shrink and soften apparel, preparing it for retail distribution.',
                img: 'https://images.unsplash.com/photo-1444312645910-ffa973656eba?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', // generic wash/water
                features: ['Sizing Removal', 'Pre-shrinking', 'Bulk Processing', 'Retail Ready']
            }
        };

        function switchTab(type) {
            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-brand-dark', 'text-white', 'border-brand-dark');
                btn.classList.add('bg-white', 'text-brand-dark', 'border-gray-200');
            });
            
            const activeBtn = event.currentTarget || document.querySelector('.tab-btn');
            activeBtn.classList.remove('bg-white', 'border-gray-200');
            activeBtn.classList.add('bg-brand-dark', 'text-white', 'border-brand-dark');

            // Update content with fade effect
            const contentDiv = document.getElementById('service-content');
            const imgEl = document.getElementById('service-img');
            
            contentDiv.style.opacity = 0;
            imgEl.style.opacity = 0;

            setTimeout(() => {
                const data = servicesData[type];
                document.getElementById('service-title').innerText = data.title;
                document.getElementById('service-desc').innerText = data.desc;
                imgEl.src = data.img;

                // Update features list
                const featuresContainer = document.querySelector('#service-content .grid');
                featuresContainer.innerHTML = '';
                data.features.forEach(feature => {
                    featuresContainer.innerHTML += `
                        <div class="flex items-center gap-3">
                            <div class="bg-brand-yellow w-6 h-6 rounded flex items-center justify-center text-brand-dark"><i class="ph ph-check font-bold"></i></div>
                            <span class="text-sm font-medium text-gray-700">${feature}</span>
                        </div>
                    `;
                });

                contentDiv.style.opacity = 1;
                contentDiv.style.transition = 'opacity 0.3s ease';
                imgEl.style.opacity = 1;
                imgEl.style.transition = 'opacity 0.3s ease';
            }, 300);
        }
    </script>
</body>
</html>