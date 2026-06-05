<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.code_connection') }} – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-sm">
        <div class="text-center mb-6">
            <div class="text-4xl mb-2">📩</div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.check_mails') }}</h1>
            <p class="text-gray-500 text-sm mt-1">
                {{ __('app.code_sent_to') }} <strong>{{ $email }}</strong>
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 rounded-lg p-3 mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('auth.otp.verify') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}" />

            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.six_number_code') }}</label>
            <input
                type="text"
                name="token"
                required
                autofocus
                maxlength="6"
                inputmode="numeric"
                pattern="[0-9]{6}"
                placeholder="123456"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-center tracking-widest text-lg focus:outline-none focus:ring-2 focus:ring-green-400 mb-4"
            />
            <button
                type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition"
            >
                {{ __('app.connection') }} →
            </button>
        </form>

        <p class="text-center text-sm text-gray-400 mt-4">
            <a href="{{ route('login') }}" class="hover:underline">← {{ __('app.change_mail') }}</a>
        </p>

        <p class="text-center text-xs text-gray-400 mt-2">
            {{ __('app.code_exp_desc') }}
        </p>
    </div>
</body>
</html>
