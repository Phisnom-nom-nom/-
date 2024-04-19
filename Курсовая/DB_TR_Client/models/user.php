<?php

class User {
    // Свойства, соответствующие полям в таблице "users" базы данных 
    public $id;
    public $login;
    public $password;
    public $role;
    public $last_name;
    public $first_name;
    public $middle_name;
    public $phone;

    // Конструктор для создания объекта модели
    public function __construct($id, $login, $password, $role, $last_name, $first_name, $middle_name, $phone) {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->role = $role;
        $this->last_name = $last_name;
        $this->first_name = $first_name;
        $this->middle_name = $middle_name;
        $this->phone = $phone;
    }

    // Геттеры и сеттеры для каждого свойства
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLogin() {
        return $this->login;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($last_name) {
        $this->last_name = $last_name;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($first_name) {
        $this->first_name = $first_name;
    }

    public function getMiddleName() {
        return $this->middle_name;
    }

    public function setMiddleName($middle_name) {
        $this->middle_name = $middle_name;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }
}
?>