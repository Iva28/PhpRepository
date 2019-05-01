<?php
namespace Models;

class Book  {
    public $id;
    public $title;
    public $author;
    public $year;

    public static function create(string $title, string $author, int $year) {
        $obj = new Book();
        $obj->title = $title;
        $obj->author = $author;
        $obj->year = $year;
        return $obj; 
    }
}

?>