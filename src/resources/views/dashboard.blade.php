@extends('layouts.app')
@section('title', __('app.dashboard'))

@section('content')

{{-- Classement --}}
<div class="mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">🏆 {{ __('app.leaderboard') }}</h2>
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">{{ __('app.player') }}</th>
                <th class="px-4 py-3 text-right">{{ __('app.points') }}</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($leaderboard as $i => $player)
            <tr class="{{ $player->id === auth()->id() ? 'bg-green-50 font-semibold' : '' }}">
                <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                <td class="px-4 py-3">
                    {{ $player->name }}
                    @if($player->id === auth()->id())
                    <span class="text-green-600 text-xs ml-1">(vous)</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right font-bold text-green-700">
                    {{ $player->predictions_sum_points_earned ?? 0 }} pts
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-4 text-center text-gray-400">{{ __('app.noplayer') }}</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Matchs à pronostiquer --}}
<div class="mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">📋 {{ __('app.upcoming_matches') }}</h2>
    @forelse($upcomingMatches as $match)
    <div class="bg-white rounded-2xl shadow p-5 mb-4">
        <form action="{{ route('predictions.store.json', $match) }}" class="score-form" method="POST">
            @csrf
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-gray-400 uppercase tracking-wide">
                    {{ $match->phaseLabel() }} · {{ $match->getLocalPlayedAt()->format('d/m/Y H:i') }}
                </span>
                @if($userPredictions->has($match->id))
                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full score-status">✓ {{ __('app.prediction_registered') }}</span>
                @else
                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full score-status"
                      style="display: none"></span>
                @endif
            </div>

            <div class="flex items-center gap-4 mb-4">
                <div class="flex-1 text-right font-semibold text-gray-800">
                    <img src="/images/flags/4x3/{{ strtolower($match->homeTeam->name) }}.svg" width="24"
                         class="inline-block">
                    <span>{{ $match->homeTeam->displayName() }}</span>
                </div>
                <span class="text-gray-400 font-bold">VS</span>
                <div class="flex-1 font-semibold text-gray-800">
                    <img src="/images/flags/4x3/{{ strtolower($match->awayTeam->name) }}.svg" width="24"
                         class="inline-block">
                    <span>{{ $match->awayTeam->displayName() }}</span>
                </div>
            </div>

            <div class="flex items-center justify-center gap-3">
                <input
                    type="number" name="home_score" min="0" max="20"
                    value="{{ $userPredictions->get($match->id)?->home_score ?? '' }}"
                    class="w-16 text-center border border-gray-300 rounded-lg py-2 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-green-400"
                    placeholder="0"
                />
                <span class="text-gray-400 font-bold">–</span>
                <input
                    type="number" name="away_score" min="0" max="20"
                    value="{{ $userPredictions->get($match->id)?->away_score ?? '' }}"
                    class="w-16 text-center border border-gray-300 rounded-lg py-2 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-green-400"
                    placeholder="0"
                />
                <button type="submit"
                        class="ml-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    {{ $userPredictions->has($match->id) ? __('app.edit') : __('app.validate') }}
                </button>
            </div>
        </form>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow p-6 text-center text-gray-400">
        {{ __('app.nomatch') }}
    </div>
    @endforelse
    <script>
        document.querySelectorAll('.score-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const status = this.querySelector('.score-status');
                status.textContent = "{{ __('app.prediction_wait') }}...";

                // Traitement du formulaire courant
                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                    .then(response => {
                        console.log(response.status);
                        console.log(response.headers.get('content-type'));

                        return response.json();
                    })
                    .then(data => {
                        status.style.display = 'block';
                        if (data.success) {
                            status.textContent = "✓ {{ __('app.prediction_registered') }}";
                        } else {
                            status.textContent = "❌ {{ __('app.prediction_error') }}";
                        }
                    });
            });
        });
    </script>
</div>

{{-- Résultats récents --}}
<div>
    <h2 class="text-xl font-bold text-gray-800 mb-4">✅ {{ __('app.recent_results') }}</h2>
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        @forelse($finishedMatches as $match)
        @php $prediction = $match->predictions->first(); @endphp
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 last:border-0">
            <div class="flex items-center gap-3 flex-1">
                    <span class="text-sm font-semibold text-gray-700 text-right flex-1">
                        <img src="/images/flags/4x3/{{ strtolower($match->homeTeam->name) }}.svg" width="24"
                             class="inline-block">
                        {{ $match->homeTeam->displayName() }}
                    </span>
                <span class="bg-gray-800 text-white text-sm font-bold px-3 py-1 rounded-lg">
                        {{ $match->home_score }} – {{ $match->away_score }}
                    </span>
                <span class="text-sm font-semibold text-gray-700 flex-1">
                        <img src="/images/flags/4x3/{{ strtolower($match->awayTeam->name) }}.svg" width="24"
                             class="inline-block">
                        {{ $match->awayTeam->displayName() }}
                    </span>
            </div>
            <div class="ml-4 text-right min-w-[80px]">
                @if($prediction)
                <div class="text-xs text-gray-400">{{ __('app.your_prediction')}} : {{ $prediction->scoreLabel() }}
                </div>
                <div
                    class="text-sm font-bold {{ $prediction->points_earned > 0 ? 'text-green-600' : 'text-gray-400' }}">
                    +{{ $prediction->points_earned }} pt{{ $prediction->points_earned > 1 ? 's' : '' }}
                </div>
                @else
                <div class="text-xs text-gray-300">{{ __('app.noprediction') }}</div>
                @endif
            </div>
        </div>
        @empty
        <div class="px-5 py-4 text-center text-gray-400 text-sm">{{ __('app.noresult') }}</div>
        @endforelse
    </div>
</div>

@endsection
