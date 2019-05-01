<?php
require_once './exceptions.php';

class Repo {

    public $host;
    public $dbname;
    public $user;
    public $password;

    public $pdo;

    public function __construct(string $host, string $dbname, string $user, string $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->password = $password;
        $this->create();
    }

    function create() {
        $this->pdo = new PDO("mysql:host=$this->host", $this->user, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $dbExists =  $this->pdo->query("SHOW DATABASES LIKE '$this->dbname'")->rowCount() > 0;
        if (!$dbExists) {
            $sql = "CREATE DATABASE $this->dbname";
            $this->pdo->exec($sql);
        }
        $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    function createDb($object) {
        $reflection = new ReflectionClass(get_class($object));
        $class = $reflection->getShortName()."s";

        $props = [];
        foreach ($reflection->getProperties() as $value) {
            $p = $value->getName();
            if ($p == 'id') continue;
            $props[$p] = getType($object->$p);
        }

        $properies = '';
        foreach ($props as $key => $value) {
            $type = '';
            if ($value == 'string') $type = 'VARCHAR(255)';
            if ($value == 'integer') $type = 'INT';
            $properies = $properies. "$key $type,";
        }
        $properies = substr($properies, 0, -1);

        $sql = "CREATE TABLE IF NOT EXISTS $class (
            id INT PRIMARY KEY AUTO_INCREMENT,
            $properies)";
        $this->pdo->exec($sql);
    }

    function insert($object) {
        $this->createDb($object);
        $reflection = new ReflectionClass(get_class($object));
        $props = [];
        $data = [];
        foreach ($reflection->getProperties() as $value) {
            $p = $value->getName();
            if ($p == 'id') continue;
            $props[] = $p;
            $data[] = $object->$p;
        }

        $class = $reflection->getShortName();
        $table = strtolower($class).'s';
        $pr = implode(", ", $props);
        for ($i=0; $i < count($data); $i++) { $chars[] = '?'; }
        $values = implode(',', $chars);
        $sql = "INSERT INTO $table ($pr) VALUES ($values)";
        for ($i=0; $i < count($props); $i++) { $props[$i] =  $props[$i]."=? "; }
        $cond = implode("AND ", $props);
        $query = "SELECT * FROM $table WHERE $cond";
        $exists = $this->pdo->prepare($query);
        $exists->execute($data);
        if ($exists->rowCount()) {
            throw new DublicateEntryException("$class already exists!");
        }
        else {
            $this->pdo->prepare($sql)->execute($data);
        }
    }

    function get($object) {
        $class = explode('\\', $object);
        $table = strtolower(end($class)).'s';
        $dbExists =  $this->pdo->query("SHOW TABLES LIKE '$table'")->rowCount() > 0;
        $arr = [];
        if (!$dbExists) return $arr;

        $statement = $this->pdo->prepare("SELECT * FROM $table");
        $statement->execute();
        while($obj = $statement->fetchObject($object)) {
            $arr[] = $obj;
        }
        return $arr;
    }

    function update($object) {
        $reflection = new ReflectionClass(get_class($object));
        $props = [];
        $values = [];
        $id = $object->id;
        foreach ($reflection->getProperties() as $value) {
            $p = $value->getName();
            if ($p == 'id') continue;
            $props[] = $p;
            $values[] = $object->$p;
        }

        $table = strtolower($reflection->getShortName()).'s';
        $sql = "UPDATE $table SET ";
        for ($i=0; $i < count($props); $i++) { 
            $tmp[] = $props[$i]."=? ";
        }
        $sql = $sql.implode(', ', $tmp)." WHERE id = $id";
        $update = $this->pdo->prepare($sql);
        $update->execute($values); 
    }

    function delete($class, $id) {
        $c = explode('\\', $class);
        $table = strtolower(end($c)).'s';
        $stmt = $this->pdo->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    function find($class, $id) {
        $c = explode('\\', $class);
        $table = strtolower(end($c)).'s';
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$id]); 
        $obj = $stmt->fetchObject($class);
        if ($obj === false) {
            throw new EntryNotFoundException("Not found!");
        } else {
            return $obj;
        }
    }
}
