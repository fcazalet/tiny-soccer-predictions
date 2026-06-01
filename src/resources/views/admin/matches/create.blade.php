@extends('layouts.app')
@section('title', 'Nouveau match')

@section('content')

<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">➕ Nouveau match</h1>

    <div class="bg-white rounded-2xl shadow p-6">
        <form action="{{ route('admin.matches.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Équipe domicile</label>
                    <select name="home_team_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        <option value="">-- Choisir --</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('home_team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->displayName() }} (Groupe {{ $team->group->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Équipe extérieur</label>
                    <select name="away_team_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        <option value="">-- Choisir --</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('away_team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->displayName() }} (Groupe {{ $team->group->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phase</label>
                <select name="phase" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    <option value="group">Phase de groupes</option>
                    <option value="r16">Huitièmes de finale</option>
                    <option value="qf">Quarts de finale</option>
                    <option value="sf">Demi-finales</option>
                    <option value="final">Finale</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date & heure</label>
                <input type="datetime-local" name="played_at" required
                    value="{{ old('played_at') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>

            @if($errors->any())
                <div class="bg-red-50 text-red-600 rounded-lg p-3 text-sm">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-5 py-2 rounded-lg transition">
                    Créer le match
                </button>
                <a href="{{ route('admin.matches.index') }}" class="text-gray-500 hover:underline px-4 py-2 text-sm">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
