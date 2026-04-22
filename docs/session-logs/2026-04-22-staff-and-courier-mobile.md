# 2026-04-22 — Сотрудники, мобильная роль курьера, pickup-флоу

Один день, два репозитория: бэкенд `logexim-b` (Laravel) + мобильное приложение `logexim_mobile` (Flutter).

## Общая картина

До этого дня в системе были только **админ** (один дефолтный через `admins`) и **клиент** (кабинет через `users`).
К концу дня появились:

- Раздел **«Сотрудники»** в админке — CRUD для курьеров и диспетчеров
- Единый логин `/admin` с fallback в таблицу `staff`
- Детальный статус накладной (7 этапов доставки)
- API для мобильного приложения сотрудника (Sanctum)
- Полноценный **флоу забора груза курьером с подписью отправителя** в мобильном приложении
- Конвертация подписи в WEBP на сервере
- Отображение забора + подписи в админской карточке

---

## Бэкенд (`logexim-b`)

### 1. Таблица `staff` и модель

**Миграция** `2026_04_22_000001_create_staff_table.php`:
- `id, full_name, login (UNIQUE), password, role, phone, email, note, active, timestamps`
- Пароль хранится в старой схеме `sha1(md5($plain))` — унаследовано от CodeIgniter для единообразия с `users` и `admins`.

**Модель** `App\Models\Staff`:
- `extends Authenticatable` (для Sanctum)
- `use HasApiTokens`
- Поле `password` в `$hidden`

### 2. Роли в админке

`AdminController::checkAuth(array $roles = ['admin'])`:
- Принимает список разрешённых ролей
- Дефолт `['admin']` — существующие методы продолжают работать как «только админ»

`AdminController::auth()`:
- 1) ищет в `admins` — если найден, сессия `role = admin`, редирект на `/admin/dashboard`
- 2) иначе ищет в `staff` (`active = 1`) — сессия `role = dispatcher|courier`, редирект на `/admin/invoices`

`AdminController::logout()` — `session()->flush()` вместо точечного forget.

### 3. Раздел «Сотрудники» — CRUD

`StaffController`:
- `index`, `create`, `store`, `edit`, `update`, `toggle`, `destroy`
- Доступ только для `role=admin` (через `checkAdmin()`)
- Валидация: `role in: dispatcher,courier`, уникальность `login`, пароль min:4; при `update` пароль опционален (пустой = не менять)
- Дополнительная проверка: `login` не должен совпадать с логином в `admins`

Роуты `/admin/staff/*` (GET/POST), все защищены `checkAdmin()`.

Views `resources/views/admin/staff/`: `index`, `create`, `edit` — в стиле существующей админки.

### 4. Меню админки по ролям

`resources/views/layouts/admin.blade.php`:
- Пункты «Дашборд», «Пользователи», «Сотрудники», «Заказы», «Новости», «Страницы», «Города», «Тарифы» — видны только при `role=admin`
- «Накладные» и «Выход» видны всем
- В шапке показывается `full_name (диспетчер)` / `full_name (курьер)` / `Администратор`

### 5. Доступ курьера/диспетчера к накладным

Методы `AdminController`:
- `invoices`, `viewInvoice`, `checkNewInvoices` — открыты для `['admin','dispatcher','courier']`
- `updateInvoiceStatus`, `updateInvoice` — доступны только `['admin','dispatcher']` (курьер в админке read-only)

`invoice_view.blade.php`:
- Для курьера: всё text-only, без селектов/инпутов/кнопки сохранения
- Скрыт блок «Информация об оплате» для всех кроме админа (поле `payment` тоже не пускается в update для не-админа)

`invoices_table.blade.php`:
- Для курьера — status-бейдж это span, не кнопка (нет кликабельности модалки смены статуса)
- Добавлена колонка «Курьер» для admin/dispatcher
- Добавлена колонка «Этап» — видна всем

### 6. Назначение курьера на накладную

**Миграция** `2026_04_22_000002_add_courier_id_to_invoices.php`:
- `courier_id` (nullable bigint, индекс) в `invoices`

`Invoice::courier()` — `belongsTo(Staff, 'courier_id')`.

UI в `invoice_view.blade.php` (для admin/dispatcher):
- Селект «Курьер» под статусом, под ним — селект «Этап доставки»
- Курьер видит только ФИО назначенного курьера как текст

`updateInvoice`:
- Принимает `courier_id` только от admin/dispatcher
- Принимает `detail_status` (0..6)
- **Автосмена:** если курьер назначается первый раз (пред. `courier_id` отличался) и `detail_status == 0` — ставится `1` («Назначен курьер»)

Фильтры для курьера:
- `invoices()` — только `where('courier_id', staff_id)`
- `viewInvoice($id)` — редирект на `/admin/invoices` если чужая
- `updateInvoiceStatus` / `updateInvoice` — недоступны курьеру (только admin/dispatcher могут менять)
- `checkNewInvoices` (AJAX-пуллинг) — фильтр по своему id
- Бейдж новых накладных в сайдбаре и начальный `lastId` для JS-полинга — в скоупе курьера

### 7. Детальный статус (7 этапов)

**Миграция** `2026_04_22_000003_add_detail_status_to_invoices.php`:
- `detail_status` (tinyint, default 0) в `invoices`

`Invoice::DETAIL_STATUSES`:
```
0 => Заявка создана
1 => Назначен курьер          (автоматически при назначении)
2 => Курьер забрал            (автоматически при pickup-flow мобилки)
3 => На складе                (вручную)
4 => Отправлено в пункт назначения
5 => У курьера в пункте назначения
6 => Доставлено               (будет ставиться автоматически при delivery-flow — не реализовано сегодня)
```

`Invoice::detailStatusLabel()` — хелпер.

### 8. API для мобильного приложения сотрудника (Sanctum)

Публичные:
- `POST /api/staff/auth` — `{login, password}`
  - 200: `{token, user: {id, login, full_name, role, phone, email}}`
  - 401: неверные данные
  - 403: аккаунт отключён
  - 422: валидация

Защищённые (middleware `auth:sanctum`):
- `POST /api/staff/logout` — инвалидация текущего токена
- `GET /api/staff/profile` — данные залогиненного
- `GET /api/staff/invoices` — список (курьер: свои, диспетчер: все; лимит 200)
- `GET /api/staff/invoices/by-number/{number}` — поиск по скану штрихкода
- `GET /api/staff/invoices/{id}` — детали
- `POST /api/staff/invoices/{id}/pickup` — приём подписи + перевод в `detail_status=2`

### 9. Pickup (забор груза): подпись → WEBP

**Миграция** `2026_04_22_000004_add_pickup_signature_to_invoices.php`:
- `pickup_signature` (string nullable) в `invoices`

`StaffInvoiceController::pickup`:
- Принимает `{signature: base64 PNG}` (либо data-URI `data:image/png;base64,...`)
- Проверка: `detail_status >= 2` → 422 «Накладная уже забрана»
- Конвертация PNG → WEBP через GD:
  - `imagecreatefromstring` → `imagewebp($im, null, 85)` + `ob_get_clean()` (без temp-файла — на хостинге `open_basedir` ограничивает `/tmp`)
  - Fallback в PNG если что-то не так
- Сохранение в `storage/app/public/signatures/pickup/{id}_{Ymd_His}.webp`
- Обновление: `pickup_signature = относительный путь`, `detail_status = 2`
- В ответе `invoice.pickup_signature_url` — полный URL (через `Storage::disk('public')->url(...)`)

Симлинк `public/storage` создан (`php artisan storage:link`).

### 10. Админская карточка: блок «Забор курьером»

В `invoice_view.blade.php` между «Хрупкий груз» и «Доставка»:
- Если `pickup_signature` есть — отдельная секция: имя курьера + миниатюра подписи (клик → открыть файл в новой вкладке)
- Если подписи нет — секция не рендерится

### 11. Расширение API-ответа накладной (под mobile)

`StaffInvoiceController::present()`:
- `sender` и `recipient` содержат `name, company, phone, address, city, region, district, country, full_address`
- `cargo` — `description, weight, volume_weight, quantity, fragile`
- На уровне накладной — `special` (особые инструкции), `status_key` (`assigned | in_delivery | delivered`)
- При `$full = true` добавляется `plan_date, fact_date, special, pickup_signature_url`

---

## Мобильное приложение (`logexim_mobile`)

Стек: Flutter, `ChangeNotifier` + `SharedPreferences`, пакет `http`, Sanctum Bearer-токены.

### 1. Auth

`AuthScreen` — вкладка «Сотрудник» теперь:
- Поле **«Логин»** вместо «Номер телефона»
- Кнопка «Войти» вызывает `POST /api/staff/auth`
- Спиннер во время запроса
- Ошибки API через `showErrorDialog`
- При успехе: `AuthService.saveAuth(token, user)` → переход на `CourierShell`

### 2. `CourierShell` — bottom nav

Четыре таба: **Заявки / Сканер / История / Профиль**.

Реализация:
- Не используется `IndexedStack` (иначе state не пересоздаётся)
- При каждом tap по табу меняется `Key` у соответствующего экрана → новый State → свежий `initState` → свежий API-запрос
- То же при повторном tap по активному табу

`CourierBottomNavBar` — 4 пункта с красной активной иконкой.

### 3. `CourierDashboardScreen`

- ФИО залогиненного из `AuthService.user['full_name']`
- Чипсы: Все / Ожидают забора / В доставке / Завершены — при смене триггерят `_load()` (запрос на сервер)
- `RefreshIndicator` поверх `ListView` (pull-to-refresh)
- Loading / Error / Empty состояния с кнопкой «Повторить»
- Карточка: номер, статус-бейдж, адрес отправки, компания
- При тапе — Detail, после возврата из Detail — автоматический `_load()`

### 4. `CourierOrderDetailScreen`

Stateful, держит `_order` в state:
- Секции как в админке: Отправитель, Получатель, Груз, Особые инструкции (если есть), Этапы доставки (timeline)
- Все поля адреса по отдельности: ФИО, Компания, Телефон, Страна, Область, Город, Район, Адрес
- Без цены
- Статус-бар сверху (цвет по `status_key`)
- Timeline — 7 этапов, текущий фиолетовый, готовые зелёные
- **Pull-to-refresh** (`_refresh()` → `fetchOrder(id)`)
- Кнопка «Сканировать накладную» — только если `status == assigned` (пропадает после забора)
- После возврата из сканера с сигналом `'pickedUp'` — автоматический `_refresh()`

### 5. `ScannerScreen` — реальный сканер

Пакет `mobile_scanner: ^5.2.3`.

Разрешения:
- Android: `android.permission.CAMERA` + `<uses-feature>`
- iOS: `NSCameraUsageDescription`

Реализация:
- Lifecycle-обработка через `WidgetsBindingObserver` (pause/resume → stop/start)
- Явный `_controller.start()` в `addPostFrameCallback`
- `MobileScanner(fit: BoxFit.cover)` в `Stack(fit: StackFit.expand)` — превью на всю область
- Полупрозрачная чёрная подложка, красная рамка со углами
- Форматы: QR + Code128/39 + EAN13/8 + ITF
- Torch toggle
- Manual input — диалог с полем номера
- При скане — `GET /api/staff/invoices/by-number/{number}`:
  - Если накладная не assigned → alert «Уже забрана / в доставке / доставлена»
  - Иначе → Pickup flow
- После успеха:
  - `Navigator.canPop == true` (пришли из Detail) → `Navigator.pop('pickedUp')`
  - `Navigator.canPop == false` (пришли через таб) → `Navigator.push(CourierOrderDetailScreen(обновлённая))`

### 6. Pickup flow: PickupConfirmation → Signature

`PickupConfirmationScreen`:
- Принимает `CourierOrder order` + `int invoiceId`
- Бейдж «Накладная найдена», номер, дата
- Секции: Отправитель, Получатель, Груз (с «Хрупкий»), Особые инструкции — как в админке
- Кнопки: «Подтвердить — груз принят» (зелёная) + «Отмена»
- После успеха Signature — получает обратно обновлённый `CourierOrder` и сам popит его наверх

`SignatureScreen`:
- `RepaintBoundary` вокруг канваса + `CustomPainter` (ручки, список штрихов)
- Проверка «Распишитесь на поле выше» если нет ни одного штриха
- Экспорт через `boundary.toImage(pixelRatio: 2.0) → toByteData(ui.ImageByteFormat.png)`
- Base64 → `CourierApi.confirmPickup(id, b64)` → сервер возвращает обновлённую накладную
- `AlertDialog`: **«Груз принят» / «Накладная #XXX забрана у отправителя. Статус обновлён.»**
- После ОК: `Navigator.pop(context, freshOrder)` → в PickupConfirmation → scanner или Detail

### 7. `CourierApi`

Сервис-обёртка над `ApiProvider.client`:
- `fetchOrders()` → `List<CourierOrder>`
- `fetchOrder(id)` → `CourierOrder`
- `findByNumber(number)` → `(id, order)`
- `confirmPickup(id, base64Png)` → `CourierOrder` (обновлённая)

Маппер:
- `id` сохраняется в модели (нужен для `fetchOrder`/`confirmPickup`)
- `status_key` мапится в `CourierOrderStatus` (assigned/in_delivery/delivered)
- `detail_status` → timeline из 7 элементов с состояниями done/current/pending

### 8. Профиль курьера

`CourierProfileScreen` — stateful:
- Аватар из инициалов реального имени
- ФИО, Телефон — из `AuthService.user`
- Бейдж «Курьер» или «Диспетчер» в зависимости от `role`
- «Выйти из аккаунта» → `POST /api/staff/logout` + `AuthService.logout()` + переход на `AuthScreen`

### 9. `SplashScreen` — маршрутизация по роли

При перезапуске приложения:
- Нет токена → `AuthScreen`
- `role in ['courier','dispatcher']` → `CourierShell`
- Иначе (клиент) → `ClientShell`

### 10. Дефолтные аккаунты для теста

Созданы через админку:
- Диспетчер `disp1` / `test123` (Ivanov Ivan)
- Курьер `cour1` / `test456` (Courier One, id=2)

---

## Текущий статус (к концу сессии)

**Работает end-to-end:**
1. Админ заходит на `/admin` → создаёт курьера через «Сотрудники»
2. Админ/диспетчер в карточке накладной выбирает курьера из селекта → сохраняет. Этап автоматически = «Назначен курьер»
3. Курьер логинится в мобильном приложении → видит свои заявки
4. Курьер сканирует штрихкод накладной камерой → экран подтверждения с деталями
5. «Подтвердить — груз принят» → экран подписи → отправитель рисует подпись → «Подтвердить»
6. Сервер конвертирует в WEBP, сохраняет, ставит `detail_status=2`
7. Alert «Груз принят» → Detail с обновлённым статусом (timeline шагнул)
8. Админ/диспетчер в админке видит в карточке блок «Забор курьером» с ФИО и картинкой-подписью

**Ещё не сделано:**
- Этапы `3..5` (На складе / Отправлено / У курьера в пункте) — только админ/диспетчер ставит их вручную через селект в админке. Нужен ли mobile-флоу для этого — обсудить.
- Этап **6 (Доставлено)** — нет автоматики. Нужен flow вроде pickup, но:
  - Сканирование накладной (или выбор из списка)
  - Экран подтверждения получателя
  - Подпись получателя → WEBP → `delivery_signature` (ещё не добавлена колонка)
  - `detail_status = 6`, возможно автоматом `status = 3` (Исполнена)
- История доставок курьера (`CourierHistoryScreen` сейчас на моках)
- Просмотр подписи в мобильном приложении (в Detail — мы её не рендерим)
- Разграничение функционала диспетчер vs курьер в админке (пока идентичны)
- Push-уведомления курьеру при назначении новой накладной

## Следующие приоритеты (обсудить)

1. **Доставка получателю** — зеркало pickup-флоу, самый естественный следующий шаг
2. История курьера — показать на Dashboard/History реальные доставки
3. Вывести подпись в mobile-Detail (админка уже показывает)
4. Автопереход основного `status` при финальных этапах (detail=6 → status=3)

## Commits (backend, 2026-04-22)

```
bf57a59 feat(admin): показ подписи отправителя в карточке накладной
9395ec9 fix(api): запрет повторного pickup для уже забранной накладной
71b0227 fix(api): конвертация подписи в WEBP без temp-файла
ebf1546 feat(api): детальные поля адреса/груза в ответе накладной
8f2ace6 feat(api): поиск накладной по номеру + приём подписи при заборе
65af024 feat(api): список накладных для мобильного приложения сотрудника
b486abc feat(api): авторизация сотрудников через Sanctum
b9c8720 feat(invoices): детальный статус (этап доставки) + автосмена
75259b5 feat(courier): read-only режим в карточке и списке накладных
f06bf37 feat(invoices): назначение курьера на накладную + фильтр для курьера
6dab7e0 feat(invoices): скрыть блок «Информация об оплате» для курьеров/диспетчеров
281f683 feat: условное меню админки по ролям + имя/роль в шапке
72ff184 feat: blade-шаблоны для раздела сотрудников (index/create/edit)
3d7cd23 feat: роуты /admin/staff/* для CRUD сотрудников
b3fc84e feat: StaffController — CRUD для курьеров/диспетчеров
223e04f feat(admin): роли в checkAuth + fallback-логин в таблицу staff
181707f feat: таблица staff + модель для курьеров/диспетчеров
5419c16 docs: план реализации раздела «Сотрудники»
84defcd docs: spec раздела «Сотрудники» (курьеры и диспетчера)
```

Мобильное приложение — git не инициализирован, изменения в рабочем дереве.

## Реквизиты для следующей сессии

- **Тестовый диспетчер:** `disp1` / `test123`
- **Тестовый курьер:** `cour1` / `test456` (id=2, назначен на несколько накладных)
- **База:** MySQL `logexim` локально
- **Laravel проект:** `C:\projects\logexim-b`
- **Flutter проект:** `C:\projects\logexim_mobile`
- **HTML-шаблоны:** `C:\projects\logexim_app` (screens-5/6 — курьер, screens-7 — не смотрели)
- **Прод-каталог:** неизвестен, обновление через `git pull origin main` + `php artisan migrate` + `php artisan storage:link` (если не был сделан) + clear кешей
