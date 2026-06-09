@extends('layouts.app')
@section('title', __('app.set_score'))

@section('content')

<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">🎯 {{ __('app.set_score') }}</h1>

    <div class="bg-white rounded-2xl shadow p-6">
        <div class="text-center mb-6">
            <p class="text-sm text-gray-400 mb-3">{{ $match->phaseLabel() }} · {{ $match->played_at->format('d/m/Y H:i') }}</p>
            <div class="flex items-center justify-center gap-4 text-lg font-bold text-gray-800">
                <img src="/images/flags/4x3/{{ strtolower($match->homeTeam->name) }}.svg" width="24" class="inline-block">
                <span>{{ $match->homeTeam->displayName() }}</span>
                <span class="text-gray-300">VS</span>
                <img src="/images/flags/4x3/{{ strtolower($match->awayTeam->name) }}.svg" width="24" class="inline-block">
                <span>{{ $match->awayTeam->displayName() }}</span>
            </div>
        </div>

        <form action="{{ route('admin.matches.updateScore', $match) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Score to 90 min --}}
            <p class="text-xs text-gray-400 text-center uppercase tracking-wide mb-2">
                Score (temps réglementaire)
            </p>
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

            {{-- Qualifié — phases éliminatoires uniquement --}}
            @if($match->isKnockout())
            <div class="border-t border-gray-100 pt-5 mb-6">
                <p class="text-xs text-gray-400 text-center uppercase tracking-wide mb-3">
                    ➡️ Équipe qualifiée
                    <span class="text-orange-400 font-normal normal-case">(peut différer du score si prolongations / TAB)</span>
                </p>
                <div class="flex gap-3 justify-center">
                    {{-- Home --}}
                    <label class="winner-option flex-1">
                        <input type="radio" name="winner" value="home" class="sr-only"
                               {{ old('winner', $match->winner) === true || old('winner', $match->winner) === 'home' ? 'checked' : '' }}>
                        <span class="winner-btn flex items-center justify-center gap-2 border-2 rounded-xl py-3 px-3 cursor-pointer text-sm font-semibold transition
                                     {{ old('winner', $match->winner) === true || old('winner', $match->winner) === 'home'
                                         ? 'border-blue-500 bg-blue-50 text-blue-700'
                                         : 'border-gray-200 text-gray-600 hover:border-blue-300 hover:bg-blue-50' }}">
                            <img src="/images/flags/4x3/{{ strtolower($match->homeTeam->name) }}.svg" width="20">
                            {{ $match->homeTeam->displayName() }}
                        </span>
                    </label>

                    {{-- Away --}}
                    <label class="winner-option flex-1">
                        <input type="radio" name="winner" value="away" class="sr-only"
                               {{ old('winner', $match->winner) === false || old('winner', $match->winner) === 'away' ? 'checked' : '' }}>
                        <span class="winner-btn flex items-center justify-center gap-2 border-2 rounded-xl py-3 px-3 cursor-pointer text-sm font-semibold transition
                                     {{ old('winner', $match->winner) === false || old('winner', $match->winner) === 'away'
                                         ? 'border-blue-500 bg-blue-50 text-blue-700'
                                         : 'border-gray-200 text-gray-600 hover:border-blue-300 hover:bg-blue-50' }}">
                            <img src="/images/flags/4x3/{{ strtolower($match->awayTeam->name) }}.svg" width="20">
                            {{ $match->awayTeam->displayName() }}
                        </span>
                    </label>
                </div>
            </div>

            <script>
                document.querySelectorAll('.winner-option').forEach(label => {
                    label.addEventListener('click', () => {
                        document.querySelectorAll('.winner-btn').forEach(btn => {
                            btn.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700');
                            btn.classList.add('border-gray-200', 'text-gray-600');
                        });
                        label.querySelector('.winner-btn').classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700');
                        label.querySelector('.winner-btn').classList.remove('border-gray-200', 'text-gray-600');
                    });
                });
            </script>
            @endif

            @if($errors->any())
            <div class="bg-red-50 text-red-600 rounded-lg p-3 text-sm mb-4">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
            @endif

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition">
                    {{ __('app.save_and_calculate') }}
                </button>
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('admin.matches.index') }}" class="text-sm text-gray-400 hover:underline">{{ __('app.cancel') }}</a>
            </div>
        </form>
    </div>
</div>

@endsection
