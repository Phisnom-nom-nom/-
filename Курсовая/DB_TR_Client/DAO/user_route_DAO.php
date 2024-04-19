<?php
require_once '../database_config.php';
require_once '../models/user_route.php';

class UserRouteDAO {
    private $conn;

    public function __construct() {
        $this->conn = DatabaseConfig::connect();
        if (!$this->conn) {
            die("Ошибка подключения к базе данных.");
        }
    }

    // Добавление новой записи в таблицу user_routes
    public function addUserRoute(UserRoute $userRoute) {
        $query = "INSERT INTO user_routes (user_id, route_id, purchase_date, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $userRoute->getUserId(),
            $userRoute->getRouteId(),
            $userRoute->getPurchaseDate(),
            $userRoute->getStartDate(),
            $userRoute->getEndDate()
        ]);

        return $this->conn->lastInsertId();
    }

    // Получение записи по ID
    public function getUserRouteById($id) {
        $query = "SELECT * FROM user_routes WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new UserRoute(
                $row['id'],
                $row['user_id'],
                $row['route_id'],
                $row['purchase_date'],
                $row['start_date'],
                $row['end_date']
            );
        }

        return null;
    }

    // Обновление записи
    public function updateUserRoute(UserRoute $userRoute) {
        $query = "UPDATE user_routes SET user_id = ?, route_id = ?, purchase_date = ?, start_date = ?, end_date = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $userRoute->getUserId(),
            $userRoute->getRouteId(),
            $userRoute->getPurchaseDate(),
            $userRoute->getStartDate(),
            $userRoute->getEndDate(),
            $userRoute->getId()
        ]);
    }

    // Удаление записи по ID
    public function deleteUserRoute($id) {
        $query = "DELETE FROM user_routes WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Получение всех записей
    public function getAllUserRoutes() {
        $query = "SELECT * FROM user_routes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $userRouteList = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userRouteList[] = new UserRoute(
                $row['id'],
                $row['user_id'],
                $row['route_id'],
                $row['purchase_date'],
                $row['start_date'],
                $row['end_date']
            );
        }

        return $userRouteList;
    }

    public function getAllUserRoutesJson() {  
        $userRouteList = $this->getAllUserRoutes();
        $userRouteArray = array_map(function($userRoute) {
            return [
                'id' => $userRoute->getId(),
                'user_id' => $userRoute->getUserId(),
                'route_id' => $userRoute->getRouteId(),
                'purchase_date' => $userRoute->getPurchaseDate(),
                'start_date' => $userRoute->getStartDate(),
                'end_date' => $userRoute->getEndDate()
            ];
        }, $userRouteList);
    
        return json_encode($userRouteArray); 
    }

    // Метод для получения маршрутов пользователя по ID пользователя
    public function getUserRoutesByUserId($userId) {
        $query = "SELECT ur.id as user_route_id, ur.purchase_date, ur.start_date, ur.end_date, r.* 
                FROM user_routes ur
                JOIN routes r ON ur.route_id = r.id
                WHERE ur.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);

        $userRoutes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userRoutes[] = [
                'user_route_id' => $row['user_route_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'price' => $row['price'],
                'purchase_date' => $row['purchase_date'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date']
            ];
        }

        return $userRoutes;
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getAllUserRoutesJson') {
    $dao = new UserRouteDAO();
    echo $dao->getAllUserRoutesJson();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteMultipleUserRoutes' && !empty($_POST['ids'])) {
    $dao = new UserRouteDAO(); 
    $ids = explode(',', $_POST['ids']);
    $errors = [];
    $successCount = 0;

    foreach ($ids as $id) {
        $success = $dao->deleteUserRoute(trim($id));

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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getUserRouteById' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $dao = new UserRouteDAO();
    $userRoute = $dao->getUserRouteById($id);

    if ($userRoute) {
        echo json_encode([
            'id' => $userRoute->getId(),
            'user_id' => $userRoute->getUserId(),
            'route_id' => $userRoute->getRouteId(),
            'purchase_date' => $userRoute->getPurchaseDate(),
            'start_date' => $userRoute->getStartDate(),
            'end_date' => $userRoute->getEndDate()
        ]);
    } else {
        echo json_encode(['error' => 'Запись не найдена']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $dao = new UserRouteDAO();
    switch ($_POST['action']) {
        case 'addUserRoute':
            $user_id = $_POST['user_id'];
            $route_id = $_POST['route_id'];
            $purchase_date = $_POST['purchase_date'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            $userRoute = new UserRoute(null, $user_id, $route_id, $purchase_date, $start_date, $end_date);
            $newId = $dao->addUserRoute($userRoute);

            echo json_encode(['success' => true, 'message' => 'Запись добавлена', 'newId' => $newId]);
            break;
        case 'updateUserRoute':
            $id = $_POST['id'];
            $user_id = $_POST['user_id'];
            $route_id = $_POST['route_id'];
            $purchase_date = $_POST['purchase_date'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            $userRoute = new UserRoute($id, $user_id, $route_id, $purchase_date, $start_date, $end_date);
            $success = $dao->updateUserRoute($userRoute);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Запись обновлена']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении записи']);
            }
            break;
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getUserRoutesByUserId') {
    $userId = $_SESSION['id']; 
    $dao = new UserRouteDAO();
    $userRoutes = $dao->getUserRoutesByUserId($userId);

    echo json_encode($userRoutes);
    exit();
}

?>
           
