<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница авторизации</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            width: 300px;
            padding: 20px;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-container h1 {
            margin-bottom: 20px;
            color: #ff0000;
            font-size:18px;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #ff0000;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #cc0000;
        }

        .login-container a {
            display: block;
            margin-top: 15px;
            color: #ff0000;
            text-decoration: none;
            font-size: 14px;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ff0000;
            font-size: 14px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-message img {
            margin-right: 8px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <img src="/assets/img/logo.svg" alt="Логотип"/>
    <h1>Восстановление пароля</h1>
    <form method="post" action="/cabinet/newpassword/<?echo $restore_code?>">
                            <input class="form-control" type="password" name="password" placeholder="Введите новый пароль" required>
                            <input class="form-control" type="password" name="re-password" placeholder="Повторите пароль" required>
                            <div class="form-button">
                                <button id="submit" type="submit" class="ibtn" name="restore" value="Восстановить">Восстановить</button> 
                            </div>
        
                        </form>
    <?if (!empty($error)){?>
    <div class="error-message">
        <img src="https://img.icons8.com/material-outlined/24/ff0000/error--v1.png" alt="Ошибка">
        <?echo $error?>
    </div>
    <?;}?>
</div>

<!-- Подключаем Cleave.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Cleave('input[name="iin"]', {
                blocks: [12], // Ограничение на 12 цифр
                numericOnly: true // Только цифры
            });
        });
    </script>
</body>
</html>
