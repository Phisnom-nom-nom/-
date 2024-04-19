<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) {
    // Если пользователь не авторизован, устанавливаем его как гостя
    $_SESSION['username'] = 'guest';
    $_SESSION['password'] = '';
    $_SESSION['role'] = 'guest';
} else {
    // Если пользователь авторизован, но роль не установлена, устанавливаем по умолчанию
    if (!isset($_SESSION['role'])) {
        $_SESSION['role'] = 'guest';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Туристические маршруты</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="sidebar">
    <h2>Меню</h2>
    <button class="navButton" onclick="loadContent('/views/view_user_data.html')" <?php if($_SESSION['role'] === 'guest') echo 'disabled'; ?>>Мои данные</button>
    <button class="navButton" onclick="loadContent('/views/view_user_route.html')" <?php if($_SESSION['role'] === 'guest') echo 'disabled'; ?>>Мои маршруты</button>
    <button class="navButton" onclick="loadContent('/views/view_route.html')">Список маршрутов</button>
    <button class="navButton" id="logoutButton" onclick="window.location.href='<?php echo ($_SESSION['role'] === 'guest') ? 'login.php' : 'logout.php'; ?>'"><?php echo ($_SESSION['role'] === 'guest') ? 'Вход' : 'Выйти'; ?></button>
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
            if (url === '/views/view_user_data.html'){
                fillFormData();
                setEditForm();
            }
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

function setEditForm() {
    document.getElementById('editUserDataForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'updateUser'); 

        fetch('../DAO/user_DAO.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Данные пользователя успешно обновлены.');
            } else {
                alert('Произошла ошибка при обновлении данных пользователя.');
            }
        })
        .catch(error => console.error('Ошибка:', error));
    });
}

function fillFormData() {
    fetch(`../DAO/user_DAO.php?action=getUserByIdSession&id`) 
    .then(response => response.json())
    .then(data => {
        if (!data.error) {
            document.getElementById('login').value = data.login;
            document.getElementById('password').value = data.password;
            document.getElementById('last_name').value = data.last_name;
            document.getElementById('first_name').value = data.first_name;
            document.getElementById('middle_name').value = data.middle_name;
            document.getElementById('phone').value = data.phone;
        } else {
            console.error('Пользователь не найден:', data.error);
        }
    })
    .catch(error => console.error('Ошибка при получении данных пользователя:', error));
}

function highlightActiveButton(activeUrl) {
    document.querySelectorAll('.navButton').forEach(button => {
        const onclickAttribute = button.getAttribute('onclick');
        if (onclickAttribute && onclickAttribute.includes(activeUrl)) {
            button.classList.add('activeButton');
        } else {
            button.classList.remove('activeButton');
        }
    });
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
