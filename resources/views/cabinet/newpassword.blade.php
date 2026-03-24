<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый пароль - Logexim Express</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { width: 300px; padding: 20px; border: 1px solid #eaeaea; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .login-container h1 { margin-bottom: 20px; color: #ff0000; }
        .login-container input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; box-sizing: border-box; }
        .login-container button { width: 100%; padding: 10px; background-color: #ff0000; border: none; border-radius: 4px; color: white; font-size: 16px; cursor: pointer; }
        .login-container button:hover { background-color: #cc0000; }
        .error-message { color: #ff0000; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
<div class="login-container">
    <img src="/images/new-logo.png" alt="Логотип"/>
    <h1>Новый пароль</h1>
    <form action="{{ url('/cabinet/newpassword/' . $restore_code) }}" method="post">
        @csrf
        <input type="password" name="password" placeholder="Новый пароль" required>
        <input type="password" name="re-password" placeholder="Повторите пароль" required>
        <button type="submit">Сохранить</button>
    </form>
    @if(!empty($error))
    <div class="error-message">{{ $error }}</div>
    @endif
</div>
</body>
</html>
