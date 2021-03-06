<?php
session_start();
require_once "login/sql.php";
require_once 'login/functions.php';

checkLogin();

$p = 1;
if (isset($_GET["p"])) {
    $p = intval(htmlspecialchars($_GET["p"]));
    if ($p < 1)
        $p = 1;
    $offset = 100 * ($p - 1);
} else {
    $offset = 0;
}
require_once "header.php";
?>
<title>sideView</title>
</head>

<body>
    <?php

    $sql = $conn->prepare("SELECT files.*, users.name AS username, AVG(userrating.rating) AS avgrating FROM files LEFT JOIN users on users.id = files.userId LEFT JOIN userrating on userrating.fileId = files.ID LEFT JOIN tagfile ON tagfile.fileid = files.id WHERE tagfile.tagid = 11 GROUP BY files.id ORDER BY id DESC LIMIT 100 OFFSET ?");
    $sql->bind_param("i", $offset);
    $sql->execute();
    $result = $sql->get_result();
    $conn->close();
    echo "<div class=\"potato\">";
    while ($rows = $result->fetch_object()) {
        echo "<a  href=\"/view/$rows->name?q=tag%3Asafe\" target=\"_top\">"; //open link
        echo "<div class=\"pics picsBorder\" id=\"$rows->name\">"; //open table cell
        if (substr($rows->name, -4) == ".gif")
            echo '<button class="thumbButton sideView">►</button>';
        echo rating($rows->avgrating);

        list($width, $height) = getimagesize("thumbnails/$rows->name");

        echo "<img class=\"thumb\" src=\"thumbnails/$rows->name\" alt=\"$rows->name\" loading=\"lazy\" width=\"$width\" height=\"$height\">"; //print thumbnail
        echo "</div></a>"; //close link and table cell
    }
    echo "</div>";
    echo '<div class="pageButtons">
        <a href="/?p=' . ($p - 1) . '"><button>←</button></a>
        <span> ' . $p . ' </span>
        <a href="/?p=' . ($p + 1) . '"><button>→</button></a>
    </div>';

    function rating($rating)
    {
        if ($rating == "" || $rating == 0)
            return;
        if ($rating - floor($rating) == 0.5)
            $rating = floor($rating);
        $rating = (int) $rating; // 5.000 -> 5
        return '<img class="starView" src="list/img/' . $rating . '.png">';
    }
    ?>
</body>

</html>