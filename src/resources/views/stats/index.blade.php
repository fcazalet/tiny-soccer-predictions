@extends('layouts.app')
@section('title', __('app.stats'))

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-6">📈 {{ __('app.stats') }}</h1>

{{-- Top scores exacts --}}
<div class="mb-8">
    <h2 class="text-lg font-bold text-gray-800 mb-3">🎯 {{ __('app.top_exact_scores') }}</h2>
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        @forelse($exactScoresByPlayer as $i => $row)
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 last:border-0">
            <span class="text-gray-700 font-medium">{{ $i + 1 }}. {{ $row->user->name }}</span>
            <span class="font-bold text-green-700">{{ $row->exact_count }}</span>
        </div>
        @empty
        <div class="px-5 py-4 text-center text-gray-400">{{ __('app.noresult') }}</div>
        @endforelse
    </div>
</div>

{{-- Classement par phase --}}
<div class="mb-8">
    <h2 class="text-lg font-bold text-gray-800 mb-3">🏅 {{ __('app.ranking_by_phase') }}</h2>
    @foreach($pointsByPlayerAndPhase as $phase => $rows)
    <div class="mb-4">
        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">{{ __('app.phase_'.$phase) }}</h3>
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            @foreach($rows->sortByDesc('total_points')->values() as $j => $row)
            <div class="flex items-center justify-between px-5 py-2.5 border-b border-gray-100 last:border-0">
                <span class="text-gray-700">{{ $j + 1 }}. {{ $row->name }}</span>
                <span class="font-bold text-green-700">{{ $row->total_points }} pts</span>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- Matchs avec le plus de scores exacts --}}
<div class="mb-8">
    <h2 class="text-lg font-bold text-gray-800 mb-3">🔥 {{ __('app.most_exact_fixtures') }}</h2>
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        @forelse($mostExactFixtures as $fixture)
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 last:border-0">
            <span class="text-gray-700 text-sm">
                {{ $fixture->homeTeam->displayName() }} {{ $fixture->home_score }}-{{ $fixture->away_score }} {{ $fixture->awayTeam->displayName() }}
            </span>
            <span class="font-bold text-green-700">{{ $fixture->exact_count }} 🎯</span>
        </div>
        @empty
        <div class="px-5 py-4 text-center text-gray-400">{{ __('app.noresult') }}</div>
        @endforelse
    </div>
</div>

{{-- Matchs sans vainqueur trouvé --}}
<div class="mb-8">
    <h2 class="text-lg font-bold text-gray-800 mb-3">😵 {{ __('app.no_winner_found') }}</h2>
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        @forelse($noWinnerFoundFixtures as $fixture)
        <div class="px-5 py-3 border-b border-gray-100 last:border-0 text-sm text-gray-700">
            {{ $fixture->homeTeam->displayName() }} vs {{ $fixture->awayTeam->displayName() }}
        </div>
        @empty
        <div class="px-5 py-4 text-center text-gray-400">{{ __('app.noresult') }}</div>
        @endforelse
    </div>
</div>

{{-- Joueurs les plus audacieux --}}
<div class="mb-8">
    <h2 class="text-lg font-bold text-gray-800 mb-3">🎲 {{ __('app.most_audacious') }}</h2>
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        @forelse($mostAudaciousPlayers as $i => $row)
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 last:border-0">
            <span class="text-gray-700 font-medium">{{ $i + 1 }}. {{ $row['user']->name }}</span>
            <span class="font-bold text-green-700">{{ $row['contrarian_count'] }} 🎲</span>
        </div>
        @empty
        <div class="px-5 py-4 text-center text-gray-400">{{ __('app.noresult') }}</div>
        @endforelse
    </div>
</div>

@endsection