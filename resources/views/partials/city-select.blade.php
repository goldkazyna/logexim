{{-- Выбор города из справочника (CityDelivery). Вручную вписать нельзя.
     Параметры: $name, $id, $selected, $cities. Если сохранённое значение
     вне справочника (старые данные) — добавляем его отдельным пунктом,
     чтобы не потерять. --}}
@php
    $selected = trim((string) ($selected ?? ''));
    $titles = $cities->pluck('title')->all();
    $orphan = $selected !== '' && ! in_array($selected, $titles, true);
@endphp
<select id="{{ $id }}" name="{{ $name }}" class="form-input w-full md:w-1/2" required>
    <option value="" @selected($selected === '')>Выберите город</option>
    @if($orphan)
        <option value="{{ $selected }}" selected>{{ $selected }}</option>
    @endif
    @foreach($cities as $city)
        <option value="{{ $city->title }}" @selected($selected === $city->title)>{{ $city->title }}</option>
    @endforeach
</select>
