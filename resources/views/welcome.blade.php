<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans">
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div class="min-h-screen flex flex-col selection:bg-[#ff2d20] selection:text-white">
            <div class="w-full px-6">
                <header class="mt-4">
                    @if (Route::has('login'))
                        <livewire:welcome.navigation />
                    @endif
                </header>
                <main class="mt-6 flex flex-col items-center">
                    @auth
                        <h1 class="text-white">Please Go to the Dashboard Page</h1>
                    @else
                        <h1 class="text-white">Please Login to view the Dashboard Page</h1>
                    @endauth
                </main>
            </div>
        </div>
    </div>
</body>

</html>
