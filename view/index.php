<?php
    require_once '../login/sql.php';
    if(isset($_GET["id"]))
        $id = htmlspecialchars($_GET["id"]);
    else{
        header("Location: ../list");
        die("No id");
    }
    
    $sql = $conn->prepare("SELECT * FROM files WHERE name = ? ORDER BY id ASC");
    $sql->bind_param("s",$id);
    $sql->execute();
    $result = mysqli_fetch_assoc($sql->get_result());
    $currID = $result["id"];
    
    $sql = $conn->prepare("SELECT * from files WHERE id < ? ORDER BY id DESC LIMIT 1");
    $sql->bind_param("i",$currID);
    $sql->execute();
    $result = mysqli_fetch_assoc($sql->get_result());
    $prev = $result["name"];
    
    if(isset($_GET["random"])){
        $sql = $conn->prepare("SELECT * FROM `files` ORDER BY rand() LIMIT 1");
    }else{
        $sql = $conn->prepare("SELECT * from files WHERE id > ? ORDER BY id ASC LIMIT 1");
        $sql->bind_param("i",$currID);
    }
    $sql->execute();
    $result = mysqli_fetch_assoc($sql->get_result());
    $next = $result["name"];
 
    $conn->close();

    require_once "../header.php";
    ?>
    <link rel="stylesheet" type="text/css" media="screen" href="../main.css" />
    <title><?php echo $id ?></title>
    <link rel="stylesheet" type="text/css" media="screen" href="view.css" />
    <script src="view.js"></script>
</head>

<body onkeydown="keyDown(event);">
    <?php
    echo "<input value=\"$domain/files/$id\" class=\"hiddenVal\" style=\"opacity:0; height=0px;\">";
    $id = "../files/".$id;
        echo "
            <div id=\"picDiv\" class=\"center\">
                <a href=\"$domain/view/?id=$next\" target=\"_top\"><img id=\"prev\" class=\"floatLink pic\" src=\"../files/$id\"></a>
                <a href=\"$domain/view/?id=$prev";
                if(isset($slide))
                    echo "&slide=$slide";
                if(isset($random))
                    echo "&slide=$random";
                "\" target=\"_top\"><img id=\"next\" class=\"floatLink pic\" src=\"../files/$id\"></a>
                <img id=\"centerImage\" class=\"pic\" src=\"../files/$id\">
            </div>
        ";
    require_once "../footer.php";
    if(isset($slide)){
        echo '
        <script>setTimeout(next, '.$slide.');
        function next(){document.querySelector("#next").click(); }
        </script>';
    }
    ?>
</body>
</html>
