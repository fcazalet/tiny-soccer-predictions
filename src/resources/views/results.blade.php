@extends('layouts.app')
@section('title', __('app.my_results'))

@section('content')

<div class="mb-6 flex items-center justify-between">
    <h2 class="text-xl font-bold text-gray-800">📋 {{ __('app.results') }}</h2>
    <div class="flex gap-4 text-sm">
        <div class="text-center">
            <div class="font-bold text-green-600 text-lg">{{ $totalPointsEarned }}</div>
            <div class="text-gray-400 text-xs uppercase tracking-wide">{{ __('app.points') }}</div>
        </div>
        <div class="text-center">
            <div class="font-bold text-green-600 text-lg">{{ $userPredictions->count() }}</div>
            <div class="text-gray-400 text-xs uppercase tracking-wide">{{ __('app.predicted') }}</div>
        </div>
        <div class="text-center">
            <div class="font-bold text-gray-700 text-lg">{{ $userPredictions->count() }} / {{ $totalFixturesCount }}
            </div>
            <div class="text-gray-400 text-xs uppercase tracking-wide">{{ __('app.predictions') }}</div>
        </div>
    </div>
</div>

{{-- Phase filter tabs --}}
@if($phases->isNotEmpty())
<div class="flex gap-2 mb-6 overflow-x-auto pb-1">
    <a href="{{ route('results.index') }}"
       class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition
              {{ !request('phase') ? 'bg-green-500 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-green-300' }}">
        {{ __('app.all') }}
    </a>
    @foreach($phases as $phase)
    <a href="{{ route('results.index', ['phase' => $phase]) }}"
       class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition
              {{ request('phase') === $phase ? 'bg-green-500 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:border-green-300' }}">
        {{ __('app.phase_'.$phase) }}
    </a>
    @endforeach
</div>
@endif

{{-- Predictions list --}}
<div class="bg-white rounded-2xl shadow overflow-hidden">
    @forelse($filteredFixtures as $fixture)
    @php $prediction = $userPredictions->get($fixture->id) @endphp
    <div class="px-5 py-4 border-b border-gray-100 last:border-0">

        {{-- Phase & date --}}
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-gray-400 uppercase tracking-wide">
                {{ $fixture->phaseLabel() }} · {{ $fixture->getLocalPlayedAt()->format('d/m/Y H:i') }}
            </span>
            @if($fixture->home_score !== null)
            {{-- Match finished: show points badge --}}
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                             {{ $prediction && $prediction->points_earned > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                    +{{ $prediction ? $prediction->points_earned : 0 }} pt{{ $prediction && $prediction->points_earned > 1 ? 's' : '' }}
            </span>
            @else
                @if($fixture->played_at->isFuture())
                {{-- Match not yet played --}}
                <span class="text-xs bg-blue-50 text-blue-400 px-2 py-0.5 rounded-full">
                    {{ __('app.upcoming') }}
                </span>
                @else
                    @if ($fixture->played_at->addHours(3)->isFuture())
                        {{-- Match started --}}
                        <span class="text-xs bg-green-50 text-green-400 px-2 py-0.5 rounded-full">
                            {{ __('app.started') }}
                        </span>
                    @else
                        {{-- Match ended --}}
                        <span class="text-xs bg-red-50 text-red-400 px-2 py-0.5 rounded-full">
                            {{ __('app.ended') }}
                        </span>
                    @endif
                @endif
            @endif
        </div>

        {{-- Teams --}}
        <div class="flex items-center gap-3 mb-3">
            {{-- Home --}}
            <span class="text-sm font-semibold text-gray-700 text-right flex-1">
                <img src="/images/flags/4x3/{{ strtolower($fixture->homeTeam->name) }}.svg" width="24"
                     class="inline-block mr-1">
                {{ $fixture->homeTeam->displayName() }}
            </span>

            {{-- Actual score (if played) vs VS --}}
            <div class="text-center shrink-0">
                @if($fixture->home_score !== null)
                <span class="bg-gray-800 text-white text-sm font-bold px-3 py-1 rounded-lg">
                        {{ $fixture->home_score }} – {{ $fixture->away_score }}
                    </span>
                @if($fixture->isKnockout() && $fixture->winner)
                <div class="text-xs text-gray-400 mt-1">
                    ➡️ {{ $fixture->winner === 'home' ? $fixture->homeTeam->displayName() : $fixture->awayTeam->displayName()
                    }}
                </div>
                @endif
                @else
                <span class="text-gray-400 font-bold text-sm">VS</span>
                @endif
            </div>

            {{-- Away --}}
            <span class="text-sm font-semibold text-gray-700 flex-1">
                <img src="/images/flags/4x3/{{ strtolower($fixture->awayTeam->name) }}.svg" width="24"
                     class="inline-block mr-1">
                {{ $fixture->awayTeam->displayName() }}
            </span>
        </div>

        {{-- User prediction --}}
        <div class="pt-3 border-t border-gray-50 flex items-center justify-end gap-2">
            <span class="text-xs text-gray-400">{{ __('app.your_prediction') }} :</span>
            @if($prediction)
            <div class="text-xs font-medium text-gray-600">
                {{ $prediction->scoreLabel() }}
                @if($fixture->isKnockout())
                @if($prediction->predicted_winner)
                · {{ $prediction->predicted_winner === 'home' ? $fixture->homeTeam->displayName() :
                $fixture->awayTeam->displayName() }}
                @else
                · <span class="text-orange-400">{{ __('app.no_winner_predicted') }}</span>
                @endif
                @endif
            </div>
            @else
            <div class="text-xs font-medium text-gray-600">
                <span class="text-orange-400">{{ __('app.noprediction') }}</span>
            </div>
            @endif
        </div>

    </div>
    @empty
    <div class="px-5 py-10 text-center text-gray-400 text-sm">{{ __('app.noprediction') }}</div>
    @endforelse
</div>

@endsection
