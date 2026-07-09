@extends('layouts.app')
@section('title', $player->name)

@section('content')

<a href="{{ route('dashboard') }}" class="text-sm text-gray-400 hover:text-green-600 mb-4 inline-block">
    ← {{ __('app.back_to_home') }}
</a>

<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800">
        📜 {{ __('app.predictions_of') }} {{ $player->name }}
    </h2>
    <span class="text-sm font-bold text-green-700">
        {{ $totalPointsEarned }} pts
    </span>
</div>

{{-- Phase filter tabs --}}
@if($phases->isNotEmpty())
<div class="flex gap-2 mb-6 overflow-x-auto pb-1">
    <a href="{{ route('players.predictions', $player) }}"
       class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition
              {{ !request('phase') ? 'bg-green-500 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-green-300' }}">
        {{ __('app.all') }}
    </a>
    @foreach($phases as $phase)
    <a href="{{ route('players.predictions', [$player, 'phase' => $phase]) }}"
       class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition
              {{ request('phase') === $phase ? 'bg-green-500 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-green-300' }}">
        {{ __('app.phase_'.$phase) }}
    </a>
    @endforeach
</div>
@endif

<div class="bg-white rounded-2xl shadow overflow-hidden">
    @forelse($predictions as $prediction)
    @php $fixture = $prediction->fixture; @endphp
    <div class="flex flex-col px-5 py-4 border-b border-gray-100 last:border-0 gap-2">

        <span class="text-xs text-gray-400 uppercase tracking-wide">
            {{ $fixture->phaseLabel() }} · {{ $fixture->getLocalPlayedAt()->format('d/m/Y H:i') }}
        </span>

        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-gray-700 flex-1 flex items-center justify-end gap-1 text-right">
                {{ $fixture->homeTeam->displayName() }}
                <img src="/images/flags/4x3/{{ strtolower($fixture->homeTeam->name) }}.svg" width="20" class="inline-block shrink-0">
            </span>

            <span class="bg-gray-800 text-white text-sm font-bold px-2 py-1 rounded-lg whitespace-nowrap">
                {{ $fixture->home_score }} – {{ $fixture->away_score }}
            </span>

            <span class="text-sm font-semibold text-gray-700 flex-1 flex items-center gap-1">
                <img src="/images/flags/4x3/{{ strtolower($fixture->awayTeam->name) }}.svg" width="20" class="inline-block shrink-0">
                {{ $fixture->awayTeam->displayName() }}
            </span>
        </div>

        <div class="flex items-center justify-end gap-2">
            <div class="text-xs text-gray-400">
                {{ __('app.prediction') }} :
                {{ $prediction->scoreLabel() }}
                @if($fixture->isKnockout())
                    @if($prediction->predicted_winner)
                        · {{ $prediction->predicted_winner === 'home' ? $fixture->homeTeam->displayName() : $fixture->awayTeam->displayName() }}
                    @else
                        · <span class="text-orange-400">{{ __('app.no_winner_predicted') }}</span>
                    @endif
                @endif
            </div>
            <div class="text-sm font-bold shrink-0 {{ $prediction->points_earned > 0 ? 'text-green-600' : 'text-gray-400' }}">
                +{{ $prediction->points_earned }} pt{{ $prediction->points_earned > 1 ? 's' : '' }}
            </div>
        </div>
    </div>
    @empty
    <div class="px-5 py-4 text-center text-gray-400 text-sm">{{ __('app.noresult') }}</div>
    @endforelse
</div>

@endsection