<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $passwordHash = sha1(md5($request->input('password')));

        $staff = Staff::where('login', $request->input('login'))
            ->where('password', $passwordHash)
            ->first();

        if (!$staff) {
            return response()->json([
                'message' => 'Неверный логин или пароль',
            ], 401);
        }

        if (!$staff->active) {
            return response()->json([
                'message' => 'Учётная запись отключена. Обратитесь к администратору.',
            ], 403);
        }

        $token = $staff->createToken('mobile-staff')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->presentStaff($staff),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Вы вышли из аккаунта']);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'user' => $this->presentStaff($request->user()),
        ]);
    }

    private function presentStaff(Staff $staff): array
    {
        return [
            'id' => $staff->id,
            'login' => $staff->login,
            'full_name' => $staff->full_name,
            // role — основная роль, для сборок, выпущенных до многоролевости.
            'role' => $staff->primaryRole() ?? $staff->role,
            'roles' => $staff->roleNames(),
            'phone' => $staff->phone,
            'email' => $staff->email,
            'warehouse_location' => $staff->warehouse_location,
        ];
    }
}
