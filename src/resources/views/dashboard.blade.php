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
                <th class="px-4 py-3 text-right">{{ __('app.predictions') }}</th>
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
                <td class="px-4 py-3 text-right text-gray-500">
                    {{ $player->predictions_count ?? 0 }} / {{ $totalFixturesCount }}
                </td>
                <td class="px-4 py-3 text-right font-bold text-green-700">
                    {{ $player->predictions_sum_points_earned ?? 0 }} pts
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-4 text-center text-gray-400">{{ __('app.noplayer') }}</td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Fixtures to predict --}}
<div class="mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-800">📋 {{ __('app.upcoming_matches') }}</h2>
        <span class="text-sm font-medium text-gray-500">
            <span class="font-bold text-green-600">{{ $userPredictions->count() }}</span>
            <span class="text-gray-400">/</span>
            {{ $upcomingMatches->count() }} {{ __('app.predicted') }}
        </span>
    </div>
    @forelse($upcomingMatches as $match)
    @php $existingPrediction = $userPredictions->get($match->id); @endphp
    <div class="bg-white rounded-2xl shadow p-5 mb-4">
        <form action="{{ route('predictions.store.json', $match) }}" class="score-form" method="POST">
            @csrf
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-gray-400 uppercase tracking-wide">
                    {{ $match->phaseLabel() }} · {{ $match->getLocalPlayedAt()->format('d/m/Y H:i') }}
                </span>
                @if($existingPrediction)
                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full score-status">✓ {{ __('app.prediction_registered') }}</span>
                @else
                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full score-status"
                      style="display: none"></span>
                @endif
            </div>

            {{-- Équipes + score --}}
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

            {{-- Bouton cotes --}}
            <div class="relative flex justify-center mb-3">
                <button type="button"
                        onclick="toggleOdds({{ $match->id }}, this)"
                        class="odds-btn text-xs text-gray-400 hover:text-green-600 underline underline-offset-2 transition">
                    📊 {{ __('app.see_odds') }}
                </button>

                {{-- Popover --}}
                <div id="odds-popover-{{ $match->id }}"
                    class="hidden absolute bottom-8 left-1/2 -translate-x-1/2 z-10
                            bg-white border border-gray-200 shadow-lg rounded-xl p-4 w-72">

                    {{-- Flèche --}}
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-white border-r border-b border-gray-200 rotate-45"></div>

                    {{-- Contenu chargé en AJAX --}}
                    <div id="odds-content-{{ $match->id }}" class="text-center text-xs text-gray-400">
                        Chargement...
                    </div>
                </div>
            </div>

            {{-- Saisie du score --}}
            <div class="flex items-center justify-center gap-3 mb-4">
                <input
                    type="number" name="home_score" min="0" max="20"
                    value="{{ $existingPrediction?->home_score ?? '' }}"
                    class="w-16 text-center border border-gray-300 rounded-lg py-2 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-green-400"
                    placeholder="0"
                />
                <span class="text-gray-400 font-bold">–</span>
                <input
                    type="number" name="away_score" min="0" max="20"
                    value="{{ $existingPrediction?->away_score ?? '' }}"
                    class="w-16 text-center border border-gray-300 rounded-lg py-2 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-green-400"
                    placeholder="0"
                />
                <button type="submit"
                        class="ml-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    {{ $existingPrediction ? __('app.edit') : __('app.validate') }}
                </button>
            </div>

            {{-- Sélecteur de vainqueur — phases éliminatoires uniquement --}}
            @if($match->isKnockout())
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 text-center mb-3 uppercase tracking-wide font-medium">
                    🏅 {{ __('app.who_advances') }}
                </p>
                <div class="flex gap-3 justify-center">
                    {{-- Bouton Home --}}
                    <label class="winner-option flex-1 max-w-[160px]">
                        <input type="radio" name="predicted_winner" value="home" class="sr-only"
                               {{ $existingPrediction?->predicted_winner === 'home' ? 'checked' : '' }}>
                        <span class="winner-btn flex items-center justify-center gap-2 border-2 rounded-xl py-2 px-3 cursor-pointer text-sm font-semibold transition
                                     {{ $existingPrediction?->predicted_winner === 'home'
                                          ? 'border-green-500 bg-green-50 text-green-700'
                                          : 'border-gray-200 text-gray-600 hover:border-green-300 hover:bg-green-50' }}">
                            <img src="/images/flags/4x3/{{ strtolower($match->homeTeam->name) }}.svg" width="20" class="inline-block">
                            {{ $match->homeTeam->displayName() }}
                        </span>
                    </label>

                    {{-- Bouton Away --}}
                    <label class="winner-option flex-1 max-w-[160px]">
                        <input type="radio" name="predicted_winner" value="away" class="sr-only"
                               {{ $existingPrediction?->predicted_winner === 'away' ? 'checked' : '' }}>
                        <span class="winner-btn flex items-center justify-center gap-2 border-2 rounded-xl py-2 px-3 cursor-pointer text-sm font-semibold transition
                                     {{ $existingPrediction?->predicted_winner === 'away'
                                          ? 'border-green-500 bg-green-50 text-green-700'
                                          : 'border-gray-200 text-gray-600 hover:border-green-300 hover:bg-green-50' }}">
                            <img src="/images/flags/4x3/{{ strtolower($match->awayTeam->name) }}.svg" width="20" class="inline-block">
                            {{ $match->awayTeam->displayName() }}
                        </span>
                    </label>
                </div>
            </div>
            @endif

        </form>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow p-6 text-center text-gray-400">
        {{ __('app.nomatch') }}
    </div>
    @endforelse

    <script>
        // Highlight visuel des boutons radio vainqueur
        document.querySelectorAll('.winner-option').forEach(label => {
            label.addEventListener('click', () => {
                const form = label.closest('form');
                form.querySelectorAll('.winner-btn').forEach(btn => {
                    btn.classList.remove('border-green-500', 'bg-green-50', 'text-green-700');
                    btn.classList.add('border-gray-200', 'text-gray-600');
                });
                label.querySelector('.winner-btn').classList.add('border-green-500', 'bg-green-50', 'text-green-700');
                label.querySelector('.winner-btn').classList.remove('border-gray-200', 'text-gray-600');
            });
        });

        // Soumission AJAX
        document.querySelectorAll('.score-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const status = this.querySelector('.score-status');
                status.textContent = "{{ __('app.prediction_wait') }}...";
                status.style.display = 'block';

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            status.textContent = "✓ {{ __('app.prediction_registered') }}";
                            status.className = 'text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full score-status';
                        } else {
                            status.textContent = "❌ {{ __('app.prediction_error') }}";
                            status.className = 'text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full score-status';
                        }
                    });
            });
        });
    </script>
</div>
<script>
async function toggleOdds(fixtureId, btn) {
    const popover = document.getElementById(`odds-popover-${fixtureId}`);
    const content = document.getElementById(`odds-content-${fixtureId}`);

    // Toggle
    if (!popover.classList.contains('hidden')) {
        popover.classList.add('hidden');
        return;
    }

    popover.classList.remove('hidden');

    // Ne recharge pas si déjà chargé
    if (content.dataset.loaded) return;

    try {
        const response = await fetch(`/fixtures/${fixtureId}/odds`);

        if (!response.ok) {
            content.innerHTML = '<span class="text-gray-300">Aucune cote disponible</span>';
            return;
        }

        const data = await response.json();
        const { odds, probabilities, home_team, away_team } = data;

        content.innerHTML = `
            <p class="text-xs text-gray-400 mb-3 font-medium uppercase tracking-wide">📊 Cotes moyennes</p>
            <div class="flex justify-between gap-2 mb-3">
                <div class="flex-1 bg-green-50 rounded-lg p-2 text-center">
                    <div class="font-bold text-green-700 text-sm">${odds.home}</div>
                    <div class="text-gray-400 text-xs truncate">${home_team}</div>
                    <div class="text-green-600 font-semibold text-xs mt-1">${probabilities.home}%</div>
                </div>
                <div class="flex-1 bg-gray-50 rounded-lg p-2 text-center">
                    <div class="font-bold text-gray-600 text-sm">${odds.draw}</div>
                    <div class="text-gray-400 text-xs">Nul</div>
                    <div class="text-gray-500 font-semibold text-xs mt-1">${probabilities.draw}%</div>
                </div>
                <div class="flex-1 bg-red-50 rounded-lg p-2 text-center">
                    <div class="font-bold text-red-600 text-sm">${odds.away}</div>
                    <div class="text-gray-400 text-xs truncate">${away_team}</div>
                    <div class="text-red-500 font-semibold text-xs mt-1">${probabilities.away}%</div>
                </div>
            </div>
            <p class="text-xs text-gray-300">Moyenne de ${data.bookmakers_count} bookmakers</p>
        `;

        content.dataset.loaded = true;

    } catch (e) {
        content.innerHTML = '<span class="text-red-400">Erreur de chargement</span>';
    }
}

// Ferme la popover en cliquant ailleurs
document.addEventListener('click', (e) => {
    if (!e.target.closest('.odds-btn') && !e.target.closest('[id^="odds-popover-"]')) {
        document.querySelectorAll('[id^="odds-popover-"]').forEach(p => p.classList.add('hidden'));
    }
});
</script>

{{-- Résultats récents --}}
<div>
    <h2 class="text-xl font-bold text-gray-800 mb-4">✅ {{ __('app.recent_results') }}</h2>
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        @forelse($finishedMatches as $match)
        @php $prediction = $match->predictions->first(); @endphp

        <div class="flex flex-col px-5 py-4 border-b border-gray-100 last:border-0 gap-2">

            {{-- Ligne 1 : phase + date --}}
            <span class="text-xs text-gray-400 uppercase tracking-wide">
                {{ $match->phaseLabel() }} · {{ $match->getLocalPlayedAt()->format('d/m/Y H:i') }}
            </span>

            {{-- Ligne 2 : équipes + score --}}
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-gray-700 flex-1 flex items-center justify-end gap-1 text-right">
                    {{ $match->homeTeam->displayName() }}
                    <img src="/images/flags/4x3/{{ strtolower($match->homeTeam->name) }}.svg" width="20" class="inline-block shrink-0">
                </span>

                <div class="text-center shrink-0">
                    <span class="bg-gray-800 text-white text-sm font-bold px-2 py-1 rounded-lg whitespace-nowrap">
                        {{ $match->home_score }} – {{ $match->away_score }}
                    </span>
                    {{-- Display the actual winner in knockout stages. --}}
                    @if($match->isKnockout() && $match->winner)
                        <div class="text-xs text-gray-400 mt-1">
                            ➡️ {{ $match->winner === 'home' ? $match->homeTeam->displayName() : $match->awayTeam->displayName() }}
                        </div>
                    @endif
                </div>

                <span class="text-sm font-semibold text-gray-700 flex-1 flex items-center gap-1">
                    <img src="/images/flags/4x3/{{ strtolower($match->awayTeam->name) }}.svg" width="20" class="inline-block shrink-0">
                    {{ $match->awayTeam->displayName() }}
                </span>
            </div>

            {{-- Ligne 3 : prédiction + points --}}
            @if($prediction)
                <div class="flex items-center justify-end gap-2">
                    <div class="text-xs text-gray-400">
                        {{ __('app.your_prediction') }} :
                        @if($match->isKnockout())
                            {{ $prediction->scoreLabel() }}
                            @if($prediction->predicted_winner)
                                · {{ $prediction->predicted_winner === 'home' ? $match->homeTeam->displayName() : $match->awayTeam->displayName() }}
                            @else
                                · <span class="text-orange-400">{{ __('app.no_winner_predicted') }}</span>
                            @endif
                        @else
                            {{ $prediction->scoreLabel() }}
                        @endif
                    </div>
                    <div class="text-sm font-bold shrink-0 {{ $prediction->points_earned > 0 ? 'text-green-600' : 'text-gray-400' }}">
                        +{{ $prediction->points_earned }} pt{{ $prediction->points_earned > 1 ? 's' : '' }}
                    </div>
                </div>
            @else
                <div class="flex items-center justify-end gap-2">
                    <span class="text-xs text-gray-300">{{ __('app.noprediction') }}</span>
                </div>
            @endif

        </div>

        @empty
        <div class="px-5 py-4 text-center text-gray-400 text-sm">{{ __('app.noresult') }}</div>
        @endforelse
    </div>
</div>

@endsection
