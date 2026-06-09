@extends('layouts.app')
@section('title', 'Admin — Matchs')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">⚙️ {{ __('app.manage_matches')}}</h1>
    <a href="{{ route('admin.matches.create') }}"
       class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + {{ __('app.new_match')}}
    </a>
</div>

@foreach(['group', 'r32', 'r16', 'qf', 'sf', 'final'] as $phase)
@if($matches->has($phase))
<h2 class="text-lg font-semibold text-gray-600 mt-6 mb-3">{{ __('app.phase_'.$phase)}}</h2>
<div class="bg-white rounded-2xl shadow overflow-hidden mb-4">
    @foreach($matches[$phase] as $match)
    @php $isKnockout = in_array($match->phase, ['r32', 'r16', 'qf', 'sf', 'final']); @endphp
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 last:border-0">
        <div class="flex items-center gap-3 flex-1">
            <span class="text-sm text-gray-500 w-32">{{ $match->played_at->format('d/m/Y H:i (P)') }}</span>
            <img src="/images/flags/4x3/{{ strtolower($match->homeTeam->name) }}.svg" width="24">
            <span class="font-semibold text-gray-800">{{ $match->homeTeam->displayName() }}</span>

            @if($match->isFinished())
            <span class="bg-gray-800 text-white text-sm font-bold px-3 py-1 rounded-lg">
                                {{ $match->home_score }} – {{ $match->away_score }}
                            </span>
            {{-- Qualifié en phase éliminatoire --}}
            @if($isKnockout)
            <span class="text-xs bg-blue-50 text-blue-600 border border-blue-200 px-2 py-1 rounded-full font-medium">
                                    ➡️ {{ $match->winner !== null
                                        ? ($match->winner === true ? $match->homeTeam->displayName() : $match->awayTeam->displayName())
                                        : '—' }}
                                </span>
            @endif
            @else
            <span class="text-gray-300 font-bold px-3">VS</span>
            {{-- Indicateur score manquant en éliminatoires --}}
            @if($isKnockout)
            <span class="text-xs text-orange-400">⚠️ qualifié à saisir</span>
            @endif
            @endif

            <img src="/images/flags/4x3/{{ strtolower($match->awayTeam->name) }}.svg" width="24">
            <span class="font-semibold text-gray-800">{{ $match->awayTeam->displayName() }}</span>
        </div>
        <a href="{{ route('admin.matches.score', $match) }}"
           class="text-sm text-green-600 hover:underline ml-4">
            {{ $match->isFinished() ? __('app.update_score') : __('app.set_score') }}
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
