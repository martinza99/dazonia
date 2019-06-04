<?php
    require_once '../login/sql.php';
    require_once '../login/functions.php';
    echo "<body onload=\"clickLink();\">
        <a href=\"$domain/list/?$_SERVER[QUERY_STRING]\" target=\"_top\" class=\"link\" style=\"opacity:0;\">$domain/list/?$_SERVER[QUERY_STRING]</a>
        <script>
            function clickLink(){
                document.querySelector(\".link\").click();
            }
        </script>
    </body>";
    die();
?>

