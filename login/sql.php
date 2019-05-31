<?php
    $conn = new mysqli('localhost','USERNAME','PASSWORD','DATABASE');
    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    
    $domain = "http://localhost/dazonia";
    exec('git rev-parse --verify HEAD', $output);//requires git
    $hash = substr($output[0],0,6);
    
    function checkLogin($userID){
        $sql = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $sql->bind_param("i",$userID);
        if($sql->affected_rows==0){
            header("$domain/login");
            die("Login error");
        }
    }
?>