<?php
    require_once "sql.php";

    $domain = "http://localhost/dazonia";
    exec('git rev-parse --verify HEAD', $output);//requires git
    $hash = substr($output[0],0,6);
    
    function checkLogin($userID){
        $conn = $GLOBALS["conn"];
        $sql = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $sql->bind_param("i",$userID);
        $sql->execute();
        if($sql->get_result()->num_rows==0){
            session_destroy();
            return false;
        }
        return true;
    }
?>