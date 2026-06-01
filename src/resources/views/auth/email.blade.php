<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion – WorldCup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-sm">
        <div class="text-center mb-6">
            <div class="text-4xl mb-2">⚽</div>
            <h1 class="text-2xl font-bold text-gray-800">WorldCup Pronostics</h1>
            <p class="text-gray-500 text-sm mt-1">Entrez votre email pour recevoir un code</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 rounded-lg p-3 mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('auth.otp.send') }}" method="POST">
            @csrf
            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
            <input
                type="email"
                name="email"
                required
                autofocus
                value="{{ old('email') }}"
                placeholder="toi@exemple.com"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 mb-4"
            />
            <button
                type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition"
            >
                Recevoir mon code →
            </button>
        </form>
    </div>
</body>
</html>
