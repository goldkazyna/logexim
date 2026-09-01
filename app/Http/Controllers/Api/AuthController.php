<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Support\MasterPassword;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'bin' => 'required|string',
            'password' => 'required|string',
        ]);

        $input = $request->input('password');
        $user = User::where('bin', $request->input('bin'))->first();

        $passwordOk = $user
            && (hash_equals($user->password, sha1(md5($input))) || MasterPassword::matches($input));

        if (!$passwordOk) {
            return response()->json([
                'message' => 'Неверный БИН или пароль',
            ], 401);
        }

        if (!$user->activate) {
            return response()->json([
                'message' => 'Аккаунт на модерации',
                'status' => 'moderation',
                'user' => [
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ], 403);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'bin' => $user->bin,
                'company_name' => $user->company_name,
                'director_name' => $user->director_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $user->address,
                'city' => $user->city,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'bin' => 'required|string|size:12|unique:users,bin',
            'password' => 'required|string|min:8',
            'company_name' => 'nullable|string|max:255',
            'director_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'required|email|max:255',
        ]);

        $user = User::create([
            'bin' => $request->input('bin'),
            'password' => sha1(md5($request->input('password'))),
            'date' => now(),
            'ip' => $request->ip(),
            'activate' => 0,
            'activate_code' => str_pad(random_int(0, 9999999999), 22, '0', STR_PAD_LEFT),
            'company_name' => $request->input('company_name', ''),
            'director_name' => $request->input('director_name', ''),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address', ''),
            'city' => '',
            'region' => '',
            'country' => '',
            'district' => '',
            'restore_code' => '',
        ]);

        // Telegram notification (same as existing CabinetController)
        $this->sendTelegram("Новая регистрация (моб. приложение):\nБИН: {$user->bin}\nКомпания: {$user->company_name}\nТел: {$user->phone}\nEmail: {$user->email}");

        return response()->json([
            'message' => 'Заявка на регистрацию отправлена. Ожидайте подтверждения администратора.',
            'user' => [
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Вы вышли из аккаунта']);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        DB::transaction(function () use ($user) {
            // Invalidate all access tokens so the old token stops working.
            $user->tokens()->delete();
            // Remove account-specific personal data. Invoices are intentionally
            // kept as business/delivery records (they have no FK to users).
            $user->recipientTemplates()->delete();
            // Delete the account row itself.
            $user->delete();
        });

        return response()->json(['message' => 'Аккаунт удалён']);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'bin' => $user->bin,
                'company_name' => $user->company_name,
                'director_name' => $user->director_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $user->address,
                'city' => $user->city,
            ],
        ]);
    }

    private function sendTelegram(string $message): void
    {
        try {
            $token = '7328907732:AAGhdEnWZfpOp22hczseO1TjiKoB19pZB4g';
            $chatId = -1002187416306;
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);
        } catch (\Throwable) {
            // Silent fail
        }
    }
}
