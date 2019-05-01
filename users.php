<?php 
require_once 'connection.php';
require_once 'repo.php';
require_once './Models/user.php';
require_once './exceptions.php';

use Models\User;

$repo = new Repo($host, $dbname, $user, $password);

$users;

$id = 0;
$login = '';
$password = '';
$email = '';

$action = 'Create';

if(isset($_GET['update'])) {
    $id = $_GET['update'];
    try {
        $user = $repo->find(User::class, $id);
        $login = $user->login;
        $password = $user->password;
        $email = $user->email;
        $action = 'Update';
    } catch (\Throwable $ex) {
        echo '<script type="text/javascript">
                alert("'.$ex->getMessage().'");
                window.location = "users.php";s
              </script>';
    }
    
}

if (isset($_POST['login'], $_POST['password'], $_POST['email'])) {
    if (!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['email'])) {
        if(!empty($_GET['action'])) {
            if($_GET['action'] == 'Update') {
                $user = User::create($_POST['login'], $_POST['password'], $_POST['email']);
                $user->id = $_POST['userId'];
                try {
                    $repo->update($user);
                } catch (\Throwable $ex) {
                    echo '<script type="text/javascript">
                        alert("'.$ex->getMessage().'");
                        window.location = "users.php";s
                        </script>';
                }
            }
            if($_GET['action'] == 'Create') {
                $user = User::create($_POST['login'], $_POST['password'], $_POST['email']);
                try {
                    $repo->insert($user);
                } catch (\Throwable $ex) {
                    echo '<script type="text/javascript">
                        alert("'.$ex->getMessage().'");
                        window.location = "users.php";
                        </script>';
                } 
            } 
        } 
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $repo->delete(User::class, $id);
    } catch (\Throwable $ex) {
        echo '<script type="text/javascript">
                alert("'.$ex->getMessage().'");
                window.location = "users.php";
              </script>';
    } 
} 

$users = $repo->get(User::class);

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
    <form method="POST" action="users.php?action=<?=$action?>">
        Login: <input type="text" name="login" required value="<?= $login?>"/><br><br>
        Password: <input type="password" name="password" required value="<?=$password?>"/><br><br>
        Email: <input type="email" name="email" required value="<?=$email?>"/><br><br>
        <input type="hidden" id="userId" name="userId" value="<?=$id?>">      
        <input type="submit" name="submit" value="<?=$action?>">  
    </form>
</div>
<br>

<div>
    <?php if (!empty($users)) : ?>
    <?php 
     foreach ($users as $user) {
       echo "<div>$user->login, $user->password, $user->email 
                <a href='?delete=$user->id'>delete</a>
                <a href='?update=$user->id'>update</a>
            </div>";
    }
    ?>
    <?php else: ?>
        <p>No data</p>
    <?php endif;?>
    <br>
    <a href='index.php'>Back</a>
</div>

