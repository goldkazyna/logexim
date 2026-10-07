{{-- Стили полосы этапа доставки (partials.stage-progress / invoice-stage-cell). --}}
<style>
    .stage-prog__dots { display: flex; align-items: center; gap: 4px; }
    .stage-prog__dot { width: 10px; height: 10px; border-radius: 50%; background: #e2e5ea; display: block; }
    .stage-prog__dot.done { background: #16a34a; }
    .stage-prog__dot.cur { background: #d0171c; box-shadow: 0 0 0 3px rgba(208,23,28,.18); }
    .stage-prog__label { font-size: 12px; color: #555; margin-top: 5px; }
    .stage-cancelled { display: inline-block; font-size: 12px; font-weight: 600; color: #dc3545; background: #fdecec; padding: 4px 12px; border-radius: 999px; }
</style>
