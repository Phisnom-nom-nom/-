<?php

session_start();

class DatabaseConfig {
    private static $url = 'mysql:host=localhost;dbname=tr';

    public static function connect($login = '', $password = '') {
        $username = empty($login) ? ($_SESSION['username'] ?? '') : $login;
        $password = empty($password) ? ($_SESSION['password'] ?? '') : $password;

        try {
            $conn = new PDO(self::$url, $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            $_SESSION['login_error'] = 'Ошибка подключения к базе данных. Пожалуйста, проверьте ваши учетные данные.';
            return null;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];

    $conn = DatabaseConfig::connect();
    if ($conn) {
        $stmt = $conn->prepare("SELECT id, role FROM users WHERE login = ? AND password = ?");
        $stmt->execute([$_POST['username'], $_POST['password']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['role'] = $user['role'];
            $_SESSION['id'] = $user['id'];

            // В зависимости от роли пользователя перенаправляем на разные страницы
            if ($user['role'] === 'admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $_SESSION['login_error'] = 'Неверный логин или пароль.';
            header("Location: login.php");
            exit;
        }
    } else {
        header("Location: login.php");
        exit;
    }
}

?>
