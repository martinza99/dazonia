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
        $conn->close();

        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row['password'])){
            $_SESSION["userId"] = $row['id'];
            return true;
        }
    }
?>