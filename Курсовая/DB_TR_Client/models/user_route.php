<?php
class UserRoute {
    // Свойства, соответствующие полям в таблице "user_routes" базы данных 
    public $id;
    public $user_id;
    public $route_id;
    public $purchase_date;
    public $start_date;
    public $end_date;

    // Конструктор для создания объекта модели
    public function __construct($id, $user_id, $route_id, $purchase_date, $start_date, $end_date) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->route_id = $route_id;
        $this->purchase_date = $purchase_date;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    // Геттеры и сеттеры для каждого свойства
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function getRouteId() {
        return $this->route_id;
    }

    public function setRouteId($route_id) {
        $this->route_id = $route_id;
    }

    public function getPurchaseDate() {
        return $this->purchase_date;
    }

    public function setPurchaseDate($purchase_date) {
        $this->purchase_date = $purchase_date;
    }

    public function getStartDate() {
        return $this->start_date;
    }

    public function setStartDate($start_date) {
        $this->start_date = $start_date;
    }

    public function getEndDate() {
        return $this->end_date;
    }

    public function setEndDate($end_date) {
        $this->end_date = $end_date;
    }
}
