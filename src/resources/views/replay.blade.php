@extends('layouts.app')
@section('title', __('app.replay'))

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">⏱️ {{ __('app.replay_title') }}</h2>
    <p class="text-sm text-gray-400 mt-1">{{ __('app.replay_subtitle') }}</p>
</div>

{{--
    $competitionDays : Collection of Carbon dates (UTC midnight) — all distinct days that have at least one played fixture
    $allPlayers      : Collection of User models (same as leaderboard)
    $snapshotsByDay  : Associative array keyed by date string 'Y-m-d' →
                         each value is an array of players ordered by points desc,
                         with attributes: id, name, total_points, total_predictions
    Example shape for a single day entry:
    [
      'id'                  => 1,
      'name'                => 'Alice',
      'total_points'        => 12,
      'total_predictions'   => 5,
    ]
--}}

@php
    $days = $competitionDays->map(fn($d) => $d->format('Y-m-d'))->values()->toArray();
    $lastIndex = count($days) - 1;
    // Default to the last day (most recent)
    $defaultIndex = $lastIndex;
@endphp

{{-- ───────── Day Slider ───────── --}}
<div class="bg-white rounded-2xl shadow p-5 mb-6">
    <div class="flex items-center justify-between mb-3">
        <button id="prev-day"
                class="p-2 rounded-full hover:bg-gray-100 transition disabled:opacity-30 disabled:cursor-not-allowed"
                aria-label="Previous day">
            ◀
        </button>

        <div class="flex-1 mx-4">
            <div class="text-center mb-2">
                <span id="day-label" class="text-sm font-semibold text-green-700 bg-green-50 px-3 py-1 rounded-full"></span>
            </div>
            <input
                id="day-slider"
                type="range"
                min="0"
                max="{{ $lastIndex }}"
                value="{{ $defaultIndex }}"
                step="1"
                class="w-full accent-green-500 cursor-pointer"
            />
            <div class="flex justify-between text-xs text-gray-400 mt-1">
                <span>{{ \Carbon\Carbon::parse($days[0])->format('d/m') }}</span>
                <span>{{ \Carbon\Carbon::parse($days[$lastIndex])->format('d/m') }}</span>
            </div>
        </div>

        <button id="next-day"
                class="p-2 rounded-full hover:bg-gray-100 transition disabled:opacity-30 disabled:cursor-not-allowed"
                aria-label="Next day">
            ▶
        </button>
    </div>

    {{-- Mini timeline of days --}}
    <div class="flex gap-1 justify-center flex-wrap mt-3" id="day-pills">
        @foreach($days as $i => $day)
        <button
            data-index="{{ $i }}"
            class="day-pill text-xs px-2 py-1 rounded-full border transition
                   {{ $i === $defaultIndex ? 'bg-green-500 text-white border-green-500' : 'border-gray-200 text-gray-500 hover:border-green-300 hover:text-green-600' }}">
            {{ \Carbon\Carbon::parse($day)->format('d/m') }}
        </button>
        @endforeach
    </div>
</div>

{{-- ───────── Leaderboard snapshot ───────── --}}
<div id="leaderboard-wrapper">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-bold text-gray-800">🏆 {{ __('app.leaderboard') }}</h3>
        <span id="snapshot-subtitle" class="text-xs text-gray-400"></span>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm" id="replay-table">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left w-8">#</th>
                    <th class="px-4 py-3 text-left">{{ __('app.player') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('app.predictions') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('app.points') }}</th>
                    <th class="px-4 py-3 text-center w-16">{{ __('app.trend') }}</th>
                </tr>
            </thead>
            <tbody id="replay-tbody" class="divide-y divide-gray-100">
                {{-- Filled by JS --}}
            </tbody>
        </table>
    </div>
</div>

{{-- ───────── JS ───────── --}}
<script>
(function () {
    // All snapshot data passed from PHP → JS
    const days       = @json($days);
    const snapshots  = @json($snapshotsByDay);   // { 'Y-m-d': [ {id, name, total_points, total_predictions}, … ] }
    const currentUserId = {{ auth()->id() }};

    const slider       = document.getElementById('day-slider');
    const prevBtn      = document.getElementById('prev-day');
    const nextBtn      = document.getElementById('next-day');
    const dayLabel     = document.getElementById('day-label');
    const subtitle     = document.getElementById('snapshot-subtitle');
    const tbody        = document.getElementById('replay-tbody');
    const pills        = document.querySelectorAll('.day-pill');

    // Format a date string 'Y-m-d' → 'Lundi 12 juin 2025' (French locale if available)
    function formatDay(dateStr) {
        const d = new Date(dateStr + 'T00:00:00Z');
        return d.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' });
    }

    // Render the leaderboard for a given day index
    function render(index) {
        const day      = days[index];
        const snapshot = snapshots[day] ?? [];

        // Previous day snapshot for delta calculation
        const prevDay      = index > 0 ? days[index - 1] : null;
        const prevSnapshot = prevDay ? (snapshots[prevDay] ?? []) : null;

        // Build rank map for previous day: playerId → rank (1-based)
        const prevRankMap = {};
        if (prevSnapshot) {
            prevSnapshot.forEach((p, i) => { prevRankMap[p.id] = i + 1; });
        }

        // Update UI chrome
        dayLabel.textContent  = formatDay(day);
        subtitle.textContent  = index === 0
            ? '{{ __('app.replay_first_day') }}'
            : '{{ __('app.replay_vs_previous') }}';

        prevBtn.disabled = index === 0;
        nextBtn.disabled = index === days.length - 1;

        // Update pills
        pills.forEach(pill => {
            const i = parseInt(pill.dataset.index, 10);
            if (i === index) {
                pill.classList.add('bg-green-500', 'text-white', 'border-green-500');
                pill.classList.remove('border-gray-200', 'text-gray-500');
            } else {
                pill.classList.remove('bg-green-500', 'text-white', 'border-green-500');
                pill.classList.add('border-gray-200', 'text-gray-500');
            }
        });

        // Build rows
        if (snapshot.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">{{ __('app.noplayer') }}</td></tr>`;
            return;
        }

        tbody.innerHTML = snapshot.map((player, i) => {
            const rank     = i + 1;
            const isMe     = player.id === currentUserId;
            const rowClass = isMe ? 'bg-green-50 font-semibold' : '';

            // Rank badge
            let rankBadge;
            if (rank === 1)      rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-400 text-white text-xs font-bold">1</span>`;
            else if (rank === 2) rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-300 text-white text-xs font-bold">2</span>`;
            else if (rank === 3) rankBadge = `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-600 text-white text-xs font-bold">3</span>`;
            else                 rankBadge = `<span class="text-gray-400">${rank}</span>`;

            // Trend
            let trendHtml;
            if (!prevSnapshot || prevRankMap[player.id] === undefined) {
                trendHtml = `<span class="text-gray-300 text-xs">—</span>`;
            } else {
                const prevRank = prevRankMap[player.id];
                const delta    = prevRank - rank; // positive = moved up
                if (delta > 0) {
                    trendHtml = `
                        <span class="inline-flex flex-col items-center">
                            <span class="text-green-500 text-base leading-none">▲</span>
                            <span class="text-green-600 text-xs font-bold">+${delta}</span>
                        </span>`;
                } else if (delta < 0) {
                    trendHtml = `
                        <span class="inline-flex flex-col items-center">
                            <span class="text-red-400 text-base leading-none">▼</span>
                            <span class="text-red-500 text-xs font-bold">${delta}</span>
                        </span>`;
                } else {
                    trendHtml = `<span class="text-gray-300 text-sm">＝</span>`;
                }
            }

            return `
            <tr class="${rowClass} transition-colors duration-200">
                <td class="px-4 py-3">${rankBadge}</td>
                <td class="px-4 py-3">
                    ${player.name}
                    ${isMe ? `<span class="text-green-600 text-xs ml-1">(vous)</span>` : ''}
                </td>
                <td class="px-4 py-3 text-right text-gray-500">${player.total_predictions}</td>
                <td class="px-4 py-3 text-right font-bold text-green-700">${player.total_points} pts</td>
                <td class="px-4 py-3 text-center">${trendHtml}</td>
            </tr>`;
        }).join('');
    }

    // Events
    slider.addEventListener('input', () => render(parseInt(slider.value, 10)));

    prevBtn.addEventListener('click', () => {
        const v = Math.max(0, parseInt(slider.value, 10) - 1);
        slider.value = v;
        render(v);
    });

    nextBtn.addEventListener('click', () => {
        const v = Math.min(days.length - 1, parseInt(slider.value, 10) + 1);
        slider.value = v;
        render(v);
    });

    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            const i = parseInt(pill.dataset.index, 10);
            slider.value = i;
            render(i);
        });
    });

    // Initial render
    render(parseInt(slider.value, 10));
})();
</script>

@endsection