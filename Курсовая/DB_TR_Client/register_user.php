<?php

require_once 'database_config.php';

function registerUser($login, $password, $role, $lastName, $firstName, $middleName, $phone) {
    try {
        // Подключение к базе данных с правами администратора
        $conn = DatabaseConfig::connect('admin', 'admin');
        // Вставка записи о пользователе в таблицу users
        $stmt = $conn->prepare("INSERT INTO users (login, password, role, last_name, first_name, middle_name, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$login, $password, $role, $lastName, $firstName, $middleName, $phone]);
        // Создание пользователя MySQL и назначение прав
        $conn->exec("CREATE USER '$login'@'localhost' IDENTIFIED BY '$password'");
        $conn->exec("GRANT SELECT, UPDATE ON tr.* TO '$login'@'localhost'");
        $conn->exec("FLUSH PRIVILEGES");
        
        return true;
    } catch (PDOException $e) {
        echo "Ошибка при регистрации: " . $e->getMessage();
        return false;
    }
}
