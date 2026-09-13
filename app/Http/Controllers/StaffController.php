<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Staff;
use App\Models\CityDelivery;
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
        $staff = Staff::with('cities')->orderBy('id', 'desc')->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        if ($r = $this->checkAdmin()) return $r;
        $cities = CityDelivery::orderBy('title')->get(['id', 'title']);
        return view('admin.staff.create', compact('cities'));
    }

    public function store(Request $request)
    {
        if ($r = $this->checkAdmin()) return $r;

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'login'     => 'required|string|max:100|unique:staff,login',
            'password'  => 'required|string|min:4',
            'roles'     => ['required', 'array', 'min:1'],
            'roles.*'   => [Rule::in(array_keys(Staff::ROLE_LABELS))],
            'phone'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'note'      => 'nullable|string',
            'warehouse_location' => 'nullable|string|max:255',
            'active'    => 'nullable',
        ]);

        if (Admin::where('login', $data['login'])->exists()) {
            return back()->withErrors(['login' => 'Такой логин уже используется'])->withInput();
        }

        $roles = self::normalizeRoles($data['roles']);

        $staff = Staff::create([
            'full_name' => $data['full_name'],
            'login'     => $data['login'],
            'password'  => sha1(md5($data['password'])),
            // role — первая роль набора: на неё опирается код и сборки
            // приложения, выпущенные до многоролевости.
            'role'      => $roles[0],
            'roles'     => $roles,
            'phone'     => $data['phone'] ?? null,
            'email'     => $data['email'] ?? null,
            'note'      => $data['note'] ?? null,
            'warehouse_location' => in_array('warehouse', $roles, true)
                ? ($data['warehouse_location'] ?? null)
                : null,
            'active'    => $request->has('active') ? 1 : 0,
        ]);
        $staff->cities()->sync($this->validCityIds($request));

        return redirect('/admin/staff')->with('success', 'Сотрудник добавлен');
    }

    /**
     * Оставляет из присланных city_ids только реально существующие города.
     *
     * @return list<int>
     */
    private function validCityIds(Request $request): array
    {
        $ids = array_map('intval', (array) $request->input('city_ids', []));
        if ($ids === []) {
            return [];
        }

        return CityDelivery::whereIn('id', $ids)->pluck('id')->all();
    }

    /**
     * Приводит набор ролей к порядку из ROLE_LABELS и убирает дубли, чтобы
     * основная роль не зависела от порядка чекбоксов в форме.
     *
     * @param  array<int, string>  $roles
     * @return list<string>
     */
    private static function normalizeRoles(array $roles): array
    {
        return array_values(array_filter(
            array_keys(Staff::ROLE_LABELS),
            fn ($role) => in_array($role, $roles, true),
        ));
    }

    public function edit($id)
    {
        if ($r = $this->checkAdmin()) return $r;
        $item = Staff::findOrFail($id);
        $cities = CityDelivery::orderBy('title')->get(['id', 'title']);
        return view('admin.staff.edit', compact('item', 'cities'));
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->checkAdmin()) return $r;
        $item = Staff::findOrFail($id);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'login'     => ['required', 'string', 'max:100', Rule::unique('staff', 'login')->ignore($item->id)],
            'password'  => 'nullable|string|min:4',
            'roles'     => ['required', 'array', 'min:1'],
            'roles.*'   => [Rule::in(array_keys(Staff::ROLE_LABELS))],
            'phone'     => 'nullable|string|max:50',
            'email'     => 'nullable|email|max:255',
            'note'      => 'nullable|string',
            'warehouse_location' => 'nullable|string|max:255',
            'active'    => 'nullable',
        ]);

        if (Admin::where('login', $data['login'])->exists()) {
            return back()->withErrors(['login' => 'Такой логин уже используется'])->withInput();
        }

        $roles = self::normalizeRoles($data['roles']);

        $update = [
            'full_name' => $data['full_name'],
            'login'     => $data['login'],
            'role'      => $roles[0],
            'roles'     => $roles,
            'phone'     => $data['phone'] ?? null,
            'email'     => $data['email'] ?? null,
            'note'      => $data['note'] ?? null,
            'warehouse_location' => in_array('warehouse', $roles, true)
                ? ($data['warehouse_location'] ?? null)
                : null,
            'active'    => $request->has('active') ? 1 : 0,
        ];

        if (!empty($data['password'])) {
            $update['password'] = sha1(md5($data['password']));
        }

        $item->update($update);
        $item->cities()->sync($this->validCityIds($request));

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
