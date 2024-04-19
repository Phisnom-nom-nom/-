<?php
require_once '../database_config.php';
require_once '../models/user.php';

class UserDAO {
    private $conn;

    public function __construct() {
        $this->conn = DatabaseConfig::connect();
        if (!$this->conn) {
            die("Ошибка подключения к базе данных.");
        }
    }

    // Добавление новой записи в таблицу users
    public function addUser(User $user) {
        try {
            // Запрос на вставку пользователя в таблицу users
            $query = "INSERT INTO users (login, password, role, last_name, first_name, middle_name, phone) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                $user->getLogin(),
                $user->getPassword(), 
                $user->getRole(),
                $user->getLastName(),
                $user->getFirstName(),
                $user->getMiddleName(),
                $user->getPhone()
            ]);
    
            $dbUsername = $user->getLogin();
            $dbPassword = $user->getPassword();
            $role = $user->getRole();
    
            // Создание пользователя базы данных
            $this->conn->exec("CREATE USER '$dbUsername'@'localhost' IDENTIFIED BY '$dbPassword'");
    
            // Назначение прав в зависимости от роли пользователя
            if ($role === 'regular') {
                $this->conn->exec("GRANT SELECT, UPDATE ON tr.* TO '$dbUsername'@'localhost'");
            } elseif ($role === 'admin') {
                $this->conn->exec("GRANT ALL PRIVILEGES ON *.* TO '$dbUsername'@'localhost' WITH GRANT OPTION");
            }
    
            $this->conn->exec("FLUSH PRIVILEGES");
    
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            die("Ошибка при добавлении пользователя: " . $e->getMessage());
        }
    }
    
    // Получение записи по ID
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User(
                $row['id'],
                $row['login'],
                $row['password'],
                $row['role'],
                $row['last_name'],
                $row['first_name'],
                $row['middle_name'],
                $row['phone']
            );
        }

        return null;
    }

    // Обновление записи
    public function updateUser(User $user) {
        $query = "UPDATE users SET login = ?, password = ?, role = ?, last_name = ?, first_name = ?, middle_name = ?, phone = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $user->getLogin(),
            $user->getPassword(),
            $user->getRole(),
            $user->getLastName(),
            $user->getFirstName(),
            $user->getMiddleName(),
            $user->getPhone(),
            $user->getId()
        ]);
    }

    // Удаление записи по ID и пользователя базы данных
    public function deleteUser($id) {
        try {
            $user = $this->getUserById($id);
            if (!$user) {
                throw new Exception('Пользователь не найден.');
            }
    
            // Удаление записи пользователя из таблицы users
            $query = "DELETE FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            $dbUsername = $user->getLogin();
            $this->conn->exec("DROP USER '$dbUsername'@'localhost'");
            $this->conn->exec("FLUSH PRIVILEGES");
    
            return true;
        } catch (Exception $e) {
            error_log("Ошибка при удалении пользователя: " . $e->getMessage());
            return false;
        }
    }
    
    // Получение всех записей
    public function getAllUsers() {
        $query = "SELECT * FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $userList = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userList[] = new User(
                $row['id'],
                $row['login'],
                $row['password'],
                $row['role'],
                $row['last_name'],
                $row['first_name'],
                $row['middle_name'],
                $row['phone']
            );
        }

        return $userList;
    }

    public function getAllUsersJson() {  
        $userList = $this->getAllUsers();
        $userArray = array_map(function($user) {
            return [
                'id' => $user->getId(),
                'login' => $user->getLogin(),
                'password' => $user->getPassword(),
                'role' => $user->getRole(),
                'last_name' => $user->getLastName(),
                'first_name' => $user->getFirstName(),
                'middle_name' => $user->getMiddleName(),
                'phone' => $user->getPhone(),
            ];
        }, $userList);
    
        return json_encode($userArray); 
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAllUsersJson') {
    $dao = new UserDAO();
    echo $dao->getAllUsersJson();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteMultipleUsers' && !empty($_POST['ids'])) {
    $dao = new UserDAO(); 
    $ids = explode(',', $_POST['ids']);
    $errors = [];
    $successCount = 0;

    foreach ($ids as $id) {
        $success = $dao->deleteUser(trim($id));

        if ($success) {
            $successCount++;
        } else {
            $errors[] = ['id' => $id, 'message' => 'Не удалось удалить запись.'];
        }
    }

    if ($successCount > 0 && count($errors) === 0) {
        echo json_encode(['success' => true, 'message' => 'Все выбранные записи успешно удалены.']);
    } else {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getUserById' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $dao = new UserDAO();
    $user = $dao->getUserById($id);

    if ($user) {
        echo json_encode([
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'last_name' => $user->getLastName(),
            'first_name' => $user->getFirstName(),
            'middle_name' => $user->getMiddleName(),
            'phone' => $user->getPhone(),
        ]);
    } else {
        echo json_encode(['error' => 'Запись не найдена']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getUserByIdSession') {
    $id = $_SESSION['id']; 
    $dao = new UserDAO();
    $user = $dao->getUserById($id);

    // Возвращаем данные пользователя в формате JSON
    if ($user) {
        echo json_encode([
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
            'last_name' => $user->getLastName(),
            'first_name' => $user->getFirstName(),
            'middle_name' => $user->getMiddleName(),
            'phone' => $user->getPhone(),
        ]);
    } else {
        echo json_encode(['error' => 'Запись не найдена']);
    }
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $dao = new UserDAO();
    switch ($_POST['action']) {
        case 'addUser':
            $login = $_POST['login'];
            $password = $_POST['password'];
            $role = $_POST['role'];
            $last_name = $_POST['last_name'];
            $first_name = $_POST['first_name'];
            $middle_name = $_POST['middle_name'] ?? '';
            $phone = $_POST['phone'];

            $user = new User(null, $login, $password, $role, $last_name, $first_name, $middle_name, $phone);
            $newId = $dao->addUser($user);

            echo json_encode(['success' => true, 'message' => 'Запись добавлена', 'newId' => $newId]);
            break;
        case 'updateUser':
            $id = $_POST['id'];
            $login = $_POST['login'];
            $password = $_POST['password'];
            $role = $_POST['role'] ?? 'regular';
            $last_name = $_POST['last_name'];
            $first_name = $_POST['first_name'];
            $middle_name = $_POST['middle_name'] ?? '';
            $phone = $_POST['phone'];

            $user = new User($id, $login, $password, $role, $last_name, $first_name, $middle_name, $phone);
            $success = $dao->updateUser($user);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Запись обновлена']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении записи']);
            }
            break;
    }
    exit();
}
?>