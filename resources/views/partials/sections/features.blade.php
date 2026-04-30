<section class="bg-brand-gray py-20 px-6 md:px-12" id="about">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-6">
            <h2 class="text-3xl md:text-4xl font-black font-heading uppercase max-w-lg leading-tight text-brand-dark">
                Your Trusted Partner In Achieving Pristine Finishes.
            </h2>
            <p class="text-gray-500 max-w-sm text-sm">
                Established with the mission to elevate your apparel's look and
                feel, we bring a blend of modern technology, natural elements, and
                industrial expertise.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $features = [
                    [
                        'icon' => 'ph-truck',
                        'title' => 'Large Scale Capacity',
                        'desc' => 'Equipped with industrial-grade machinery to handle bulk orders seamlessly, ensuring your production timeline is met with efficiency.'
                    ],
                    [
                        'icon' => 'ph-leaf',
                        'title' => 'Eco-Friendly Bio-Agents',
                        'desc' => 'We prioritize environmental health by utilizing advanced enzymes and eco-friendly bleaching agents that reduce water contamination.'
                    ],
                    [
                        'icon' => 'ph-drop',
                        'title' => 'Specialized Techniques',
                        'desc' => 'From pumice stone abrasion to delicate sand washes, our experts apply precise methodologies to achieve the exact texture and fade required.'
                    ]
                ];
            @endphp

            @foreach($features as $feature)
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-brand-yellow transition-colors group">
                    <div class="w-12 h-12 bg-gray-100 group-hover:bg-brand-yellow transition-colors rounded-xl flex items-center justify-center mb-6">
                        <i class="ph {{ $feature['icon'] }} text-2xl font-bold"></i>
                    </div>
                    <h3 class="font-bold text-sm tracking-widest uppercase mb-3 text-brand-dark">
                        {{ $feature['title'] }}
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        {{ $feature['desc'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>