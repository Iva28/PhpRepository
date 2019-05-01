<?php 
require_once 'connection.php';
require_once 'repo.php';
require_once './Models/book.php';
use Models\Book;

$repo = new Repo($host, $dbname, $user, $password);

$books;

$id = 0;
$title = '';
$author = '';
$year = date('Y'); 

$action = 'Create';

if(isset($_GET['update'])) {
    $id = $_GET['update'];
    try {
        $book = $repo->find(Book::class, $id);
        $title = $book->title;
        $author = $book->author;
        $year = $book->year;
        $action = 'Update';
    } catch (\Throwable $th) {
        echo '<script type="text/javascript">
                alert("'.$ex->getMessage().'");
                window.location = "books.php";
              </script>';
    }

}

if (isset($_POST['title']) && isset($_POST['author']) && isset($_POST['year'])) {
    if (!empty($_POST['title']) && !empty($_POST['author']) && !empty($_POST['year'])) {
        if(!empty($_GET['action'])) {
            if($_GET['action'] == 'Update') {
                $book = Book::create($_POST['title'], $_POST['author'], $_POST['year']);
                $book->id = $_POST['bookId'];
                try {
                    $repo->update($book);
                } catch (\Throwable $ex) {
                    echo '<script type="text/javascript">
                        alert("'.$ex->getMessage().'");
                        window.location = "books.php";
                        </script>';
                }
            } 
            if($_GET['action'] == 'Create') {
                $book = Book::create($_POST['title'], $_POST['author'], $_POST['year']);
                try {
                    $repo->insert($book);
                } catch (\Throwable $ex) {
                    echo '<script type="text/javascript">
                        alert("'.$ex->getMessage().'");
                        window.location = "books.php";
                        </script>';
                } 
            } 
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $repo->delete(Book::class, $id);
    } catch (\Throwable $ex) {
        echo '<script type="text/javascript">
                alert("'.$ex->getMessage().'");
                window.location = "books.php";
              </script>';
    } 
} 


$books = $repo->get(Book::class);

?>

<style>
    form { width: 250px;}
    h4 { text-align: center; }
    div { width: fit-content; }
    input[type=submit] { margin: auto; display: block;}
    input[type=text],input[type=password],input[type=email] { float: right; }
    table { border-collapse: collapse; text-align: center; }
    table, td, th { border: 1px solid black; padding: 5px; }
    caption {padding-bottom: 10px; font-weight: bold;}
</style>

<div>   
    <h4>Form</h4>
    <form method="POST" action="books.php?action=<?=$action?>">
        Title: <input type="text" name="title" required value="<?= $title?>"/><br><br>
        Author: <input type="text" name="author" required value="<?=$author?>"/><br><br>
        Year: <input type="number" name="year" min='1900' max="<?=$year?>" required value="<?=$year?>"/><br><br>
        <input type="hidden" id="bookId" name="bookId" value="<?=$id?>">      
        <input type="submit" name="submit" value="<?=$action?>">  
    </form>
</div>
<br>

<div>
    <?php if (!empty($books)) : ?>
    <?php 
     foreach ($books as $book) {
       echo "<div>$book->title, $book->author, $book->year 
                <a href='?delete=$book->id'>delete</a>
                <a href='?update=$book->id'>update</a>
            </div>";
    }
    ?>
    <?php else: ?>
        <p>No data</p>
    <?php endif;?>
    <br>
    <a href='index.php'>Back</a>
</div>

