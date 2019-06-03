<?php
    session_start();
    require_once "sql.php";
    require_once 'functions.php';
    if($_SESSION["userId"]>1||!checkLogin($_SESSION["userId"])){
        header("Location: .");
        die();
    }
    $userId = $_SESSION["userId"];

    if(isset($_POST["action"])){
        switch ($_POST["action"]){
            case "d":
                $sql = $conn->prepare("DELETE FROM users WHERE id = ?");
                $sql->bind_param("i",$_POST["id"]);
                $sql->execute();
                die("User deleted");
                break;
        }
    }
    require_once "../header.php";
?>

    <title>User List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="../main.css<?php echo "?$hash" ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$(function(){
    $(".deleteButton").click(function(){
        
        if(confirm("Delete "+$(this).closest("tr")[0].id))
        deleteFile(this);
    });
    $(".deleteAllButton").click(function(){
        alert("disabled. I really don't think that's a good idea."); 
    });
});

function deleteFile(_btn) {
    var tr = _btn.closest("tr");
    $.post("users.php",
    {
        id: tr.id,
        action: "d"
    },
        function(){tr.remove();}
    );
}
</script>
</head>
<body>
<?php
    $sql = $conn->prepare("SELECT * FROM users ORDER BY id");
    $sql->execute();
    $result = $sql->get_result();
    echo '<table border="1">';
    echo '<th><a href="token.php" target="_top" style="color:#2196F3;"><button>#</a></th><th>Name</th><th><button class="deleteAllButton">X</button></th></th>';
    while($rows = $result->fetch_assoc()){
            echo "<tr id=\"$rows[id]\">";
            echo "<td>$rows[id]</td>";
            echo "<td><a href=\"$domain/list/?q=u%3A$rows[id]\" target=\"_top\">$rows[name]</a>";//print token
            echo "<td><button class=\"deleteButton\">X</button></td>";
            echo "</tr>";
    }
    echo "</table>";
    require_once "../footer.php";  
?>
</body>
</html>

<?php
function generateRandomString($length){//generates random strings
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>