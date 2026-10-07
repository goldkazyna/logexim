{{-- Ячейка «Этап доставки»: отменена — плашкой, локальная — короткой полосой,
     иначе полная цепочка. Параметры: $inv, $public (клиентские названия этапов). --}}
@php
    $stage = $inv->effectiveStage();
    $public = $public ?? false;
@endphp
@if((int) $inv->status === 4)
    <span class="stage-cancelled">Отменена</span>
@elseif($inv->isLocalDelivery())
    @php $localPos = $stage >= 6 ? 2 : ($stage >= 2 ? 1 : 0); @endphp
    @include('partials.stage-progress', ['labels' => array_values($public ? \App\Models\Invoice::LOCAL_DETAIL_STATUSES : \App\Models\Invoice::LOCAL_STAFF_STATUSES), 'current' => $localPos])
@else
    @include('partials.stage-progress', ['labels' => array_values($public ? \App\Models\Invoice::PUBLIC_DETAIL_STATUSES : \App\Models\Invoice::DETAIL_STATUSES), 'current' => $stage])
@endif
