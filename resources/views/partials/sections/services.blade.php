<section class="py-24 px-6 md:px-12 bg-white" id="services">
    <div class="max-w-7xl mx-auto text-center mb-16">
        <h2 class="text-4xl md:text-5xl font-black font-heading uppercase text-brand-dark mb-4">
            Our Services
        </h2>
        <p class="text-gray-500 max-w-2xl mx-auto">
            Specialized finishing processes designed to give your garments the
            perfect look, feel, and character.
        </p>
    </div>

    <div class="max-w-6xl mx-auto">

        {{-- ✅ TABS DARI DATABASE --}}
        <div class="flex flex-wrap justify-center gap-3 mb-12" id="service-tabs">
            @foreach($layanans as $index => $layanan)
                <button 
                    data-index="{{ $index }}"
                    class="tab-btn {{ $index === 0 ? 'active bg-brand-dark text-white border-brand-dark' : 'bg-white text-brand-dark border-gray-200 hover:border-brand-dark' }} px-6 py-2.5 rounded-full text-sm font-bold uppercase tracking-wide border-2 transition-all">
                    
                    {{ $layanan->name }}
                </button>
            @endforeach
        </div>

        @php $first = $layanans->first(); @endphp

        {{-- CONTENT --}}
        <div class="bg-brand-gray p-8 md:p-12 rounded-3xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center min-h-[500px]">
            
            {{-- TEXT --}}
            <div id="service-content">

                <h3 class="text-3xl font-black font-heading uppercase mb-4 text-brand-dark" id="service-title">
                    {{ $first->name ?? 'No Service' }}
                </h3>

                <p class="text-gray-600 mb-8 leading-relaxed" id="service-desc">
                    {{ $first->deskripsi ?? '-' }}
                </p>

                {{-- FEATURES (sementara static) --}}
                <h4 class="font-bold text-sm tracking-widest uppercase mb-4 text-brand-dark">
                    What We Offer
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8" id="service-features">
                    <div class="text-gray-500 text-sm">
                        Layanan profesional dengan kualitas terbaik
                    </div>
                </div>

                <a href="#footer" 
                   class="inline-block bg-brand-yellow text-brand-dark px-8 py-3.5 rounded-full font-bold hover:bg-yellow-300 transition-colors">
                    Request Sample
                </a>
            </div>

            {{-- IMAGE --}}
            <div class="relative h-full min-h-[300px] md:min-h-[400px] rounded-2xl overflow-hidden border-4 border-white shadow-xl">
                <img id="service-img"
                    src="{{ $first && $first->gambar ? asset('images/layanan/'.$first->gambar[0]) : '' }}"
                    alt="Service Image"
                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300" />
            </div>

        </div>
    </div>
</section>