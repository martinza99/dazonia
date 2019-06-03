<?php
    require_once '../login/sql.php';
    require_once '../login/functions.php';
    echo $_SERVER[QUERY_STRING];
    header("Location: $domain/list/?$_SERVER[QUERY_STRING]");
    die();
?>