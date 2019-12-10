<?php
    session_start();
    require_once 'sql.php';
    require_once 'functions.php';
        if(!isset($_POST['username'])){
            header("Location: $domain/login/");
            die();
        }

        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

            
        if(!checkUser($username,$password,$conn))
            die('Wrong Username or Password <br><a href="'.$domain.'/login/" target=\"_top\">go to Login</a>');
            
        header("Location: $domain");

function checkUser($username,$password,$conn){
    $sql = $conn->prepare("SELECT * FROM `users` WHERE `name` = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();
    $user = $result->fetch_object();
    if($result->num_rows == 0)
        return false;
    if(password_verify($password, $user->password)){
        $_SESSION["userId"] = $user->id;
        $sql = $conn->prepare("UPDATE users SET lastLogin = NOW() WHERE `id` = ?");
        $sql->bind_param("i", $user->id);
        $sql->execute();
        return true;
    }
}
?>