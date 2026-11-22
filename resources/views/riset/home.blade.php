<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="antialiased">
        <main class="max-w-5xl mx-auto py-8">
            <h1 class="text-2xl font-semibold">Sutha's Workshop</h1>
            <p class="mt-2">
                I keep all my experiments here. Reach me at
                <a href="mailto:alpha.sutha@gmail.com" class="underline">my email</a>.
            </p>

            <section class="mt-16">
                <h2 class="text-lg font-semibold">Gallery</h2>
                <p class="italic">A small showcase of my experiments</p>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <figure>
                        <model-viewer
                            src="{{ asset('models/diponegoro-monas-vggt.glb') }}"
                            alt="3D statue of Pangeran Diponegoro"
                            camera-controls
                            auto-rotate
                            shadow-intensity="1"
                            interaction-prompt="none"
                            camera-orbit="290deg 75deg 15%"
                            class="w-full h-56 bg-gray-200"
                        ></model-viewer>
                        <figcaption class="mt-2 text-xs text-gray-600">
                            <span>Pangeran Diponegoro (3D) at Monumen Nasional, Jakarta.</span>
                            <span>Reconstructed using VGGT.</span>
                        </figcaption>
                    </figure>
                </div>
            </section>
        </main>
    </body>
</html>
