<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Успешная регистрация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .container img {
            width: 100px;
            margin-bottom: 20px;
        }

        .container h1 {
            color: #D0171C;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .container p {
            font-size: 18px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .container .note {
            font-size: 14px;
            color: #777;
            margin-bottom: 30px;
        }

        .container .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #D0171C;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .container .btn:hover {
            background-color: #b11418;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://img.icons8.com/ios-filled/100/D0171C/checkmark.png" alt="Успешно">
        <h1>Письмо для восстановления пароля отправлено на ваш email.</h1>
        <p>Чтобы восстановить пароль, перейдите по ссылке в письме. Если письмо не оказалось во входящих, пожалуйста, проверьте папку "Спам".</p>
        
        <a href="/" class="btn">Вернуться на главную</a>
    </div>
</body>
</html>
