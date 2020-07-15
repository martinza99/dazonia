<?php
    session_start();
    require_once "./sql.php";
    
    if(!(isset($_POST["token"]) && isset($_POST["username"]) && isset($_POST["password"]))){
        http_response_code(400);
        die('400 Bad Request<br>No registration token, username or password!');
    }
    
        
    $token = $_POST["token"];
    $sql = $conn->prepare("SELECT * FROM register WHERE token = ?");
    $sql->bind_param("s",$token);
    $sql->execute();

    if($sql->get_result()->num_rows==0){
        http_response_code(401);
        die("401 Unauthorized<br>invalid registration token!");
    }

    $username = htmlspecialchars($_POST["username"]);
    $password =  password_hash(htmlspecialchars($_POST["password"]),PASSWORD_DEFAULT);

    $sql = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $sql->bind_param("s",$username);
    $sql->execute();

    $result = $sql->get_result();
    if($result->num_rows>0){
        http_response_code(400);
        die("400 Bad Request<br>Name already in use!");
    }    
    else{
        $apiKey = generateRandomString(64);
        $sql = $conn->prepare("INSERT INTO users (name,password,apiKey) VALUES (?,?,?)");
        $sql->bind_param("sss",$username,$password,$apiKey);
        
        if(!$sql->execute()){
            http_response_code(500);
            die("500 Internal Server Error<br>SQL error! - Please contact an admin.");
        }
        $_SESSION["userId"] = $conn->insert_id;
        header("Location: /");
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
