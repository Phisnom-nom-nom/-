<?php
require_once '../database_config.php';
require_once '../models/route.php';

class RouteDAO {
    private $conn;

    public function __construct() {
        $this->conn = DatabaseConfig::connect();
        if (!$this->conn) {
            die("Ошибка подключения к базе данных.");
        }
    }

    // Добавление новой записи в таблицу routes
    public function addRoute(Route $route) {
        $query = "INSERT INTO routes (name, description, price) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $route->getName(),
            $route->getDescription(),
            $route->getPrice()
        ]);

        return $this->conn->lastInsertId();
    }

    // Получение записи по ID
    public function getRouteById($id) {
        $query = "SELECT * FROM routes WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Route(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['price']
            );
        }

        return null;
    }

    // Обновление записи
    public function updateRoute(Route $route) {
        $query = "UPDATE routes SET name = ?, description = ?, price = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $route->getName(),
            $route->getDescription(),
            $route->getPrice(),
            $route->getId()
        ]);
    }

    // Удаление записи по ID
    public function deleteRoute($id) {
        $query = "DELETE FROM routes WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Получение всех записей
    public function getAllRoutes() {
        $query = "SELECT * FROM routes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $routeList = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $routeList[] = new Route(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['price']
            );
        }

        return $routeList;
    }

    public function getAllRoutesJson() {  
        $routeList = $this->getAllRoutes();
        $routeArray = array_map(function($route) {
            return [
                'id' => $route->getId(),
                'name' => $route->getName(),
                'description' => $route->getDescription(),
                'price' => $route->getPrice()
            ];
        }, $routeList);
    
        return json_encode($routeArray); 
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAllRoutesJson') {
    $dao = new RouteDAO();
    echo $dao->getAllRoutesJson();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteMultipleRoutes' && !empty($_POST['ids'])) {
    $dao = new RouteDAO(); 
    $ids = explode(',', $_POST['ids']);
    $errors = [];
    $successCount = 0;

    foreach ($ids as $id) {
        $success = $dao->deleteRoute(trim($id));

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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getRouteById' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $dao = new RouteDAO();
    $route = $dao->getRouteById($id);

    if ($route) {
        echo json_encode([
            'id' => $route->getId(),
            'name' => $route->getName(),
            'description' => $route->getDescription(),
            'price' => $route->getPrice()
        ]);
    } else {
        echo json_encode(['error' => 'Запись не найдена']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $dao = new RouteDAO();
    switch ($_POST['action']) {
        case 'addRoute':
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];

            $route = new Route(null, $name, $description, $price);
            $newId = $dao->addRoute($route);

            echo json_encode(['success' => true, 'message' => 'Запись добавлена', 'newId' => $newId]);
            break;
        case 'updateRoute':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];

            $route = new Route($id, $name, $description, $price);
            $success = $dao->updateRoute($route);

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
