<section class="py-16 border-y border-gray-100 bg-white">
    <div class="max-w-6xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-12 text-center md:text-left">
        @php
            $stats = [
                ['number' => '15', 'suffix' => '+', 'text' => 'Years of industrial washing experience.'],
                ['number' => '99', 'suffix' => '%', 'text' => 'Customer satisfaction rate from apparel brands.'],
                ['number' => '100', 'suffix' => '%', 'text' => 'Eco-friendly compliance on bio-agents.'],
                ['number' => '50', 'suffix' => 'k', 'text' => 'Garments processed weekly capacity.']
            ];
        @endphp

        @foreach($stats as $stat)
            <div>
                <h3 class="text-5xl font-black font-heading mb-2 text-brand-dark">
                    {{ $stat['number'] }}<span class="text-brand-yellow">{{ $stat['suffix'] }}</span>
                </h3>
                <p class="text-gray-500 text-sm">{{ $stat['text'] }}</p>
            </div>
        @endforeach
    </div>
</section>