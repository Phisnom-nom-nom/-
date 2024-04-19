<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Если пользователь авторизован, но его роль не "admin", перенаправляем на index.php
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}
?>

?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="sidebar">
    <h2>Список таблиц</h2>
    <button class="navButton" onclick="loadContent('/views/view_users_table.html')">Пользователи</button>
    <button class="navButton" onclick="loadContent('/views/view_route_table.html')">Маршруты</button>
    <button class="navButton" onclick="loadContent('/views/view_route_users_table.html')">Маршруты пользователей</button>
    <button class="navButton" id="logoutButton">Выйти</button>
</div>

<div class="content" id="content">
    <div class="sticky-header">
        <h1>Добро пожаловать!</h1>
        <p>Выберите категорию из меню слева для просмотра информации.</p>
    </div>
</div>

<script>
let currentContentUrl = '';

function loadContent(url) {
    fetch(url + '?_=' + new Date().getTime())
        .then(response => response.text())
        .then(html => {
            const content = document.getElementById('content');
            content.innerHTML = html;
            executeScripts(content);
            bindCloseButton();
            highlightActiveButton(url); 
        })
        .catch(error => console.error('Ошибка при загрузке страницы:', error));
}

function executeScripts(content) {
    const scripts = Array.from(content.querySelectorAll('script'));
    scripts.forEach(script => {
        eval(script.innerText);
    });
}

function bindCloseButton() {
    const closeButton = document.querySelector('.close-button');
    if (closeButton) {
        closeButton.onclick = function() {
            closeModal();
        };
    }
}

function highlightActiveButton(activeUrl) {
    document.querySelectorAll('.navButton').forEach(button => {
        if (button.getAttribute('onclick').includes(activeUrl)) {
            button.classList.add('activeButton');
        } else {
            button.classList.remove('activeButton');
        }
    });
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');

    sidebar.addEventListener('mouseenter', function() {
        this.style.left = '0';
        content.style.marginLeft = '220px';
    });

    sidebar.addEventListener('mouseleave', function() {
        this.style.left = '-180px';
        content.style.marginLeft = '20px';
    });
});

document.getElementById('logoutButton').addEventListener('click', function() {
    window.location.href = 'logout.php'; 
});

</script>

</body>
</html>
