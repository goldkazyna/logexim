{{-- Полоса прогресса этапа доставки.
     Параметры: $labels (список названий этапов), $current (индекс текущего). --}}
@php
    $labels = array_values($labels);
    $last = count($labels) - 1;
    $cur = max(0, min($last, (int) $current));
@endphp
<div class="stage-prog">
    <div class="stage-prog__dots">
        @foreach($labels as $i => $label)
            <span class="stage-prog__dot {{ $i < $cur ? 'done' : ($i === $cur ? 'cur' : '') }}"></span>
        @endforeach
    </div>
    <div class="stage-prog__label">{{ $labels[$cur] ?? '' }}</div>
</div>
