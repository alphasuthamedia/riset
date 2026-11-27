@extends('layout.app')

@push('head')
    @vite('resources/js/model-viewer.js')
@endpush

@section('content')
<main class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-semibold">Sutha's Workshop</h1>
    <p class="mt-2">
        An island for dumping my experiments freely. Questions or feedback:
        <a href="mailto:{{ config('whoami.email') }}" class="underline">reach me out!</a>.
    </p>

    <section class="mt-16">
        <h2 class="text-lg font-semibold">Gallery</h2>
        <p class="italic">A small showcase of my experiments</p>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <figure>
                <model-viewer
                    src="{{ asset('riset/vggt-historical-site-reconstruction/diponegoro-monas-n-15.glb') }}"
                    alt="3D statue of Pangeran Diponegoro"
                    camera-controls
                    auto-rotate
                    shadow-intensity="1"
                    interaction-prompt="none"
                    camera-orbit="290deg 75deg 15%"
                    class="w-full h-56 bg-none"
                ></model-viewer>
                <figcaption class="mt-2 text-xs">
                    <span>Pangeran Diponegoro (3D) at Monumen Nasional, Jakarta.</span>
                    <span>Reconstructed using VGGT. <a href="{{ route('riset.show', 'vggt-historical-site-reconstruction')}}" class="font-bold underline">Details</a></span>
                </figcaption>
            </figure>
        </div>
    </section>
</main>
@endsection