<nav class="bg-brand-dark text-white px-6 md:px-12 py-4 flex justify-between items-center sticky top-0 z-50">
    <div class="flex items-center gap-8">
        <a href="{{ route('home') }}" class="text-2xl font-black tracking-tighter uppercase font-heading flex flex-col leading-none">
            <span>RFD</span>
            <span class="text-xs tracking-widest text-brand-yellow">Laundry</span>
        </a>
        <div class="hidden md:flex items-center gap-6 text-sm font-medium ml-8">
            <a href="{{ route('home') }}" class="hover:text-brand-yellow transition-colors">Home</a>
            <a href="#services" class="hover:text-brand-yellow transition-colors flex items-center gap-1">
                Services <i class="ph ph-caret-down"></i>
            </a>
            <a href="#about" class="hover:text-brand-yellow transition-colors">About Us</a>
        </div>
    </div>
    
    <div class="hidden md:flex items-center gap-4">
        <a href="#footer" class="bg-white text-brand-dark px-6 py-2 rounded-full font-bold text-sm hover:bg-brand-yellow transition-colors">
            Contact Us
        </a>
    </div>
    
    <button id="menuBtn" aria-expanded="false" aria-controls="mobileMenu" class="md:hidden text-2xl focus:outline-none">
        <i class="ph ph-list"></i>
    </button>
    
    <div id="mobileMenu" class="hidden md:hidden absolute inset-x-0 top-full bg-brand-dark text-white border-t border-gray-700 shadow-lg">
        <div class="flex flex-col px-6 py-4 gap-3">
            <a href="{{ route('home') }}" class="py-2 hover:text-brand-yellow transition-colors">Home</a>
            <a href="#services" class="py-2 hover:text-brand-yellow transition-colors">Services</a>
            <a href="#about" class="py-2 hover:text-brand-yellow transition-colors">About Us</a>
            <a href="#footer" class="py-2 bg-white text-brand-dark rounded-full text-center font-bold hover:bg-brand-yellow transition-colors">
                Contact Us
            </a>
        </div>
    </div>
</nav>