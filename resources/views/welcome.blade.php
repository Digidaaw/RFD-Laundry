@extends('layout.app')

@section('title', 'RFD Laundry - Industrial Garment Washing')

@push('styles')
    @vite('resources/css/welcome.css')
@endpush

@section('content')
    <!-- Hero Section -->
    @include('partials.sections.hero')

    <!-- Trust / Features Section -->
    @include('partials.sections.features')

    <!-- Services Section -->
    @include('partials.sections.services')

    <!-- Stats Section -->
    @include('partials.sections.stats')

    <!-- Why Choose Us Section -->
    @include('partials.sections.why-choose-us')

    <!-- Testimonial Section -->
    @include('partials.sections.testimonial')

    <!-- CTA Section -->
    @include('partials.sections.cta')
@endsection

<script>
    window.layanans = @json($layanans);
</script>

@push('scripts')
    @vite('resources/js/welcome.js')
@endpush
