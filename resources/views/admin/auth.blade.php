<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #1a1a2e; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { width: 320px; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); text-align: center; }
        .login-container h1 { margin-bottom: 25px; color: #1a1a2e; font-size: 22px; }
        .login-container input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; font-size: 15px; box-sizing: border-box; }
        .login-container input:focus { border-color: #D0171C; outline: none; }
        .login-container button { width: 100%; padding: 12px; background-color: #D0171C; border: none; border-radius: 6px; color: white; font-size: 16px; cursor: pointer; margin-top: 10px; }
        .login-container button:hover { background-color: #a21216; }
        .error { color: #D0171C; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
<div class="login-container">
    <img src="{{ asset('assets/img/logo.svg') }}" alt="Logo" style="height:50px;margin-bottom:15px">
    <h1>Админ-панель</h1>
    <form action="{{ url('/admin/auth') }}" method="post">
        @csrf
        <input type="text" name="login" placeholder="Логин" required autocomplete="username">
        <input type="password" name="password" placeholder="Пароль" required autocomplete="current-password">
        <button type="submit">Войти</button>
    </form>
    @if(!empty($error))
    <div class="error">{{ $error }}</div>
    @endif
</div>
</body>
</html>
