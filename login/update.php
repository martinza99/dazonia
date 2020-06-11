<?php
    session_start();
    require_once "sql.php";
    require_once 'functions.php';
    checkAdmin();

    require_once "../header.php";
    if(isset($_POST["commit"]))
        exec("git reset --hard $_POST[commit] 2>&1", $execOutput, $exitCode);
?>


<title>Dazonia Update</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <form action="update.php" method="post" autocomplete="off">
        <label for="commit">Commit: </label><input type="text" name="commit" id="commit" placeholder="commit" value="master" size="40"><br>
        <input type="submit" value="Change version">
    </form>
   <?php
   if(isset($_POST["commit"])){
        echo "<ul>";
        foreach ($execOutput as $key => $value) {
            echo "<li>" . htmlspecialchars($value) . "</li>";
        }
        echo "<li>Exit code: $exitCode</li>";
        echo "</ul>";
   }
        require_once("../footer.php");
   ?>
</body>
</html>