{{-- Полоса прогресса этапа доставки (0..6). Параметр: $stage. --}}
@php $labels = \App\Models\Invoice::DETAIL_STATUSES; $cur = max(0, min(6, (int) $stage)); @endphp
<div class="stage-prog">
    <div class="stage-prog__dots">
        @for($i = 0; $i <= 6; $i++)
            <span class="stage-prog__dot {{ $i < $cur ? 'done' : ($i === $cur ? 'cur' : '') }}"></span>
        @endfor
    </div>
    <div class="stage-prog__label">{{ $labels[$cur] ?? '' }}</div>
</div>
