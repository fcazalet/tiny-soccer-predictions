@extends('layouts.app')
@section('title', 'Admin — Matchs')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">⚙️ Gestion des matchs</h1>
    <a href="{{ route('admin.matches.create') }}"
       class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Nouveau match
    </a>
</div>

@foreach(['group' => 'Phase de groupes', 'r16' => 'Huitièmes', 'qf' => 'Quarts', 'sf' => 'Demi-finales', 'final' => 'Finale'] as $phase => $label)
    @if($matches->has($phase))
        <h2 class="text-lg font-semibold text-gray-600 mt-6 mb-3">{{ $label }}</h2>
        <div class="bg-white rounded-2xl shadow overflow-hidden mb-4">
            @foreach($matches[$phase] as $match)
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="text-sm text-gray-500 w-32">{{ $match->played_at->format('d/m/Y H:i') }}</span>
                        <span class="font-semibold text-gray-800">
                            {{ $match->homeTeam->displayName() }}
                        </span>
                        @if($match->isFinished())
                            <span class="bg-gray-800 text-white text-sm font-bold px-3 py-1 rounded-lg">
                                {{ $match->home_score }} – {{ $match->away_score }}
                            </span>
                        @else
                            <span class="text-gray-300 font-bold px-3">VS</span>
                        @endif
                        <span class="font-semibold text-gray-800">
                            {{ $match->awayTeam->displayName() }}
                        </span>
                    </div>
                    <a href="{{ route('admin.matches.score', $match) }}"
                       class="text-sm text-green-600 hover:underline ml-4">
                        {{ $match->isFinished() ? 'Modifier le score' : 'Saisir le score' }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif
@endforeach

@if($matches->isEmpty())
    <div class="bg-white rounded-2xl shadow p-8 text-center text-gray-400">
        Aucun match créé. <a href="{{ route('admin.matches.create') }}" class="text-green-600 hover:underline">Créer le premier match</a>.
    </div>
@endif

@endsection
