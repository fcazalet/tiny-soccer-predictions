<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de connexion – WorldCup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-sm">
        <div class="text-center mb-6">
            <div class="text-4xl mb-2">📩</div>
            <h1 class="text-2xl font-bold text-gray-800">Vérifiez vos emails</h1>
            <p class="text-gray-500 text-sm mt-1">
                Code envoyé à <strong>{{ $email }}</strong>
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

            <label class="block text-sm font-medium text-gray-700 mb-1">Code à 6 chiffres</label>
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
                Connexion →
            </button>
        </form>

        <p class="text-center text-sm text-gray-400 mt-4">
            <a href="{{ route('login') }}" class="hover:underline">← Changer d'email</a>
        </p>

        <p class="text-center text-xs text-gray-400 mt-2">
            Ce code expire dans 10 minutes
        </p>
    </div>
</body>
</html>
