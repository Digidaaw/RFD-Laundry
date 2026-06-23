<footer id="footer" class="bg-white border-t border-gray-100 pt-16 pb-8 px-6 md:px-12">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
        <div class="col-span-1 md:col-span-1">
            <a href="{{ route('home') }}"
                class="text-2xl font-black tracking-tighter uppercase font-heading flex flex-col leading-none mb-6">
                <span class="text-brand-dark">RFD</span>
                <span
                    class="text-xs tracking-widest text-gray-500 hover:text-brand-yellow transition-colors">Laundry</span>
            </a>
            <p class="text-sm text-gray-500 leading-relaxed mb-6">
                Specialized industrial garment washing services providing stone,
                sand, enzyme, and bleach finishing for the apparel industry.
            </p>
        </div>

        <div>
            <h4 class="font-bold text-brand-dark uppercase tracking-widest text-sm mb-6">Services</h4>
            <ul class="space-y-3 text-sm text-gray-500">
                @forelse ($layanans as $layanan)
                    <li class="hover:text-brand-yellow transition-colors">
                        {{ $layanan->name }}
                    </li>
                @empty
                    <li>No Service Available</li>
                @endforelse
            </ul>
        </div>

        <div>
            <h4 class="font-bold text-brand-dark uppercase tracking-widest text-sm mb-6">Company</h4>
            <ul class="space-y-3 text-sm text-gray-500">
                <li><a href="#about" class="hover:text-brand-yellow transition-colors">About Us</a></li>
                <li><a href="#facility" class="hover:text-brand-yellow transition-colors">Facility</a></li>
                <li><a href="#sustainability" class="hover:text-brand-yellow transition-colors">Sustainability</a></li>
                <li><a href="#footer" class="hover:text-brand-yellow transition-colors">Contact</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-bold text-brand-dark uppercase tracking-widest text-sm mb-6">Contact</h4>
            <p class="text-sm text-gray-500 mb-4">
                Gg. Buana Asih No.10, Padangsambian, Kec. Denpasar Bar., Kota Denpasar, Bali 80118
            </p>
            <div class="flex gap-4">
                <a href="https://www.instagram.com/rfdlaundry/" target="_blank"
                    class="w-10 h-10 rounded-full bg-brand-gray flex items-center justify-center text-brand-dark hover:bg-brand-yellow transition-colors">
                    <i class="ph-fill ph-instagram-logo text-xl"></i>
                </a>
                <a href="https://maps.app.goo.gl/hYP8DmtyAHsH2zZr5" target="_blank"
                    class="w-10 h-10 rounded-full bg-brand-gray flex items-center justify-center text-brand-dark hover:bg-brand-yellow transition-colors">
                    <i class="ph-fill ph-map-pin text-xl"></i>
                </a>
                <a href="https://wa.me/6285738419881" target="_blank"
                    class="w-10 h-10 rounded-full bg-brand-gray flex items-center justify-center text-brand-dark hover:bg-brand-yellow transition-colors">
                    <i class="ph-fill ph-whatsapp-logo text-xl"></i>
                </a>
            </div>
        </div>
    </div>

    <div
        class="max-w-7xl mx-auto border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-400 font-medium uppercase tracking-wider">
        <p>&copy; {{ date('Y') }} RFD Laundry Services. All Rights Reserved.</p>
        <div class="flex gap-6">
            <a href="#" class="hover:text-brand-dark transition-colors">Terms of Service</a>
            <a href="#" class="hover:text-brand-dark transition-colors">Privacy Policy</a>
        </div>
    </div>
</footer>
