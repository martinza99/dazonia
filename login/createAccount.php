<?php
    session_start();
    require_once "./sql.php";
    
    if(!isset($_POST["token"]))
        die("No token!");
    if($_POST["password"]!=$_POST["password2"])
        die("Passwords don't match!");
        
    $token = $_POST["token"];
    $sql = $conn->prepare("SELECT * FROM register WHERE token = ?");
    $sql->bind_param("s",$token);
    $sql->execute();

    if($sql->get_result()->num_rows==0)
        die("Wrong token!");

    $username = htmlspecialchars($_POST["username"]);
    $password =  password_hash(htmlspecialchars($_POST["password"]),PASSWORD_DEFAULT);

    $sql = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $sql->bind_param("s",$username);
    $sql->execute();

    if($sql->get_result()->num_rows>0)
        die("Name already in use!");

    else{
        $apiKey = generateRandomString(64);
        $sql = $conn->prepare("INSERT INTO users (name,password,apiKey) VALUES (?,?,?)");
        $sql->bind_param("sss",$username,$password,$apiKey);
        
        if(!$sql->execute()){
            die("SQL error! - Please contact an admin.");
        }
        $_SESSION["userId"] = $conn->insert_id;
        header("Location: $domain");
        $sql = $conn->prepare("DELETE FROM register WHERE token = ?");
        $sql->bind_param("s",$token);
        $sql->execute();
        die("successfully created account");
    }

function generateRandomString($length){//generates random strings
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>