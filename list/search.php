<?php
    require_once '../login/sql.php';
    echo $_SERVER[QUERY_STRING];
    header("Location: $domain/list/?$_SERVER[QUERY_STRING]");
    die();
?>