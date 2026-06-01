@extends('layouts.app')
@section('title', 'Saisir le score')

@section('content')

<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">🎯 Saisir le score</h1>

    <div class="bg-white rounded-2xl shadow p-6">
        <div class="text-center mb-6">
            <p class="text-sm text-gray-400 mb-3">{{ $match->phaseLabel() }} · {{ $match->played_at->format('d/m/Y H:i') }}</p>
            <div class="flex items-center justify-center gap-4 text-lg font-bold text-gray-800">
                <span>{{ $match->homeTeam->displayName() }}</span>
                <span class="text-gray-300">VS</span>
                <span>{{ $match->awayTeam->displayName() }}</span>
            </div>
        </div>

        <form action="{{ route('admin.matches.updateScore', $match) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-center gap-4 mb-6">
                <input type="number" name="home_score" min="0" max="20"
                    value="{{ old('home_score', $match->home_score) }}"
                    required
                    class="w-20 text-center border-2 border-gray-300 rounded-xl py-3 text-2xl font-bold focus:outline-none focus:border-green-400"
                    placeholder="0" />
                <span class="text-2xl text-gray-300 font-bold">–</span>
                <input type="number" name="away_score" min="0" max="20"
                    value="{{ old('away_score', $match->away_score) }}"
                    required
                    class="w-20 text-center border-2 border-gray-300 rounded-xl py-3 text-2xl font-bold focus:outline-none focus:border-green-400"
                    placeholder="0" />
            </div>

            @if($errors->any())
                <div class="bg-red-50 text-red-600 rounded-lg p-3 text-sm mb-4">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition">
                    Enregistrer & calculer les points
                </button>
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('admin.matches.index') }}" class="text-sm text-gray-400 hover:underline">Annuler</a>
            </div>
        </form>
    </div>
</div>

@endsection
