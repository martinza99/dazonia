<?php
    session_start();
    require_once "sql.php";
    if($_SESSION["userId"]!=0){
        header("Location: .");
        die();
    }
    $userId = $_SESSION["userId"];

    if(isset($_POST["action"])){
        switch ($_POST["action"]){
            case "c": 
                $token = generateRandomString(32);
                $sql = $conn->prepare("INSERT INTO register (token) VALUES (?)");
                $sql->bind_param("s",$token);
                $sql->execute();
                die("Token created");
                break;
            case "d":
                $sql = $conn->prepare("DELETE FROM register WHERE id = ?");
                $sql->bind_param("i",$_POST["id"]);
                $sql->execute();
                die("Token deleted");
                break;
        }
    }
    require_once "../header.php";
?>

    <title>Token List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="../main.css<?php echo "?$hash" ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
function createToken(){
    $.post("token.php",{action:"c"},location.reload());
}
$(function(){
    $(".deleteButton").click(function(){
        
        if(confirm("Delete "+$(this).closest("tr")[0].id))
        deleteFile(this);
    });
    $(".deleteAllButton").click(function(){  console.log(1);      
        if(confirm($(".deleteButton").length + ' tokens will be deleted')){
            $(".deleteButton").each(deleteFiles);
        }
    });
});

function deleteFiles(i,_btn) {
    deleteFile(_btn);
}

function deleteFile(_btn) {
    var tr = _btn.closest("tr");
    $.post("token.php",
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
    $sql = $conn->prepare("SELECT * FROM register");
    $sql->execute();
    $result = $sql->get_result();
    $conn->close();
    echo '<table border="1">';
    echo '<th><button onclick="createToken();">#</button></th><th>Token</th><th><button class="deleteAllButton">X</button></th></th>';
    while($rows = $result->fetch_assoc()){
            echo "<tr id=\"$rows[id]\">";
            echo "<td>$rows[id]</td>";
            echo "<td><a href=\"$domain/login/register.php?token=$rows[token]\" target=\"_top\">$rows[token]</a>";//print token
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