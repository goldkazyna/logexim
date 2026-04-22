# Раздел «Сотрудники»: курьеры и диспетчера

**Дата:** 2026-04-22
**Статус:** Утверждён к реализации

## Цель

Добавить в админ-панель раздел управления персоналом (курьеры и диспетчера) с полным CRUD, чтобы админ мог создавать учётные записи, а курьеры/диспетчера входили через ту же форму `/admin` и видели накладные. В будущем эти же роли будут использоваться в мобильном приложении.

## Контекст

Проект — миграция с CodeIgniter на Laravel. Существующие авторизации:
- Админ — таблица `admins`, URL `/admin`, хеш `sha1(md5($password))`, сессия `admin`.
- Клиент кабинета — таблица `users`, URL `/cabinet/auth`, тот же хеш, сессия `bin`.

Авторизация сессионная (не guards), проверка — метод `checkAuth()` в `AdminController`. Новая схема должна быть совместима со старой и переиспользовать тот же алгоритм хеширования.

## Область реализации

### Входит в объём

- Миграция и модель `Staff`
- Единая форма логина `/admin` — fallback в таблицу `staff` после `admins`
- CRUD раздела «Сотрудники» (`/admin/staff*`), доступен только админу
- Доступ к существующим `/admin/invoices*` для всех трёх ролей
- Скрытие остальных разделов меню для не-админов
- Отображение имени + роли залогиненного в шапке

### Не входит в объём (YAGNI)

- Привязка накладных к конкретному курьеру/диспетчеру (`courier_id`)
- Разграничение функционала между диспетчером и курьером (пока идентичны)
- API для мобильного приложения (отдельная задача)
- Логи действий / аудит
- Восстановление пароля (админ сбросит через форму редактирования)
- Инвалидация сессии деактивированного сотрудника (только при следующем логине)

## Архитектура

### 1. База данных

Новая таблица `staff`:

| Поле        | Тип                    | Примечание                        |
|-------------|------------------------|-----------------------------------|
| id          | bigint, PK             |                                   |
| full_name   | varchar(255)           | ФИО                               |
| login       | varchar(100), UNIQUE   |                                   |
| password    | varchar(255)           | `sha1(md5($plain))`               |
| role        | varchar(20)            | `'dispatcher'` или `'courier'`    |
| phone       | varchar(50), nullable  |                                   |
| email       | varchar(255), nullable |                                   |
| note        | text, nullable         | Заметки                           |
| active      | tinyint, default 1     | 0 — деактивирован                 |
| created_at  | timestamp              |                                   |
| updated_at  | timestamp              |                                   |

Миграция не сидит дефолтных записей — админ создаёт сотрудников через UI.

### 2. Модель `App\Models\Staff`

- `protected $table = 'staff'`
- `$fillable`: все поля кроме `id`, `created_at`, `updated_at`
- Без hidden/casts (проект не использует guards Laravel)

### 3. Авторизация

Единая форма `/admin`. В `AdminController::auth()` логика:

```
1. Ищем в admins по login + sha1(md5(password))
   → найден: session(['admin' => login, 'role' => 'admin'])
              redirect /admin/dashboard
2. Иначе ищем в staff по login + sha1(md5(password)) + active=1
   → найден: session([
                'staff_id' => id,
                'staff_login' => login,
                'role' => role,         // 'dispatcher' | 'courier'
                'full_name' => full_name,
             ])
              redirect /admin/invoices
3. Иначе — back + errors "Неверный логин или пароль"
```

Хелпер `checkAuth($roles = ['admin'])`:
- принимает массив разрешённых ролей;
- если `session('role')` не входит в список — redirect `/admin`;
- дефолт `['admin']` сохраняет существующее поведение для всех текущих методов.

Logout `/admin/logout` — `session()->flush()` и редирект на `/admin` (как сейчас).

### 4. Роуты

Добавить в `routes/web.php`:

```
/admin/staff                GET    StaffController@index
/admin/staff/create         GET    StaffController@create
/admin/staff                POST   StaffController@store
/admin/staff/{id}/edit      GET    StaffController@edit
/admin/staff/{id}           POST   StaffController@update
/admin/staff/{id}/toggle    POST   StaffController@toggle
/admin/staff/{id}/delete    POST   StaffController@destroy
```

Все методы вызывают `checkAuth(['admin'])`.

В существующих методах накладных (`invoices`, `invoiceShow`, `invoiceStatus`, `invoicePrint`, массовая печать и т.п.) заменить `checkAuth()` на `checkAuth(['admin', 'dispatcher', 'courier'])`.

Остальные админские методы (users, news, pages, cities, tariffs, dashboard) оставляют `checkAuth()` (= только admin).

### 5. Контроллер `StaffController`

```php
index()        // список, без пагинации (сотрудников мало)
create()       // форма
store(Request) // валидация + Staff::create(), пароль = sha1(md5($plain))
edit($id)      // форма с данными
update($id)    // валидация + обновление; пароль — только если не пустой
toggle($id)    // флип active (0 ↔ 1)
destroy($id)   // Staff::destroy($id)
```

Каждый метод начинается с `$this->checkAdmin()` (обёртка над `checkAuth(['admin'])`).

### 6. Валидация

| Поле       | Правила (create)                  | Правила (update)                        |
|------------|-----------------------------------|-----------------------------------------|
| full_name  | required, string, max:255         | required, string, max:255               |
| login      | required, string, max:100, unique:staff | required, string, max:100, unique:staff,$id |
| password   | required, string, min:4           | nullable, string, min:4                 |
| role       | required, in:dispatcher,courier   | required, in:dispatcher,courier         |
| phone      | nullable, string, max:50          | nullable, string, max:50                |
| email      | nullable, email, max:255          | nullable, email, max:255                |
| note       | nullable, string                  | nullable, string                        |

Дополнительно: `login` не должен совпадать ни с одним `login` в таблице `admins` (защита от коллизии — при логине админы проверяются первыми, иначе staff-запись с таким логином просто не залогинится).

### 7. Views

**Layout `resources/views/layouts/admin.blade.php`:**

- Пункт "Сотрудники" (`/admin/staff`) — виден только при `session('role') === 'admin'`
- Все пункты кроме "Накладные" и "Выход" — только для админа
- "Накладные" — виден всем трём ролям
- В шапке рядом с "Выход": `{{ full_name }} ({{ role_label }})` для staff, `admin` для админа

**Новые views:**

- `resources/views/admin/staff/index.blade.php`
  - Таблица: ФИО | Логин | Роль | Телефон | Статус | Действия
  - Действия: [Редактировать] [Вкл/Выкл] [Удалить]
  - Кнопка "Добавить сотрудника" сверху
- `resources/views/admin/staff/create.blade.php`
  - Форма со всеми полями, пароль обязательный
- `resources/views/admin/staff/edit.blade.php`
  - Форма со всеми полями, пароль необязательный (пустое = не менять)

Стили и разметка — по образцу существующих таблиц/форм админки.

## Edge-кейсы

- **Коллизия логина с admins** — валидатор `store/update` отдельно проверяет отсутствие такого логина в `admins`.
- **Деактивированный сотрудник** — не пускается в `auth()`, уже вошедший доработает сессию (приемлемо сейчас).
- **Удаление сотрудника** — жёсткое (`destroy`), связи нет (накладные не привязаны).

## План тестирования (ручной)

1. Миграция создаёт таблицу.
2. Логин админа по-прежнему работает, ведёт на `/admin/dashboard`.
3. Админ открывает `/admin/staff`, видит пустой список.
4. Создание диспетчера → появляется в списке.
5. Логин диспетчера → ведёт на `/admin/invoices`, меню урезано.
6. Диспетчер пытается открыть `/admin/staff` → редирект на `/admin`.
7. Создание курьера → логин → те же права.
8. Деактивация → логин больше не проходит.
9. Смена пароля через edit (новый пароль) и без (старый остаётся).
10. Удаление сотрудника → логин больше не проходит.
