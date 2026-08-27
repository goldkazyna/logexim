{{-- Публичное отслеживание накладной по номеру. Подключается на главной
     и на странице «Найти посылку» — вёрстка и скрипт живут только здесь. --}}
<section class="search">
    <div class="container">
        <div class="search__wrapper">
            <form class="search__form form track-form">
                <h2>Найти посылку</h2>
                <div class="form__row">
                    <label class="form-text form_col-6">
                        <span class="form-text__desc form-text__desc_top">Введите номер накладной:</span>
                        <input type="text" name="invoice_number" inputmode="numeric" autocomplete="off" placeholder="903088" required>
                    </label>
                    <button type="submit" class="btn form__submit form_col-4">Продолжить</button>
                </div>

                <div class="track-result" hidden>
                    <div class="track-error" hidden>Накладная с таким номером не найдена</div>

                    <div class="track-card" hidden>
                        <div class="track-card__head">
                            <div class="track-card__title">
                                <div class="track-card__number"></div>
                                <div class="track-card__route"></div>
                                <div class="track-card__meta"></div>
                            </div>
                            <span class="track-card__badge"></span>
                        </div>

                        <div class="track-cancelled" hidden>Накладная отменена</div>

                        <ol class="track-chain"></ol>

                        <div class="track-detail" hidden>
                            <div class="track-detail__title">Подробно по доставке</div>
                            <ol class="track-chain track-chain_detail"></ol>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@push('styles')
<style>
.track-result { margin-top: 24px; }
.track-error { padding: 16px 20px; border-radius: 10px; background: #fff5f5; color: #dc3545; font-weight: 600; }

.track-card { background: #fff; border: 1px solid #e8e8e8; border-radius: 14px; padding: 24px; }
.track-card__head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
.track-card__number { font-size: 20px; font-weight: 700; color: #1a1a1a; }
.track-card__route { margin-top: 4px; font-size: 15px; color: #444; }
.track-card__meta { margin-top: 4px; font-size: 13px; color: #999; }
.track-card__badge { padding: 8px 18px; border-radius: 20px; font-size: 13px; font-weight: 600; color: #fff; white-space: nowrap; }

.track-cancelled { margin-top: 20px; padding: 14px 18px; border-radius: 10px; background: #fff5f5; color: #dc3545; font-weight: 600; }

.track-chain { list-style: none; margin: 24px 0 0; padding: 0; }
.track-chain li { position: relative; padding: 0 0 22px 32px; font-size: 15px; color: #999; }
.track-chain li:last-child { padding-bottom: 0; }
/* Точка этапа */
.track-chain li::before { content: ''; position: absolute; left: 0; top: 3px; width: 14px; height: 14px; border-radius: 50%; background: #fff; border: 2px solid #d8d8d8; box-sizing: border-box; }
/* Линия до следующего этапа */
.track-chain li::after { content: ''; position: absolute; left: 6px; top: 19px; bottom: 2px; width: 2px; background: #e8e8e8; }
.track-chain li:last-child::after { display: none; }
.track-chain li.is-done { color: #1a1a1a; }
.track-chain li.is-done::before { background: #28a745; border-color: #28a745; }
.track-chain li.is-done::after { background: #28a745; }
.track-chain li.is-current { color: #1a1a1a; font-weight: 700; }
.track-chain li.is-current::before { background: #d0171c; border-color: #d0171c; box-shadow: 0 0 0 4px rgba(208, 23, 28, .15); }

.track-detail { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; }
.track-detail__title { font-size: 13px; text-transform: uppercase; letter-spacing: .04em; color: #999; }

@media (max-width: 767px) {
    .track-card { padding: 18px; }
    .track-card__number { font-size: 17px; }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]');

    function digitsOnly(value) {
        return value.replace(/\D+/g, '').slice(0, 10);
    }

    // "20.00" -> "20", "1.50" -> "1.5"
    function trimNumber(value) {
        return String(value).replace(/(\.\d*?)0+$/, '$1').replace(/\.$/, '');
    }

    function renderChain(list, steps) {
        list.innerHTML = '';
        steps.forEach(function (step) {
            var li = document.createElement('li');
            li.className = 'is-' + step.state;
            li.textContent = step.title;
            list.appendChild(li);
        });
    }

    function describe(invoice) {
        var parts = [];
        if (invoice.created_at) parts.push('Создана ' + invoice.created_at);
        if (invoice.quantity) parts.push(trimNumber(invoice.quantity) + ' мест');
        if (invoice.weight) parts.push(trimNumber(invoice.weight) + ' кг');
        if (invoice.fact_date) parts.push('Доставлена ' + invoice.fact_date);
        else if (invoice.plan_date) parts.push('План ' + invoice.plan_date);
        return parts.join(' · ');
    }

    function render(form, invoice) {
        var card = form.querySelector('.track-card');
        var badge = card.querySelector('.track-card__badge');
        var route = [invoice.from, invoice.to].filter(Boolean).join(' → ');

        card.querySelector('.track-card__number').textContent = 'Накладная №' + invoice.number;
        card.querySelector('.track-card__route').textContent = route;
        card.querySelector('.track-card__meta').textContent = describe(invoice);

        badge.textContent = invoice.status_label;
        badge.style.background = invoice.status_color;
        // Жёлтый бейдж «Принята в работу» нечитаем белым текстом.
        badge.style.color = invoice.status_color === '#ffcc00' ? '#333' : '#fff';

        card.querySelector('.track-cancelled').hidden = !invoice.cancelled;
        renderChain(card.querySelector('.track-chain'), invoice.steps || []);

        var detail = card.querySelector('.track-detail');
        var hasDetail = (invoice.detail_steps || []).length > 0;
        detail.hidden = !hasDetail;
        if (hasDetail) {
            renderChain(detail.querySelector('.track-chain_detail'), invoice.detail_steps);
        }

        card.hidden = false;
    }

    function initTrackForm(form) {
        var input = form.querySelector('input[name="invoice_number"]');
        var result = form.querySelector('.track-result');
        var error = form.querySelector('.track-error');
        var card = form.querySelector('.track-card');

        input.addEventListener('input', function () {
            this.value = digitsOnly(this.value);
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var number = digitsOnly(input.value);
            result.hidden = false;
            card.hidden = true;
            error.hidden = true;

            if (!number) {
                error.hidden = false;
                return;
            }

            fetch('/ajax/trackInvoice', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF ? CSRF.getAttribute('content') : ''
                },
                body: JSON.stringify({ invoice_number: number })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data && data.found) {
                        render(form, data.invoice);
                    } else {
                        error.hidden = false;
                    }
                })
                .catch(function () {
                    error.textContent = 'Не удалось получить статус. Попробуйте позже.';
                    error.hidden = false;
                });
        });
    }

    document.querySelectorAll('form.track-form').forEach(initTrackForm);
})();
</script>
@endpush
