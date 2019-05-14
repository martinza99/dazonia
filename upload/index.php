<?php
    session_start();
    require_once '../login/sql.php';
    if(!isset($_SESSION["userId"])){
        header('Location: ../login/');
        die();
    }
    $userId = $_SESSION["userId"];
    $sql = $conn->prepare("SELECT `name` FROM `users` WHERE `id` = ?");
    $sql->bind_param("s",$userId);
    $sql->execute();
    $result = $sql->get_result();
    $rows = $result->fetch_assoc();
    $username = $rows['name'];

    require_once "../header.php";
    echo '
        <title>Dazonia</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="screen" href="../main.css" />
        <script src="../main.js"></script>
    </head>
    <body>
        <div class="center">
            <form action="../upload.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                <input id="fileUp" name="file" type="file"><br>
                <input type="submit">
            </form>
        </div>
        <div class="bottom">';
       if($userId==0)
            echo '<a href="login/token.php" target="_top">create register token</a><br>';
		echo '<a href="/list" target="_top">File-List</a><br>
            <a href="login/logout.php" target="_top"><button>Logout</button></a><span> '.$username.'</span>
        </div>
    </body>
    </html>
';
?>