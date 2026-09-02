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

                <div class="trk" hidden>
                    <div class="trk__error" hidden>Накладная с таким номером не найдена</div>

                    <div class="trk__card" hidden>
                        {{-- Сводка --}}
                        <div class="trk__summary">
                            <div class="trk__ident">
                                <div class="trk__num"></div>
                                <div class="trk__route"></div>
                                <div class="trk__created"></div>
                            </div>

                            <div class="trk__now">
                                <div class="trk__now-label">Текущий статус</div>
                                <div class="trk__now-body">
                                    <span class="trk__now-icon">
                                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    </span>
                                    <span class="trk__now-text"></span>
                                </div>
                            </div>

                            <div class="trk__facts">
                                <div class="trk__fact trk__fact_eta">
                                    <span class="trk__fact-ic">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                    </span>
                                    <span><b class="trk__fact-t"></b><em class="trk__fact-v"></em></span>
                                </div>
                                <div class="trk__fact trk__fact_ins">
                                    <span class="trk__fact-ic">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>
                                    </span>
                                    <span><b>Страхование груза</b><em class="trk__ins-v"></em></span>
                                </div>
                            </div>
                        </div>

                        <div class="trk__cancelled" hidden>Накладная отменена</div>

                        {{-- Две колонки --}}
                        <div class="trk__cols">
                            <div class="trk__panel">
                                <div class="trk__panel-title">История перемещения груза</div>
                                <ol class="trk__timeline"></ol>
                            </div>

                            <div class="trk__panel trk__panel_side">
                                <div class="trk__panel-title">Информация по отправлению</div>
                                <div class="trk__info"></div>
                                <a class="trk__pdf" href="#" target="_blank" rel="noopener" hidden>
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 15h6M9 18h4"/></svg>
                                    Скачать накладную (PDF)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@push('styles')
<style>
.trk { margin-top: 26px; }
.trk__error { padding: 16px 20px; border-radius: 12px; background: #fef2f2; color: #d0171c; font-weight: 600; }

.trk__card { color: #1a1a1a; }

/* Сводка */
.trk__summary {
    display: grid;
    grid-template-columns: 1.1fr 1fr 1fr;
    gap: 28px;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 26px 28px;
    box-shadow: 0 10px 30px rgba(16,24,40,.05);
}
.trk__num { font-size: 22px; font-weight: 800; letter-spacing: -.01em; }
.trk__route { margin-top: 8px; font-size: 16px; font-weight: 600; color: #333; }
.trk__created { margin-top: 8px; font-size: 13px; color: #9aa0a6; }

.trk__now { text-align: center; border-left: 1px solid #f0f0f0; border-right: 1px solid #f0f0f0; padding: 0 16px; }
.trk__now-label { font-size: 13px; color: #9aa0a6; }
.trk__now-body { margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 12px; }
.trk__now-icon { flex: none; width: 52px; height: 52px; border-radius: 50%; background: #fdecec; color: #d0171c; display: flex; align-items: center; justify-content: center; }
.trk__now-text { font-size: 17px; font-weight: 700; line-height: 1.25; text-align: left; }

.trk__facts { display: flex; flex-direction: column; justify-content: center; gap: 16px; }
.trk__fact { display: flex; align-items: flex-start; gap: 12px; font-size: 14px; }
.trk__fact-ic { flex: none; width: 34px; height: 34px; border-radius: 9px; background: #f4f6f8; color: #00056d; display: flex; align-items: center; justify-content: center; }
.trk__fact span:last-child { display: flex; flex-direction: column; }
.trk__fact b { font-weight: 500; color: #9aa0a6; font-size: 12.5px; }
.trk__fact em { font-style: normal; font-weight: 700; color: #1a1a1a; margin-top: 2px; }
.trk__ins-v.is-on { color: #28a745; }

.trk__cancelled { margin-top: 18px; padding: 14px 18px; border-radius: 12px; background: #fef2f2; color: #d0171c; font-weight: 700; }

/* Колонки */
.trk__cols { margin-top: 20px; display: grid; grid-template-columns: 1.4fr 1fr; gap: 20px; align-items: start; }
.trk__panel { background: #fff; border: 1px solid #ececec; border-radius: 16px; padding: 22px 24px; box-shadow: 0 10px 30px rgba(16,24,40,.05); }
.trk__panel-title { font-size: 17px; font-weight: 800; margin-bottom: 18px; }

/* Таймлайн */
.trk__timeline { list-style: none; margin: 0; padding: 0; }
.trk__timeline li { position: relative; display: flex; gap: 16px; padding-bottom: 22px; }
.trk__timeline li:last-child { padding-bottom: 0; }
.trk__timeline li::after { content: ''; position: absolute; left: 15px; top: 34px; bottom: -4px; width: 2px; background: #e8e8e8; }
.trk__timeline li:last-child::after { display: none; }
.trk__dot { flex: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; background: #f0f1f3; color: #9aa0a6; z-index: 1; }
.trk__timeline li.is-done .trk__dot { background: #28a745; color: #fff; }
.trk__timeline li.is-done .trk__dot::after { content: '\2713'; }
.trk__timeline li.is-current .trk__dot { background: #d0171c; color: #fff; box-shadow: 0 0 0 4px rgba(208,23,28,.15); }
.trk__row { flex: 1; display: flex; justify-content: space-between; align-items: baseline; gap: 12px; }
.trk__step-t { font-size: 15px; color: #6b7280; }
.trk__timeline li.is-done .trk__step-t { color: #1a1a1a; }
.trk__timeline li.is-current .trk__step-t { color: #1a1a1a; font-weight: 700; }
.trk__step-at { font-size: 12.5px; color: #9aa0a6; white-space: nowrap; }
.trk__timeline li.is-current { }
.trk__timeline li.is-current .trk__row { background: #fef2f2; margin: -6px -10px; padding: 6px 10px; border-radius: 8px; }

/* Информация */
.trk__info { display: flex; flex-direction: column; gap: 18px; }
.trk__inforow { display: flex; gap: 12px; align-items: flex-start; }
.trk__inforow-ic { flex: none; width: 36px; height: 36px; border-radius: 10px; background: #f4f6f8; color: #00056d; display: flex; align-items: center; justify-content: center; }
.trk__inforow-t { font-size: 12.5px; color: #9aa0a6; }
.trk__inforow-v { font-size: 15px; font-weight: 700; margin-top: 2px; }
.trk__inforow-sub { font-size: 13px; color: #6b7280; font-weight: 400; }

.trk__pdf { margin-top: 22px; display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; box-sizing: border-box; padding: 13px; border: 1px solid #d0171c; border-radius: 12px; color: #d0171c; font-weight: 700; font-size: 15px; transition: all .2s; }
.trk__pdf:hover { background: #d0171c; color: #fff; }

@media (max-width: 991px) {
    .trk__summary { grid-template-columns: 1fr; gap: 20px; }
    .trk__now { border: none; border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0; padding: 18px 0; }
    .trk__cols { grid-template-columns: 1fr; }
}
@media (max-width: 767px) {
    .trk__summary, .trk__panel { padding: 18px; }
    .trk__num { font-size: 19px; }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]');

    function digitsOnly(v) { return v.replace(/\D+/g, '').slice(0, 10); }
    function trimNum(v) { return String(v).replace(/(\.\d*?)0+$/, '$1').replace(/\.$/, ''); }
    function el(tag, cls, text) {
        var n = document.createElement(tag);
        if (cls) n.className = cls;
        if (text != null) n.textContent = text;
        return n;
    }

    // Этапы для таймлайна: детальная цепочка, если она есть, иначе
    // административная. Время подставляем только там, где оно реально известно.
    function timelineSteps(inv) {
        var rich = inv.detail_steps || [];
        var base = rich.length ? rich : (inv.steps || []);
        var times = inv.stage_times || {};
        return base.map(function (s, i) {
            var at = null;
            if (rich.length) {
                at = times[i] || null;
            } else if (i === 0) {
                at = inv.created_at || null;
            }
            return { title: s.title, state: s.state, at: at };
        });
    }

    function renderTimeline(list, steps) {
        list.innerHTML = '';
        steps.forEach(function (s, i) {
            var li = el('li', 'is-' + s.state);
            var dot = el('span', 'trk__dot');
            if (s.state !== 'done') dot.textContent = String(i + 1);
            var row = el('div', 'trk__row');
            row.appendChild(el('span', 'trk__step-t', s.title));
            if (s.at) row.appendChild(el('span', 'trk__step-at', s.at));
            li.appendChild(dot);
            li.appendChild(row);
            list.appendChild(li);
        });
    }

    var PARTY_IC = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    var BOX_IC = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8V5a1 1 0 0 0-.5-.87l-8-4.5a1 1 0 0 0-1 0l-8 4.5A1 1 0 0 0 3 8v8a1 1 0 0 0 .5.87l8 4.5a1 1 0 0 0 1 0l8-4.5A1 1 0 0 0 21 16Z"/><path d="M3.3 7 12 12l8.7-5M12 22V12"/></svg>';
    var SCALE_IC = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M5 21h14M6 7h12l3 7a5 5 0 0 1-6 0l3-7M6 7 3 14a5 5 0 0 0 6 0Z"/></svg>';

    function infoRow(icon, title, value, sub) {
        var r = el('div', 'trk__inforow');
        var ic = el('span', 'trk__inforow-ic');
        ic.innerHTML = icon;
        var body = el('div');
        body.appendChild(el('div', 'trk__inforow-t', title));
        var v = el('div', 'trk__inforow-v', value);
        if (sub) v.appendChild(el('span', 'trk__inforow-sub', '  ' + sub));
        body.appendChild(v);
        r.appendChild(ic);
        r.appendChild(body);
        return r;
    }

    function party(p) {
        if (!p) return '—';
        var name = (p.name || '').trim();
        return name || '—';
    }

    function currentStatusText(inv) {
        if (inv.cancelled) return inv.status_label || 'Отменена';
        var rich = inv.detail_steps || [];
        var cur = rich.filter(function (s) { return s.state === 'current'; })[0];
        if (cur) return cur.title;
        var done = rich.filter(function (s) { return s.state === 'done'; });
        if (done.length) return done[done.length - 1].title;
        return inv.status_label || '—';
    }

    function render(form, inv) {
        var card = form.querySelector('.trk__card');

        card.querySelector('.trk__num').textContent = 'Накладная №' + inv.number;
        card.querySelector('.trk__route').textContent =
            [inv.from, inv.to].filter(Boolean).join(' → ');
        card.querySelector('.trk__created').textContent =
            inv.created_at ? 'Создана: ' + inv.created_at : '';

        // Текущий статус: детальный этап, если цепочка ведётся, иначе общий.
        var now = card.querySelector('.trk__now-text');
        now.textContent = currentStatusText(inv);
        now.style.color = inv.cancelled ? '#d0171c' : '#1a1a1a';

        // Дата доставки: факт, если есть, иначе план
        var etaT = card.querySelector('.trk__fact_eta .trk__fact-t');
        var etaV = card.querySelector('.trk__fact_eta .trk__fact-v');
        if (inv.fact_date) { etaT.textContent = 'Доставлена'; etaV.textContent = inv.fact_date; }
        else if (inv.plan_date) { etaT.textContent = 'Ожидаемая дата доставки'; etaV.textContent = inv.plan_date; }
        else { etaT.textContent = 'Дата доставки'; etaV.textContent = 'уточняется'; }

        var insV = card.querySelector('.trk__ins-v');
        insV.textContent = inv.insured ? 'Оформлено' : 'Не оформлено';
        insV.classList.toggle('is-on', !!inv.insured);

        card.querySelector('.trk__cancelled').hidden = !inv.cancelled;

        // Таймлайн
        var timeline = card.querySelector('.trk__timeline');
        if (inv.cancelled) {
            timeline.innerHTML = '';
        } else {
            renderTimeline(timeline, timelineSteps(inv));
        }

        // Информация по отправлению
        var info = card.querySelector('.trk__info');
        info.innerHTML = '';
        info.appendChild(infoRow(PARTY_IC, 'Отправитель', party(inv.sender), inv.sender && inv.sender.city));
        info.appendChild(infoRow(PARTY_IC, 'Получатель', party(inv.recipient), inv.recipient && inv.recipient.city));
        if (inv.quantity) info.appendChild(infoRow(BOX_IC, 'Количество мест', trimNum(inv.quantity) + ' мест'));
        if (inv.weight) info.appendChild(infoRow(SCALE_IC, 'Вес груза', trimNum(inv.weight) + ' кг'));

        var pdf = card.querySelector('.trk__pdf');
        pdf.href = '/track/' + encodeURIComponent(inv.number) + '/pdf';
        pdf.hidden = false;

        card.hidden = false;
    }

    function initTrackForm(form) {
        var input = form.querySelector('input[name="invoice_number"]');
        var wrap = form.querySelector('.trk');
        var error = form.querySelector('.trk__error');
        var card = form.querySelector('.trk__card');

        input.addEventListener('input', function () { this.value = digitsOnly(this.value); });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var number = digitsOnly(input.value);
            wrap.hidden = false;
            card.hidden = true;
            error.hidden = true;
            if (!number) { error.hidden = false; return; }

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
                    if (data && data.found) render(form, data.invoice);
                    else error.hidden = false;
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
