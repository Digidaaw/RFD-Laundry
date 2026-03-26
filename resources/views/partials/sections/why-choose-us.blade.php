<section class="py-24 px-6 md:px-12 bg-gray-50" id="facility">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div class="flex justify-center lg:justify-start w-full h-full">
            <img src="{{ asset('images/c880f6b57501b35c61a9f31b18333552.jpg') }}" alt="Laundry Machine"
                class="w-full max-w-md rounded-2xl object-cover shadow-lg">
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
            
            @php
                $reasons = [
                    [
                        'title' => 'Time-Saving',
                        'desc' => 'From pickup to delivery, our process is designed to save your valuable time while ensuring clean results.'
                    ],
                    [
                        'title' => 'High-Quality Care',
                        'desc' => 'Our professionals handle your clothes with premium detergents ensuring they look and feel their best.'
                    ],
                    [
                        'title' => 'Eco-Friendly Products',
                        'desc' => 'We use safe and environmentally friendly detergents that are tough on stains but gentle on fabrics.'
                    ]
                ];
            @endphp

            <div class="space-y-6">
                @foreach($reasons as $reason)
                    <div class="flex items-start gap-4 bg-white p-6 rounded-xl shadow-sm">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center shrink-0">
                            ●
                        </div>
                        <div>
                            <h4 class="font-bold uppercase text-gray-900 mb-1">
                                {{ $reason['title'] }}
                            </h4>
                            <p class="text-sm text-gray-500">
                                {{ $reason['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-10">
                <a href="#footer" class="inline-block bg-yellow-400 text-black px-8 py-3 rounded-full font-semibold hover:bg-yellow-300 transition">
                    Start Cleaning
                </a>
            </div>
        </div>
    </div>
</section>