{{-- Строка карточки накладной с правкой на месте: карандаш → поле ввода → дискетка.
     Параметр: $field (см. App\Support\InvoiceInlineFields). Из родителя: $invoice, $canEdit. --}}
@php
    $F = \App\Support\InvoiceInlineFields::class;
    $type = $F::type($field);
    $input = $F::inputValue($invoice, $field);
@endphp
<div class="inv-row"><div class="label">{{ $F::label($field) }}:</div><div class="value">
    @if($canEdit)
    <span class="inv-inline" data-field="{{ $field }}" data-type="{{ $type }}">
        <span class="inv-inline__text">{{ $F::display($invoice, $field) }}</span>
        <span class="inv-inline__form" hidden>
            @switch($type)
                @case('textarea')
                    <textarea class="inv-edit-input inv-inline__input" rows="3">{{ $input }}</textarea>
                    @break
                @case('int')
                    <input type="number" min="1" step="1" class="inv-edit-input inv-inline__input" value="{{ $input }}">
                    @break
                @case('decimal')
                    <input type="number" min="0" step="0.01" class="inv-edit-input inv-inline__input" value="{{ $input }}">
                    @break
                @case('bool')
                    <select class="inv-edit-input inv-inline__input">
                        <option value="1" @selected($input === '1')>Да</option>
                        <option value="0" @selected($input === '0')>Нет</option>
                    </select>
                    @break
                @case('methods')
                    @foreach($F::PAYMENT_METHODS as $col => $title)
                        <label class="inv-inline__check"><input type="checkbox" value="{{ $col }}" @checked(in_array($col, $input, true))> {{ $title }}</label>
                    @endforeach
                    @break
                @default
                    <input type="text" class="inv-edit-input inv-inline__input" value="{{ $input }}">
            @endswitch
        </span>
        <button type="button" class="inv-inline__btn inv-inline__edit" title="Изменить"><i class="fas fa-pencil-alt"></i></button>
        <button type="button" class="inv-inline__btn inv-inline__save" title="Сохранить" hidden><i class="fas fa-save"></i></button>
        <span class="inv-inline__err" hidden></span>
    </span>
    @else
    {{ $F::display($invoice, $field) }}
    @endif
</div></div>
