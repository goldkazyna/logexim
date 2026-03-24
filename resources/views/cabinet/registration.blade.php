<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Logexim Express</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { width: 300px; padding: 20px; border: 1px solid #eaeaea; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); text-align: center; }
        .login-container h1 { margin-bottom: 20px; color: #ff0000; }
        .login-container input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; box-sizing: border-box; }
        .login-container button { width: 100%; padding: 10px; background-color: #ff0000; border: none; border-radius: 4px; color: white; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease; }
        .login-container button:hover { background-color: #cc0000; }
        .login-container a { display: block; margin-top: 15px; color: #ff0000; text-decoration: none; font-size: 14px; }
        .login-container a:hover { text-decoration: underline; }
        .error-message { color: #ff0000; font-size: 14px; margin-top: 10px; display: flex; align-items: center; justify-content: center; }
        .error-message img { margin-right: 8px; }
        .login-container p { background-color: #f0f8ff; color: #333; padding: 15px; border-left: 4px solid #007bff; font-size: 14px; line-height: 1.5; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="login-container">
    <img src="/images/new-logo.png" alt="Логотип"/>
    <h1>Регистрация</h1>
    <p>Чтобы воспользоваться личным кабинетом, вам необходимо отправить заявку на регистрацию. После успешного прохождения <b>модерации</b>, доступ к личному кабинету будет открыт.</p>
    <form action="{{ url('/cabinet/registration') }}" method="post">
        @csrf
        <input type="text" placeholder="БИН" name="bin" required>
        <input type="text" placeholder="Наименование компании" name="company_name" required>
        <input type="tel" placeholder="Телефон" name="phone" required>
        <input type="email" placeholder="Email" name="email" required>
        <input type="text" placeholder="Имя руководителя" name="director_name" required>
        <input type="text" placeholder="Адрес" name="address" required>
        <input type="password" placeholder="Пароль" name="password" required>
        <input type="password" placeholder="Повторите пароль" name="re-password" required>
        <button type="submit">Отправить заявку</button>
    </form>
    @if($errors->has('password'))
    <div class="error-message">
        <img src="https://img.icons8.com/material-outlined/24/ff0000/error--v1.png" alt="Ошибка">
        {{ $errors->first('password') }}
    </div>
    @endif
    @if($errors->has('bin'))
    <div class="error-message">
        <img src="https://img.icons8.com/material-outlined/24/ff0000/error--v1.png" alt="Ошибка">
        {{ $errors->first('bin') }}
    </div>
    @endif
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/addons/cleave-phone.kz.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Cleave('input[name="phone"]', { phone: true, phoneRegionCode: 'KZ', prefix: '+7', noImmediatePrefix: true, delimiter: ' ', blocks: [1, 3, 3, 2, 2], numericOnly: true });
        new Cleave('input[name="bin"]', { blocks: [12], numericOnly: true });
    });
</script>
</body>
</html>
