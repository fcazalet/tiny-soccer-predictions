<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tiny Soccer Predictions') ⚽</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="bg-green-700 text-white shadow">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="font-bold text-lg tracking-tight">⚽ {{ config('app.name') }}</a>
            <div class="flex items-center gap-4 text-sm">
                @auth
                    <span class="opacity-75">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.matches.index') }}" class="hover:underline">Admin</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="hover:underline">{{ __('app.logout') }}</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 rounded-lg px-4 py-3 mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 rounded-lg px-4 py-3 mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
