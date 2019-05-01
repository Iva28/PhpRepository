<?php
namespace Models;

class User {
    public $id;
    public $login;
    public $password;
    public $email;

    public static function create(string $login, string $password, string $email) {
        $obj = new User();
        $obj->login = $login;
        $obj->password = $password;
        $obj->email = $email;
        return $obj; 
    }
}

?>