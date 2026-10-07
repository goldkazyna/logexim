{{-- Отправитель / получатель в списке накладных: компания, ФИО, город.
     Параметры: $company, $name, $city, $cityLabel («Откуда» / «Куда»). --}}
{{ $company }}<br><small>{{ $name }}</small>
@if(trim((string) $city) !== '')
<div class="party-city"><span>{{ $cityLabel }}:</span> {{ $city }}</div>
@endif
