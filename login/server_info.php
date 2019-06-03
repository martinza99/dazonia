<?php
    session_start();
    require_once "sql.php";
    require_once 'functions.php';
    if($_SESSION["userId"]>1||!checkLogin($_SESSION["userId"])){
        header("Location: .");
        die();
    }
    $userId = $_SESSION["userId"];

    echo "<table border=\"1\">";

    foreach($_SERVER as $valueType=>$value){
        echo "<tr><td>$valueType</td><td>$value</td></tr>";
    }
    echo "</table>";

?>