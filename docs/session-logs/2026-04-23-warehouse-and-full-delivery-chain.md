# 2026-04-23 — Кладовщик, полная цепочка, аудит, подпись получателя

Продолжение работы после вчерашней сессии. К концу дня вся **жизненная цепочка накладной покрыта end-to-end**: от создания клиентом до финальной доставки получателю с подписью.

## Финальная цепочка переходов

| detail | Название этапа | Триггер | Подпись |
|--------|----------------|---------|---------|
| 0 → 1 | Назначен курьер | admin/dispatcher выбрал `courier_id` | — |
| 1 → 2 | Курьер забрал | mobile: pickup + подпись **отправителя** | ✓ WEBP |
| 2 → 3 | На складе | mobile: кладовщик → receive | — |
| 3 → 4 | Отправлено в пункт назначения | mobile: кладовщик → ship | — |
| 4 → 5 | У курьера в пункте назначения | mobile: курьер-получатель → destination-pickup | — |
| 5 → 6 | **Доставлено** (status=Исполнена) | mobile: курьер-получатель → deliver + подпись **получателя** | ✓ WEBP |

Каждый переход защищён жёсткой state machine — пропуск или повтор даёт 422 с понятным сообщением.

---

## Backend

### 1. Аудит переходов

**Таблица `invoice_events`** (миграция `2026_04_23_000001_create_invoice_events_table.php`):
- `id, invoice_id, event (64), from_detail_status, to_detail_status, actor_type (admin|staff), actor_id, actor_role, actor_name, meta (json), created_at`
- Индексы: invoice_id, event, created_at

**Модель `InvoiceEvent`** с `EVENT_LABELS`:
```
courier_assigned             Назначен курьер
receiving_courier_assigned   Назначен курьер-получатель
pickup                       Курьер забрал груз
warehouse_receive            Принято на склад
warehouse_ship               Отправлено со склада
destination_pickup           Курьер забрал со склада назначения
delivery                     Доставлено получателю
status_changed               Изменён основной статус
detail_changed               Изменён этап доставки
```

Лог пишется:
- **В Api\StaffInvoiceController** через `logEvent($invoice, $staff, $event, ...)`
- **В AdminController** через `logAdminEvent(...)` — при изменении `courier_id`, `receiving_courier_id`, `detail_status`, `status`

### 2. Роль `warehouse`

- Добавлена в валидацию `StaffController` (`in:dispatcher,courier,warehouse`)
- Новое поле `staff.warehouse_location` (nullable string) — локация склада
- В формах `admin/staff/create.blade.php` и `edit.blade.php` — селект «Кладовщик» + раскрывающееся поле «Локация склада» (JS)
- `StaffAuthController::presentStaff` отдаёт `warehouse_location` в `/api/staff/auth` и `/profile`

### 3. Колонки на накладной

**Миграция `2026_04_23_000002_add_warehouse_columns.php`:**
- `invoices.warehouse_id` (nullable, индекс) — кто сейчас держит на складе
- `invoices.received_at` — когда принято на склад
- `invoices.shipped_at` — когда отправлено
- `staff.warehouse_location`

**Миграция `2026_04_23_000003_add_receiving_courier_id.php`:**
- `invoices.receiving_courier_id` (nullable, индекс) — курьер в пункте назначения

**Миграция `2026_04_23_000004_add_delivery_columns.php`:**
- `invoices.delivery_signature` — путь к WEBP подписи получателя
- `invoices.delivered_at` — время доставки

В `Invoice` добавлены отношения: `warehouse()`, `receivingCourier()`, `events()`.

### 4. API endpoints (все — `auth:sanctum` + role-check)

```
GET  /api/staff/invoices                     — курьер/кладовщик/диспетчер
GET  /api/staff/invoices/by-number/{number}  — сканер
GET  /api/staff/invoices/{id}                — детали
GET  /api/staff/dashboard                    — сводка кладовщика
GET  /api/staff/history                      — история операций кладовщика

POST /api/staff/invoices/{id}/pickup              — курьер: 0/1 → 2 (подпись отправителя)
POST /api/staff/invoices/{id}/receive             — кладовщик: 2 → 3
POST /api/staff/invoices/{id}/ship                — кладовщик: 3 → 4 (свой склад)
POST /api/staff/invoices/{id}/destination-pickup  — курьер-получатель: 4 → 5
POST /api/staff/invoices/{id}/deliver             — курьер-получатель: 5 → 6 (подпись получателя)
```

**Фильтры в `index`:**
- Курьер: где `courier_id = me` ИЛИ `receiving_courier_id = me`
- Кладовщик: все с `detail=2` (к приёмке) + свои с `detail=3` (к отправке)

**Доступ в `show/findByNumber`:** курьер видит накладную если он либо `courier_id`, либо `receiving_courier_id`.

**`present()` отдаёт:** `courier_id/name`, `receiving_courier_id/name`, `warehouse_name/location`, `status_key`, `detail_status/label`, `created_at`, полные адреса/груз. `full=true` — `plan_date, fact_date, received_at, shipped_at, delivered_at, pickup_signature_url, delivery_signature_url`.

### 5. State machine (жёсткие проверки)

- **pickup:** role=courier, courier_id=me, detail ≤ 1
- **receive:** role=warehouse, detail=2
- **ship:** role=warehouse, detail=3, warehouse_id=me
- **destination-pickup:** role=courier, receiving_courier_id=me, detail=4
- **deliver:** role=courier, receiving_courier_id=me, detail=5

Любой неправильный переход → 422 с сообщением `«Накладная уже ... — текущий этап: ...»` или `«Накладная ещё не ...»`.

### 6. Подписи (WEBP через GD без temp-файла)

`saveSignatureAsWebp($invoiceId, $bin, $kind = 'pickup'|'delivery')`:
- `imagecreatefromstring` → `imagewebp($im, null, 85)` + `ob_get_clean()`
- Сохранение в `storage/app/public/signatures/{kind}/{id}_{Ymd_His}.webp`
- Fallback → PNG если конвертация провалилась

### 7. Админская карточка накладной

- **Первый селект** — «Курьер (отправка)»
- **Второй селект** — «Курьер (приём в пункте)»
- `updateInvoice` принимает оба, автосмена detail=0→1 при первом назначении основного курьера
- Все изменения логируются в `invoice_events`
- **Блок «История накладной»** — timeline событий с ФИО актёра и переходом «этап → этап»
- **Блок «Забор курьером»** — ФИО + миниатюра подписи отправителя (если есть `pickup_signature`)
- **Блок «Доставка получателю»** — ФИО курьера-получателя, дата доставки, миниатюра подписи получателя (если есть `delivery_signature`)

---

## Mobile (Flutter)

### Модуль `features/warehouse/` (новый)

- `WarehouseShell` — 4 таба: Главная / Сканер / История / Профиль; пересоздание State при tap по табу
- `WarehouseDashboardScreen`:
  - ФИО + локация склада сверху
  - 3 цветных счётчика (оранжевый К приёмке / синий К отправке / зелёный Сегодня)
  - **2 кнопки в ряд, контент в столбец** (иконка сверху, текст снизу): «Принять груз» (оранж) / «Отправить груз» (синяя)
  - Секции **«Ожидают приёмки»** и **«На вашем складе — к отправке»** — кликабельные плитки накладных → детальный экран (тот же `CourierOrderDetailScreen`)
  - «Последние операции» — тоже кликабельные, грузят по `invoiceId` и открывают detail
- `WarehouseScannerPickerScreen` (таб «Сканер») — выбор режима двумя большими картами (Приёмка/Отправка)
- `WarehouseScannerScreen(mode)` — реальный сканер с цветовой схемой по режиму (оранж/синий), бейдж «ПРИЁМКА»/«ОТПРАВКА», тот же `mobile_scanner` lifecycle. **Если detail не соответствует режиму — открывается detail screen с timeline** (видно где накладная), а не confirm
- `WarehouseConfirmScreen(mode)` — полная карточка без подписи, кнопки «Принять на склад» / «Подтвердить отправку» в цвете режима
- `WarehouseHistoryScreen` — чипсы периода, группировка по датам, цветные иконки операций
- `WarehouseProfileScreen` — оранжевый аватар, бейдж «Кладовщик», ФИО/Телефон/Склад, «Выйти»
- `WarehouseApi` — `fetchDashboard`, `fetchInvoices` (переиспользует `CourierApi.fetchOrders`), `findByNumber`, `receive`, `ship`, `fetchHistory`, `fetchInvoice`

### Модуль `features/courier/` (расширен)

- **`CourierOrder`** получил поля `id`, `detailStatus`, `courierId`, `receivingCourierId`
- **`CourierApi`**:
  - `fetchOrder(id)`, `findByNumber(number)`, `fetchOrders()` — как и было
  - `confirmPickup(id, base64)` → `CourierOrder` (первичный забор с подписью отправителя)
  - `destinationPickup(id)` → `CourierOrder` (забор со склада назначения, без подписи)
  - `confirmDelivery(id, base64)` → `CourierOrder` (доставка получателю с подписью)
  - **`_buildTimeline`**: если `detail == 6` — **все 7 этапов зелёные** (процесс завершён, «current» не рисуется)
- **`ScannerScreen`** курьера — умная логика по `detail_status` и ролям:
  - `detail ≤ 1 + courier_id=me` → `PickupConfirmationScreen` (подпись отправителя)
  - `detail == 4 + receiving_courier_id=me` → `DestinationPickupScreen` (простое подтверждение)
  - Иначе → `CourierOrderDetailScreen` (timeline)
  - После успеха: если `canPop` — вверх по стэку с сигналом; иначе — push detail с обновлённым order
- **`CourierOrderDetailScreen`**:
  - Stateful, pull-to-refresh, автообновление после возврата из scanner
  - Кнопка внизу **вариативная**:
    - `_canDeliver == true` (detail=5 + я receiving) → **зелёная «Отдать получателю»** → `DeliverySignatureScreen`
    - `detail ≤ 1 + status=assigned` → красная «Сканировать накладную»
    - Иначе — кнопки нет
  - Хелпер `_currentUserId()` — обрабатывает `user['id']` и как `num`, и как `String` (AuthService хранит всё строками)
- **`PickupConfirmationScreen` + `SignatureScreen`** — первичный забор с подписью отправителя (было со вчера)
- **`DestinationPickupScreen`** — новый экран, фиолетовые акценты, без подписи, кнопка «Подтвердить — груз получен»
- **`DeliverySignatureScreen`** — новый, зелёные акценты, канвас подписи получателя, кнопка «Подтвердить — доставлено», alert «Накладная доставлена»

### Splash/Auth routing по ролям
- `warehouse` → `WarehouseShell`
- `courier|dispatcher` → `CourierShell`
- Иначе → `ClientShell`

### Правила UX
- **Только `AlertDialog`**, никаких SnackBar
- Все переходы статусов → обновление экрана без повторной навигации пользователя
- Pull-to-refresh на всех списках (Dashboard, Detail, История)
- При неподходящем состоянии накладной (сканер курьера или кладовщика) — всегда показывается детальный экран с timeline, а не техническая ошибка

---

## Тестовые учётки

- Админ: `admin` / `7774333822`
- Диспетчер: `disp1` / `test123`
- Курьер: `cour1` / `test456` (id=2; сейчас одновременно и `courier_id`, и `receiving_courier_id` — в продакшене будут разные)
- Кладовщик: `ware1` / `test789` (локация: «Алматы — Центральный»)

---

## Commits (backend, 2026-04-23)

```
03b62b3 feat: финал цепочки — доставка получателю с подписью (detail 5→6)
1c16d5a feat: курьер-получатель в пункте назначения (detail 4→5)
af49989 feat(api): warehouse_location в ответах /staff/auth и /staff/profile
8c63356 feat: роль «Кладовщик» + state machine + аудит накладных
```

Mobile — git не инициализирован, изменения в рабочем дереве.

---

## Что ещё можно сделать (не сделано сегодня)

- **Delivery-подпись** в мобильном Detail — сейчас только в админке. Можно показать картинку в секции «Доставка».
- **Разделение dispatcher vs courier в админке**: сейчас оба ведут себя одинаково (оба могут менять статусы/назначать). Потом ограничим диспетчера (например, без доступа к финансам).
- **Реальная история для курьера** (`CourierHistoryScreen` всё ещё на моках).
- **Push-уведомления**: кладовщику — когда курьер забрал; receiving courier — когда склад отправил; клиенту — когда доставлено.
- **Несколько складов**: сейчас `warehouse_location` — просто строка. Если понадобится маршрутизация между несколькими городами, возможно, нужен отдельный справочник складов.
- **Отмена накладной на любом этапе** — сейчас нет явного флоу «отменить», есть только общий `status=4` (Отменена). Мобилка не умеет.
- **Счётчик «Сегодня»** в dashboard кладовщика — считает события `warehouse_receive/ship` текущего пользователя за сегодня. Проверить на проде что timezone ОК.

## Подготовка к следующей сессии

- Читать это логи + `2026-04-22-staff-and-courier-mobile.md` для полного контекста
- В памяти уже обновлён `project_migration_status.md`
- Прод-deploy:
  ```bash
  git pull origin main
  php artisan migrate
  php artisan view:clear
  php artisan config:clear
  php artisan route:clear
  ```
  `php artisan storage:link` уже выполнен вчера, повторно не нужен.
