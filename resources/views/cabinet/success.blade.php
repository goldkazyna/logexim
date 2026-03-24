<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация успешна - Logexim Express</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { width: 400px; padding: 30px; border: 1px solid #eaeaea; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); text-align: center; }
        .container h1 { color: #28a745; margin-bottom: 15px; }
        .container p { font-size: 16px; line-height: 1.5; color: #333; }
        .container a { display: inline-block; margin-top: 20px; padding: 10px 30px; background-color: #ff0000; color: #fff; text-decoration: none; border-radius: 4px; }
        .container a:hover { background-color: #cc0000; }
    </style>
</head>
<body>
<div class="container">
    <h1>Заявка отправлена!</h1>
    <p>Ваша заявка на регистрацию успешно отправлена. После прохождения модерации вам будет открыт доступ к личному кабинету.</p>
    <a href="{{ url('/cabinet/auth') }}">Войти</a>
</div>
</body>
</html>
