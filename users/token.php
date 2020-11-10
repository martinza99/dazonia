<?php

require_once "sql.php";
require_once 'functions.php';

checkAdmin();

if (isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case "c":
            $token = generateRandomString(32);
            $sql = $conn->prepare("INSERT INTO register (token) VALUES (?)");
            $sql->bind_param("s", $token);
            $sql->execute();
            die("Token created");
            break;
        case "d":
            $sql = $conn->prepare("DELETE FROM register WHERE id = ?");
            $sql->bind_param("i", $_POST["id"]);
            $sql->execute();
            die("Token deleted");
            break;
    }
}
require_once "../header.php";
?>

<title>Token List</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="login.js<?php echo "?$version" ?>"></script>
</head>

<body>
    <?php
    $sql = $conn->prepare("SELECT * FROM register");
    $sql->execute();
    $result = $sql->get_result();
    echo '<table border="1">';
    echo '<th><button onclick="createToken();">#</button></th><th>Token</th><th><button class="deleteAllButtonToken">X</button></th></th>';
    while ($rows = $result->fetch_object()) {
        echo "<tr id=\"$rows->id\">";
        echo "<td>$rows->id</td>";
        echo "<td><a href=\"/login/register.php?token=$rows->token\">$rows->token</a>"; //print token
        echo "<td><button class=\"deleteButtonToken\">X</button></td>";
        echo "</tr>";
    }
    echo "</table>";
    require_once "../footer.php";
    ?>
</body>

</html>

<?php
function generateRandomString($length)
{ //generates random strings
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>