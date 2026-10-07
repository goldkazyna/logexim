{{-- Строка карточки накладной с правкой на месте: карандаш → поле ввода → дискетка.
     Параметры: $label, $field (колонка invoices), $value, $canEdit. --}}
<div class="inv-row"><div class="label">{{ $label }}:</div><div class="value">
    @if($canEdit)
    <span class="inv-inline" data-field="{{ $field }}">
        <span class="inv-inline__text">{{ $value !== null && $value !== '' ? $value : '—' }}</span>
        <input type="text" class="inv-edit-input inv-inline__input" value="{{ $value }}" maxlength="255" hidden>
        <button type="button" class="inv-inline__btn inv-inline__edit" title="Изменить"><i class="fas fa-pencil-alt"></i></button>
        <button type="button" class="inv-inline__btn inv-inline__save" title="Сохранить" hidden><i class="fas fa-save"></i></button>
        <span class="inv-inline__err" hidden></span>
    </span>
    @else
    {{ $value }}
    @endif
</div></div>
