<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="form-wrapper">
    <div id="loginForm">
        <h2>Авторизация</h2>
        <form action="../database_config.php" method="post">
            <input type="text" id="loginUsername" name="username" placeholder="Имя пользователя" required>
            <input type="password" id="loginPassword" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
            <a href="register.php" class="register-btn">Регистрация</a>
        </form>
        <?php
        if (isset($_SESSION['login_error'])) {
            echo '<script>alert("' . $_SESSION['login_error'] . '");</script>';
            unset($_SESSION['login_error']);
        }
        ?>
    </div>
</div>
</body>
</html>
