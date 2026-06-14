<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.demo_mode') }} – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-sm">

        <div class="text-center mb-6">
            <div class="text-4xl mb-2">⚽</div>
            <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name') }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ __('app.demo_choose_role') }}</p>
        </div>

        <form action="{{ route('demo.login') }}" method="POST" class="space-y-3">
            @csrf

            <button
                type="submit"
                name="role"
                value="admin"
                class="w-full flex items-center gap-4 border border-gray-200 rounded-xl px-4 py-3 hover:bg-green-50 hover:border-green-300 transition text-left group"
            >
                <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center text-xl flex-shrink-0">
                    🛡️
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-800">{{ __('app.demo_admin') }}</p>
                    <p class="text-xs text-gray-500">{{ __('app.demo_admin_desc') }}</p>
                </div>
                <span class="text-gray-300 group-hover:text-green-400 text-lg">›</span>
            </button>

            <button
                type="submit"
                name="role"
                value="player"
                class="w-full flex items-center gap-4 border border-gray-200 rounded-xl px-4 py-3 hover:bg-green-50 hover:border-green-300 transition text-left group"
            >
                <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center text-xl flex-shrink-0">
                    👤
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-800">{{ __('app.demo_player') }}</p>
                    <p class="text-xs text-gray-500">{{ __('app.demo_player_desc') }}</p>
                </div>
                <span class="text-gray-300 group-hover:text-green-400 text-lg">›</span>
            </button>

        </form>

        <p class="text-center text-xs text-gray-400 mt-5">{{ __('app.demo_no_password') }}</p>

    </div>
</body>
</html>