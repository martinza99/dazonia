<?php

require_once "sql.php";
require_once 'functions.php';
checkAdmin();

require_once "../header.php";
exec("git pull https://github.com/martinza99/dazonia.git master 2>&1", $execOutput, $exitCode);
?>


<title>Dazonia Update</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <?php
    echo "<ul>";
    foreach ($execOutput as $key => $value) {
        echo "<li>" . htmlspecialchars($value) . "</li>";
    }
    echo "<li>Exit code: $exitCode</li>";
    echo "</ul>";
    require_once("../footer.php");
    ?>
</body>

</html>