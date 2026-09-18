{{-- Ячейка «Этап доставки»: отменена — плашкой, локальная — короткой полосой,
     иначе полная цепочка. Параметр: $inv. --}}
@php
    $stageByStatus = [0 => 0, 1 => 1, 2 => 4, 3 => 6, 4 => 0];
    $stage = (int) $inv->detail_status > 0 ? (int) $inv->detail_status : ($stageByStatus[(int) $inv->status] ?? 0);
@endphp
@if((int) $inv->status === 4)
    <span class="stage-cancelled">Отменена</span>
@elseif($inv->isLocalDelivery())
    @php $localPos = $stage >= 6 ? 2 : ($stage >= 2 ? 1 : 0); @endphp
    @include('partials.stage-progress', ['labels' => array_values(\App\Models\Invoice::LOCAL_DETAIL_STATUSES), 'current' => $localPos])
@else
    @include('partials.stage-progress', ['labels' => array_values(\App\Models\Invoice::DETAIL_STATUSES), 'current' => $stage])
@endif
