<?php

require_once 'register_user.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $role = 'regular';
    $lastName = $_POST['last_name'];
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'] ?? '';
    $phone = $_POST['phone'];
    
    $result = registerUser($login, $password, $role, $lastName, $firstName, $middleName, $phone);
    
    if ($result) {
        $_SESSION['registration_success'] = "Регистрация успешно завершена!";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['registration_error'] = "Ошибка при регистрации.";
        header("Location: register.php");
        exit;
    }
}


?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="form-wrapper">
    <div id="registerForm">
        <h2>Регистрация</h2>
        <?php
        if (isset($_SESSION['registration_error'])) {
            echo '<p style="color: red;">' . $_SESSION['registration_error'] . '</p>';
            unset($_SESSION['registration_error']);
        }
        ?>
        <form action="register.php" method="post">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="text" name="last_name" placeholder="Фамилия" required>
            <input type="text" name="first_name" placeholder="Имя" required>
            <input type="text" name="middle_name" placeholder="Отчество">
            <input type="text" name="phone" placeholder="Телефон" required>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <a href="login.php">Уже зарегистрированы? Войти</a>
    </div>
</div>
</body>
</html>
