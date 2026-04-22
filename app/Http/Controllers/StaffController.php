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
