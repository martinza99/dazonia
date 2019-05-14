<?php
    session_start();
    require_once "./sql.php";

    if(!isset($_POST["token"]))
        die("No token!");
    if($_POST["password"]!=$_POST["password2"])
        die("Passwords don't match!");
        
    $token = $_POST["token"];
    $username = htmlspecialchars($_POST["username"]);
    $password =  password_hash(htmlspecialchars($_POST["password"]),PASSWORD_DEFAULT);

    $sql = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $sql->bind_param("s",$username);
    $sql->execute();

    if($sql->get_result()->num_rows>0)
        die("Name already in use!");

    $sql = $conn->prepare("DELETE FROM register WHERE token = ?");
    $sql->bind_param("s",$token);
    $sql->execute();
    if($conn->affected_rows==0)
        die("Wrong token!");
    else{
        $sql = $conn->prepare("INSERT INTO users (name,password) VALUES (?,?)");
        $sql->bind_param("ss",$username,$password);
        $sql->execute();
        $_SESSION["userId"] = $conn->insert_id;
        header("Location: $domain");
        die("successfully created account");
    }
?>