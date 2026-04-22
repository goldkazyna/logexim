# Раздел «Сотрудники» — план реализации

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) или superpowers:executing-plans для выполнения задач одна за другой. Шаги используют checkbox (`- [ ]`) синтаксис.

**Goal:** Добавить CRUD-раздел `/admin/staff` для курьеров и диспетчеров, с единой формой логина `/admin` и доступом для ролей admin/dispatcher/courier к накладным.

**Architecture:** Отдельная таблица `staff` с полем `role` (`dispatcher` | `courier`). Таблица `admins` не трогается. В `AdminController::auth()` — fallback: сначала admins, потом staff. `checkAuth()` принимает массив ролей. Существующее поведение для админа сохраняется (в сессию кладём `role=admin`).

**Tech Stack:** Laravel 11, MySQL, Blade, sha1(md5) хеширование паролей (legacy CodeIgniter).

**Проект без автотестов** — вместо unit-тестов ручные проверки через браузер и `php artisan tinker`.

---

## Спека

Полная спека: `docs/superpowers/specs/2026-04-22-staff-couriers-dispatchers-design.md`

---

## Files overview

**Create:**
- `database/migrations/2026_04_22_000001_create_staff_table.php`
- `app/Models/Staff.php`
- `app/Http/Controllers/StaffController.php`
- `resources/views/admin/staff/index.blade.php`
- `resources/views/admin/staff/create.blade.php`
- `resources/views/admin/staff/edit.blade.php`

**Modify:**
- `app/Http/Controllers/AdminController.php` — `auth()`, `checkAuth($roles)`, `logout()`, проверки в методах накладных
- `routes/web.php` — роуты `/admin/staff/*`
- `resources/views/layouts/admin.blade.php` — условное меню по ролям, имя/роль в шапке
- `resources/views/admin/auth.blade.php` — не трогаем (форма та же)

---

## Task 1: Миграция и модель Staff

**Files:**
- Create: `database/migrations/2026_04_22_000001_create_staff_table.php`
- Create: `app/Models/Staff.php`

- [ ] **Step 1.1: Создать миграцию**

Файл `database/migrations/2026_04_22_000001_create_staff_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('login', 100)->unique();
            $table->string('password');
            $table->string('role', 20); // dispatcher | courier
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
```

- [ ] **Step 1.2: Создать модель Staff**

Файл `app/Models/Staff.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'full_name',
        'login',
        'password',
        'role',
        'phone',
        'email',
        'note',
        'active',
    ];
}
```

- [ ] **Step 1.3: Запустить миграцию**

```bash
php artisan migrate
```

Expected output: `INFO  Running migrations. ... 2026_04_22_000001_create_staff_table ... DONE`

- [ ] **Step 1.4: Проверить таблицу**

```bash
php artisan tinker --execute="print_r(\Schema::getColumnListing('staff'));"
```

Expected: массив с `id, full_name, login, password, role, phone, email, note, active, created_at, updated_at`.

- [ ] **Step 1.5: Commit**

```bash
git add database/migrations/2026_04_22_000001_create_staff_table.php app/Models/Staff.php
git commit -m "feat: таблица staff + модель для курьеров/диспетчеров"
```

---

## Task 2: AdminController — роли в checkAuth и auth()

**Files:**
- Modify: `app/Http/Controllers/AdminController.php`

Контекст: сейчас `checkAuth()` проверяет только `session('admin')`. Нужно расширить — принимать массив ролей. Существующие вызовы `$this->checkAuth()` без аргументов должны продолжать работать как «только admin».

- [ ] **Step 2.1: Добавить use для Staff**

В начале файла `app/Http/Controllers/AdminController.php` после `use App\Models\Admin;` добавить:

```php
use App\Models\Staff;
```

- [ ] **Step 2.2: Переписать метод checkAuth()**

Заменить текущий `checkAuth()` (строки 19-23):

```php
    private function checkAuth(array $roles = ['admin'])
    {
        $role = session('role');
        if (!$role || !in_array($role, $roles, true)) {
            return redirect('/admin');
        }
        return null;
    }
```

- [ ] **Step 2.3: Переписать метод auth()**

Заменить текущий `auth()` (строки 32-42) — добавить fallback в staff:

```php
    public function auth(Request $request)
    {
        $login = $request->input('login');
        $passwordHash = sha1(md5($request->input('password')));

        // 1) Admin
        $admin = Admin::where('login', $login)->where('password', $passwordHash)->first();
        if ($admin) {
            session(['admin' => $login, 'role' => 'admin']);
            return redirect('/admin/dashboard');
        }

        // 2) Staff (dispatcher | courier)
        $staff = Staff::where('login', $login)
            ->where('password', $passwordHash)
            ->where('active', 1)
            ->first();
        if ($staff) {
            session([
                'staff_id' => $staff->id,
                'staff_login' => $staff->login,
                'role' => $staff->role,
                'full_name' => $staff->full_name,
            ]);
            return redirect('/admin/invoices');
        }

        return view('admin.auth', ['error' => 'Неверный логин или пароль']);
    }
```

- [ ] **Step 2.4: Обновить authForm()**

Заменить `authForm()` (строки 26-30):

```php
    public function authForm()
    {
        if (session('role')) {
            return session('role') === 'admin'
                ? redirect('/admin/dashboard')
                : redirect('/admin/invoices');
        }
        return view('admin.auth', ['error' => '']);
    }
```

- [ ] **Step 2.5: Обновить logout()**

Заменить `logout()` (строки 44-48):

```php
    public function logout()
    {
        session()->flush();
        return redirect('/admin');
    }
```

- [ ] **Step 2.6: Обновить проверки в методах накладных**

В следующих методах AdminController заменить `$this->checkAuth()` на `$this->checkAuth(['admin', 'dispatcher', 'courier'])`:

- `invoices()` — строка 115
- `viewInvoice()` — строка 144
- `updateInvoiceStatus()` — строка 151
- `updateInvoice()` — строка 158

Также `checkNewInvoices()` (строки 87-90) — изменить первую проверку: заменить `if (!session('admin'))` на `if (!in_array(session('role'), ['admin', 'dispatcher', 'courier'], true))`.

Все остальные методы (users, dashboard, orders, news, pages, cities, tariffs) **НЕ трогать** — их `checkAuth()` без аргументов продолжит работать как «только admin».

- [ ] **Step 2.7: Ручная проверка — админ по-прежнему логинится**

Запустить сервер:

```bash
php artisan serve
```

В браузере `http://127.0.0.1:8000/admin` → логин `admin` / пароль `7774333822` → должен попасть на `/admin/dashboard`. Кликнуть «Выход» — редирект на `/admin`.

- [ ] **Step 2.8: Commit**

```bash
git add app/Http/Controllers/AdminController.php
git commit -m "feat(admin): роли в checkAuth + fallback-логин в таблицу staff"
```

---

## Task 3: StaffController

**Files:**
- Create: `app/Http/Controllers/StaffController.php`

- [ ] **Step 3.1: Создать контроллер**

Файл `app/Http/Controllers/StaffController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    private function checkAdmin()
    {
        if (session('role') !== 'admin') {
            return redirect('/admin');
        }
        return null;
    }

    public function index()
    {
        if ($r = $this->checkAdmin()) return $r;
        $staff = Staff::orderBy('id', 'desc')->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        if ($r = $this->checkAdmin()) return $r;
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        if ($r = $this->checkAdmin()) return $r;

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'login'     => 'required|string|max:100|unique:staff,login',
            'password'  => 'required|string|min:4',
            'role'      => 'required|in:dispatcher,courier',
            'phone'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'note'      => 'nullable|string',
            'active'    => 'nullable',
        ]);

        // Запрет коллизии с admins
        if (Admin::where('login', $data['login'])->exists()) {
            return back()->withErrors(['login' => 'Такой логин уже используется'])->withInput();
        }

        Staff::create([
            'full_name' => $data['full_name'],
            'login'     => $data['login'],
            'password'  => sha1(md5($data['password'])),
            'role'      => $data['role'],
            'phone'     => $data['phone'] ?? null,
            'email'     => $data['email'] ?? null,
            'note'      => $data['note'] ?? null,
            'active'    => $request->has('active') ? 1 : 0,
        ]);

        return redirect('/admin/staff')->with('success', 'Сотрудник добавлен');
    }

    public function edit($id)
    {
        if ($r = $this->checkAdmin()) return $r;
        $item = Staff::findOrFail($id);
        return view('admin.staff.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->checkAdmin()) return $r;
        $item = Staff::findOrFail($id);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'login'     => ['required', 'string', 'max:100', Rule::unique('staff', 'login')->ignore($item->id)],
            'password'  => 'nullable|string|min:4',
            'role'      => 'required|in:dispatcher,courier',
            'phone'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'note'      => 'nullable|string',
            'active'    => 'nullable',
        ]);

        if (Admin::where('login', $data['login'])->exists()) {
            return back()->withErrors(['login' => 'Такой логин уже используется'])->withInput();
        }

        $update = [
            'full_name' => $data['full_name'],
            'login'     => $data['login'],
            'role'      => $data['role'],
            'phone'     => $data['phone'] ?? null,
            'email'     => $data['email'] ?? null,
            'note'      => $data['note'] ?? null,
            'active'    => $request->has('active') ? 1 : 0,
        ];

        if (!empty($data['password'])) {
            $update['password'] = sha1(md5($data['password']));
        }

        $item->update($update);

        return redirect('/admin/staff')->with('success', 'Сотрудник обновлён');
    }

    public function toggle($id)
    {
        if ($r = $this->checkAdmin()) return $r;
        $item = Staff::findOrFail($id);
        $item->update(['active' => $item->active ? 0 : 1]);
        return redirect('/admin/staff')->with('success', 'Статус изменён');
    }

    public function destroy($id)
    {
        if ($r = $this->checkAdmin()) return $r;
        Staff::destroy($id);
        return redirect('/admin/staff')->with('success', 'Сотрудник удалён');
    }
}
```

- [ ] **Step 3.2: Commit**

```bash
git add app/Http/Controllers/StaffController.php
git commit -m "feat: StaffController — CRUD для курьеров/диспетчеров"
```

---

## Task 4: Роуты

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 4.1: Добавить use и роуты staff**

После строки `use App\Http\Controllers\AdminController;` (строка 57) добавить:

```php
use App\Http\Controllers\StaffController;
```

После блока admin-роутов (после строки 90, перед `// News`) вставить:

```php
// Admin / Staff (доступ только для role=admin)
Route::get('/admin/staff', [StaffController::class, 'index']);
Route::get('/admin/staff/create', [StaffController::class, 'create']);
Route::post('/admin/staff', [StaffController::class, 'store']);
Route::get('/admin/staff/{id}/edit', [StaffController::class, 'edit'])->whereNumber('id');
Route::post('/admin/staff/{id}', [StaffController::class, 'update'])->whereNumber('id');
Route::post('/admin/staff/{id}/toggle', [StaffController::class, 'toggle'])->whereNumber('id');
Route::post('/admin/staff/{id}/delete', [StaffController::class, 'destroy'])->whereNumber('id');
```

- [ ] **Step 4.2: Проверить роут-лист**

```bash
php artisan route:list --path=admin/staff
```

Expected: 7 роутов `/admin/staff*` с методами GET/POST.

- [ ] **Step 4.3: Commit**

```bash
git add routes/web.php
git commit -m "feat: роуты /admin/staff/*"
```

---

## Task 5: Views — index / create / edit

**Files:**
- Create: `resources/views/admin/staff/index.blade.php`
- Create: `resources/views/admin/staff/create.blade.php`
- Create: `resources/views/admin/staff/edit.blade.php`

- [ ] **Step 5.1: Создать index.blade.php**

Файл `resources/views/admin/staff/index.blade.php`:

```blade
@extends('layouts.admin')

@section('title', 'Сотрудники')

@section('content')
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
        <span>Сотрудники (курьеры и диспетчера)</span>
        <a href="/admin/staff/create" class="btn btn-primary btn-sm">+ Добавить сотрудника</a>
    </div>
    <div class="card-body">
        @if(count($staff) === 0)
            <p style="color:#888">Сотрудников пока нет.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Логин</th>
                    <th>Роль</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Статус</th>
                    <th style="width:260px">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $s)
                <tr>
                    <td>{{ $s->full_name }}</td>
                    <td>{{ $s->login }}</td>
                    <td>{{ $s->role === 'dispatcher' ? 'Диспетчер' : 'Курьер' }}</td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->email }}</td>
                    <td>
                        @if($s->active)
                            <span class="badge" style="background:#28a745">Активен</span>
                        @else
                            <span class="badge" style="background:#6c757d">Отключен</span>
                        @endif
                    </td>
                    <td>
                        <a href="/admin/staff/{{ $s->id }}/edit" class="btn btn-sm btn-primary">Редактировать</a>
                        <form action="/admin/staff/{{ $s->id }}/toggle" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">{{ $s->active ? 'Выкл' : 'Вкл' }}</button>
                        </form>
                        <form action="/admin/staff/{{ $s->id }}/delete" method="POST" style="display:inline" onsubmit="return confirm('Удалить сотрудника?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
```

- [ ] **Step 5.2: Создать create.blade.php**

Файл `resources/views/admin/staff/create.blade.php`:

```blade
@extends('layouts.admin')

@section('title', 'Новый сотрудник')

@section('content')
<div class="card">
    <div class="card-header">Новый сотрудник</div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert" style="background:#f8d7da;color:#721c24">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif
        <form action="/admin/staff" method="POST">
            @csrf
            <div class="form-group">
                <label>ФИО *</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
            </div>
            <div class="form-group">
                <label>Логин *</label>
                <input type="text" name="login" class="form-control" value="{{ old('login') }}" required>
            </div>
            <div class="form-group">
                <label>Пароль *</label>
                <input type="text" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Роль *</label>
                <select name="role" class="form-control" required>
                    <option value="dispatcher" {{ old('role') === 'dispatcher' ? 'selected' : '' }}>Диспетчер</option>
                    <option value="courier" {{ old('role') === 'courier' ? 'selected' : '' }}>Курьер</option>
                </select>
            </div>
            <div class="form-group">
                <label>Телефон</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label>Заметка</label>
                <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" checked> Активен</label>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="/admin/staff" class="btn" style="background:#6c757d;color:#fff">Отмена</a>
        </form>
    </div>
</div>
@endsection
```

- [ ] **Step 5.3: Создать edit.blade.php**

Файл `resources/views/admin/staff/edit.blade.php`:

```blade
@extends('layouts.admin')

@section('title', 'Редактирование сотрудника')

@section('content')
<div class="card">
    <div class="card-header">Редактирование сотрудника</div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert" style="background:#f8d7da;color:#721c24">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif
        <form action="/admin/staff/{{ $item->id }}" method="POST">
            @csrf
            <div class="form-group">
                <label>ФИО *</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $item->full_name) }}" required>
            </div>
            <div class="form-group">
                <label>Логин *</label>
                <input type="text" name="login" class="form-control" value="{{ old('login', $item->login) }}" required>
            </div>
            <div class="form-group">
                <label>Новый пароль <small style="color:#888">(оставьте пустым, чтобы не менять)</small></label>
                <input type="text" name="password" class="form-control" value="">
            </div>
            <div class="form-group">
                <label>Роль *</label>
                <select name="role" class="form-control" required>
                    <option value="dispatcher" {{ old('role', $item->role) === 'dispatcher' ? 'selected' : '' }}>Диспетчер</option>
                    <option value="courier" {{ old('role', $item->role) === 'courier' ? 'selected' : '' }}>Курьер</option>
                </select>
            </div>
            <div class="form-group">
                <label>Телефон</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $item->phone) }}">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $item->email) }}">
            </div>
            <div class="form-group">
                <label>Заметка</label>
                <textarea name="note" class="form-control" rows="3">{{ old('note', $item->note) }}</textarea>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" {{ $item->active ? 'checked' : '' }}> Активен</label>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="/admin/staff" class="btn" style="background:#6c757d;color:#fff">Отмена</a>
        </form>
    </div>
</div>
@endsection
```

- [ ] **Step 5.4: Commit**

```bash
git add resources/views/admin/staff/
git commit -m "feat: blade-шаблоны для раздела сотрудников"
```

---

## Task 6: Меню админки — условный рендер по ролям

**Files:**
- Modify: `resources/views/layouts/admin.blade.php`

- [ ] **Step 6.1: Обновить сайдбар — показывать пункты по ролям**

В файле `resources/views/layouts/admin.blade.php` заменить блок `<ul class="sidebar-menu">...</ul>` (строки 68-81) на:

```blade
        <ul class="sidebar-menu">
            @php $role = session('role'); @endphp
            @if($role === 'admin')
                <li><a href="/admin"><i class="fas fa-home"></i> Дашборд</a></li>
                <li><a href="/admin/users"><i class="fas fa-users"></i> Пользователи</a></li>
                <li><a href="/admin/staff"><i class="fas fa-user-tie"></i> Сотрудники</a></li>
            @endif
            @php $newInvoicesCount = \App\Models\Invoice::where('status', 0)->count(); @endphp
            <li><a href="/admin/invoices"><i class="fas fa-file-invoice"></i> Накладные <span id="invoice-badge" style="background:#D0171C;color:#fff;font-size:11px;padding:2px 7px;border-radius:10px;margin-left:5px;{{ $newInvoicesCount > 0 ? '' : 'display:none' }}">{{ $newInvoicesCount }}</span></a></li>
            @if($role === 'admin')
                <li><a href="/admin/orders"><i class="fas fa-truck"></i> Заказы/Трекинг</a></li>
                <li><a href="/admin/news"><i class="fas fa-newspaper"></i> Новости</a></li>
                <li><a href="/admin/pages"><i class="fas fa-file-alt"></i> Страницы</a></li>
                <li><a href="/admin/cities"><i class="fas fa-city"></i> Города</a></li>
                <li><a href="/admin/tariffs/avto"><i class="fas fa-car"></i> Тарифы Авто</a></li>
                <li><a href="/admin/tariffs/avia"><i class="fas fa-plane"></i> Тарифы Авиа</a></li>
                <li><a href="/admin/tariffs/zh"><i class="fas fa-train"></i> Тарифы Ж/Д</a></li>
            @endif
            <li><a href="/admin/logout" style="color:#ff6b6b"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
        </ul>
```

- [ ] **Step 6.2: Показать имя и роль в шапке**

В файле `resources/views/layouts/admin.blade.php` заменить блок `<div class="topbar-right">...</div>` (строки 86-88) на:

```blade
            <div class="topbar-right">
                @php
                    $role = session('role');
                    $who = $role === 'admin' ? 'Администратор' : (session('full_name') . ($role === 'dispatcher' ? ' (диспетчер)' : ' (курьер)'));
                @endphp
                <span style="color:#666;margin-right:15px">{{ $who }}</span>
                <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> На сайт</a>
            </div>
```

- [ ] **Step 6.3: Скрыть polling новых накладных для не-админа**

JS-опрос `/admin/invoices/check-new` в layout'е (строки 101-131) работает для всех ролей, доступ к эндпоинту уже разрешили. Ничего не меняем.

- [ ] **Step 6.4: Commit**

```bash
git add resources/views/layouts/admin.blade.php
git commit -m "feat: условное меню админки по ролям + имя/роль в шапке"
```

---

## Task 7: Ручная интеграционная проверка

**Files:** нет изменений, только верификация.

- [ ] **Step 7.1: Запустить сервер**

```bash
php artisan serve
```

Открыть `http://127.0.0.1:8000/admin`.

- [ ] **Step 7.2: Проверить вход админа**

Логин `admin` / пароль `7774333822` → редирект на `/admin/dashboard`. В шапке — «Администратор». В меню — все пункты, включая «Сотрудники».

- [ ] **Step 7.3: Проверить CRUD сотрудников**

1. `/admin/staff` — пусто
2. Создать диспетчера: ФИО `Иванов Иван`, логин `disp1`, пароль `test123`, роль «Диспетчер», активен → сохранить → появился в списке
3. Создать курьера: ФИО `Петров Пётр`, логин `cour1`, пароль `test456`, роль «Курьер», активен → появился в списке
4. Редактировать `disp1`, изменить ФИО, не менять пароль → сохранилось
5. Нажать «Выкл» у `cour1` → статус «Отключен»
6. Нажать «Вкл» у `cour1` → статус «Активен»

- [ ] **Step 7.4: Проверить вход диспетчера**

1. Выйти (`/admin/logout`)
2. Логин `disp1` / пароль `test123` → редирект на `/admin/invoices`
3. В шапке — «Иванов Иван (диспетчер)»
4. В меню — только «Накладные» и «Выход»
5. Попытаться открыть `/admin/staff` в адресной строке → редирект на `/admin`
6. Попытаться открыть `/admin/users` → редирект на `/admin`

- [ ] **Step 7.5: Проверить вход курьера**

1. Выйти
2. Логин `cour1` / пароль `test456` → `/admin/invoices`, в шапке «Петров Пётр (курьер)»
3. Меню — только «Накладные» и «Выход»
4. Открыть накладную, попытаться сменить статус → должно работать

- [ ] **Step 7.6: Проверить защиту деактивированного**

1. Выйти, войти как админ
2. Деактивировать `cour1` (кнопка «Выкл»)
3. Выйти, попытаться войти как `cour1` / `test456` → «Неверный логин или пароль»

- [ ] **Step 7.7: Проверить валидацию**

1. Войти как админ, открыть `/admin/staff/create`
2. Отправить пустую форму → ошибки валидации
3. Попробовать создать с логином `admin` (совпадение с админом) → ошибка «Такой логин уже используется»
4. Создать с логином `disp1` (дубликат) → ошибка валидации unique

- [ ] **Step 7.8: Проверить удаление**

1. Удалить `cour1` через кнопку «Удалить» (с подтверждением)
2. Попытаться войти как `cour1` → «Неверный логин или пароль»

- [ ] **Step 7.9: Итоговый коммит (если были фиксы)**

Если по ходу проверок пришлось что-то править — отдельные коммиты на каждую группу фиксов. Если всё сразу работает — пропустить шаг.

---

## Self-review checklist

- [ ] Спека покрыта: миграция/модель ✓, auth fallback ✓, checkAuth с ролями ✓, CRUD ✓, views ✓, меню ✓, валидация (в т.ч. коллизия с admins) ✓, deactivation ✓
- [ ] Нет placeholder'ов — все шаги с конкретным кодом и командами
- [ ] Типы консистентны — `role` везде `dispatcher`/`courier`, имена сессионных ключей везде одинаковые (`role`, `staff_id`, `full_name`, `admin`)
- [ ] Названия методов совпадают между tasks: `checkAuth(['admin'])`, `checkAdmin()`, `toggle`, `destroy`
