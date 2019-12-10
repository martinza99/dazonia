<?php
session_start();
require_once '../login/sql.php';
require_once '../login/functions.php';
if (isset($_GET["id"]))
    $fileName = htmlspecialchars($_GET["id"]);
else {
    header("Location: $domain/list");
    die("No id");
}
$random = "off";
$slide = 0;
if (isset($_GET["slide"]))
    $slide = htmlspecialchars($_GET["slide"]);
if (isset($_GET["random"]))
    $random = htmlspecialchars($_GET["random"]);

$filter = "";
$q = "";
if (isset($_GET["q"])) {
    $filter = $_GET["q"];
    $q = $_GET["q"];
}

$sql = $conn->prepare("SELECT id FROM files WHERE name = ?");
$sql->bind_param("s", $fileName);
$sql->execute();
$result = $sql->get_result();
$fileId = $result->fetch_object()->id;
if ($fileId == NULL)
    die("ID not found");

#region sql
$paramValues = array();
$paramType = "";

array_push($paramValues, $fileId);
$paramType .= "i";

$searchTag = filterSearch("tag:");
if ($searchTag != "") {
    array_push($paramValues, $searchTag);
    $paramType .= "s";
}
$searchFile = filterSearch("file:");
if ($searchFile != "") {
    array_push($paramValues, $searchFile);
    $paramType .= "s";
}

$searchUser = filterSearch("u:");
if ($searchUser != "") {
    array_push($paramValues, intval($searchUser));
    $paramType .= "i";
}
$searchRating = filterSearch("r:");
if ($searchRating > 0) {
    array_push($paramValues, intval($searchRating));
    $paramType .= "i";
}
while (substr($filter, 0, 1) == " ")
    $filter = substr($filter, 1);
while (substr($filter, -1, 1) == " ")
    $filter = substr($filter, 0, -1);
if ($filter != "") {
    array_push($paramValues, $filter);
    $paramType .= "s";
}

array_push($paramValues, $fileId);
$paramType .= "i";

if ($searchTag != "") {
    array_push($paramValues, $searchTag);
    $paramType .= "s";
}
if ($searchFile != "") {
    array_push($paramValues, $searchFile);
    $paramType .= "s";
}

if ($searchUser != "") {
    array_push($paramValues, intval($searchUser));
    $paramType .= "i";
}
if ($searchRating > 0) {
    array_push($paramValues, intval($searchRating));
    $paramType .= "i";
}
if ($filter != "") {
    array_push($paramValues, $filter);
    $paramType .= "s";
}
$sql = "
        SELECT * FROM(
            SELECT * FROM(
                SELECT
                    files.id AS fileID,
                    files.name AS fileName,
                    files.ogName AS fileOgName,
                    files.userId AS fileUserId,
                    users.name AS username,
                    AVG(userrating.rating) AS avgrating
                FROM
                    files
                LEFT JOIN users ON users.id = files.userId
                LEFT JOIN userrating ON userrating.fileId = files.id
                LEFT JOIN tagFile ON tagfile.fileId = files.id
                LEFT JOIN tags ON tags.id = tagfile.tagId
                WHERE
                    files.id >= ? "; //current ID >= files.id - left + mid
if ($searchTag != "")
    $sql .= "AND tags.name = ? "; //filter tags
if ($searchFile != "")
    $sql .= "AND files.name LIKE concat('%',?,'%') "; //filter name
if ($searchUser != "")
    $sql .= "AND users.id = ? "; //filter upload user
$sql .= "GROUP BY files.id
                ORDER BY files.id ASC
                LIMIT 2 -- prev current
            ) AS innerPrevCurr
        WHERE
            0 = 0 "; // placeholder WHERE in case no filter
if ($searchRating > 0)
    $sql .= "AND avgrating >= ? "; //min rating search
if ($searchRating === "0")
    $sql .= "AND avgrating IS NULL "; //unrated search
if ($filter != "")
    $sql .= "AND fileOgName LIKE concat('%',?,'%') "; //filter title
$sql .= "
        ORDER BY innerPrevCurr.fileID DESC
        LIMIT 2 -- SQL bug
        )AS prevCurr
    UNION
    SELECT * FROM(
        SELECT * FROM(
            SELECT
                files.id AS fileID,
                files.name AS fileName,
                files.ogName AS fileOgName,
                files.userId AS fileUserId,
                users.name AS username,
                AVG(userrating.rating) AS avgrating
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            LEFT JOIN tagFile ON tagfile.fileId = files.id
            LEFT JOIN tags ON tags.id = tagfile.tagId
            WHERE ";
if ($random == "off")
    $sql .= "files.id < ? "; //current ID <= files.id - curr + right
else
    $sql .= "files.id <> ? ";
if ($searchTag != "")
    $sql .= "AND tags.name = ? "; //filter tags
if ($searchFile != "")
    $sql .= "AND files.name LIKE concat('%',?,'%') "; //filter name
if ($searchUser != "")
    $sql .= "AND users.id = ? "; //filter upload user
$sql .= "GROUP BY files.id ORDER BY ";
if ($random == "off")
    $sql .= "files.id DESC ";
else
    $sql .= "RAND() ";
$sql .= "
        ) AS innerNext

        WHERE
            0 = 0 "; // placeholder WHERE in case no filter
if ($searchRating > 0)
    $sql .= "AND avgrating <= ? "; //min rating search
if ($searchRating === "0")
    $sql .= "AND avgrating IS NULL "; //unrated search
if ($filter != "")
    $sql .= "AND fileOgName LIKE concat('%',?,'%') "; //filter title
$sql .= "
            LIMIT 1 -- only next
        )AS next";
$sql = $conn->prepare($sql);
if ($sql === false)
    trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->errno . ' ' . $conn->error, E_USER_ERROR);
$a_params = array();
foreach ($paramValues as $key => $value) {
    $a_params[$key] = &$paramValues[$key];
}
call_user_func_array(
    array($sql, 'bind_param'),
    array_merge(array($paramType), $paramValues)
);

$sql->execute();
$result = $sql->get_result();
#endregion sql

#region fetch query
$left;
$right;
$username;
$userId;
$rating;
if ($rows = $result->fetch_object()) {
    if ($rows->fileID > $fileId) {
        $left = $rows->fileName;
        $rows = $result->fetch_object();
    }
    $username = $rows->username;
    $rating = $rows->avgrating;
    $userId = $rows->fileUserId;
    $username = $rows->username;
    if ($rows = $result->fetch_object())
        $right = $rows->fileName;
} else {
    die("No result");
}
if ($username == null)
    $username = "deleted user[$userId]";
if (!isset($user)) {
    unset($left);
    unset($right);
}

if(isset($left))
echo "<img src=\"../files/$left\" hidden>";
if(isset($right))
echo "<img src=\"../files/$right\" hidden>";


#endregion fetch query
require_once "../header.php";
?>
<title><?php echo $fileName ?></title>
<link rel="stylesheet" type="text/css" media="screen" href="view.css<?php echo "?$hash" ?>" />
<script src="view.js<?php echo "?$hash" ?>"></script>
</head>

<body onkeydown="keyDown(event);">
    <?php
    $qq = $q;
    if ($qq != "")
        $qq = "&q=" . urlencode($qq);

    if (isset($_SESSION["userId"]) && $_SESSION["userId"] < 2) {
        echo '<div class="right top replace">';
        echo '<a href="javascript:document.querySelector(\'#fileUp\').click();">Replace Image</a>';
        echo '
        <form action="../upload.php" method="POST" enctype="multipart/form-data" autocomplete="off" id="replaceForm">
            <input id="fileUp" name="file" type="file"><br>
            <input type="hidden" value="true" name="skip">
            <input type="text" value="' . $fileName . '" name="replace">
        </form>';
        echo "</div>";
    }

    echo "<input value=\"$domain/files/$fileName\" class=\"hiddenVal\" style=\"opacity:0; height=0px;\" readonly>";
    echo "
        <div id=\"picDiv\" class=\"center\">";
    if (isset($left))
        echo "<a href=\"$domain/view/?id=$left$qq\" target=\"_top\"><img id=\"prev\" class=\"floatLink pic\" src=\"../files/$fileName\"></a>";
    if (isset($right)) {
        echo "<a href=\"$domain/view/?id=$right$qq";
        if ($slide > 0)
            echo "&slide=$slide";
        if ($random == "on")
            echo "&random=on";
        echo "\" target=\"_top\">";
    }
    echo "<img id=\"next\" class=\"floatLink pic\" src=\"../files/$fileName\"></a>";
    if (substr(mime_content_type("../files/$fileName"), 0, 5) === "image")
        echo "<img id=\"centerImage\" class=\"pic\" src=\"../files/$fileName\">";
    else
        echo "<video id=\"centerImage\" class=\"pic\" src=\"../files/$fileName\" autoplay controls loop onloadstart=\"this.volume=0.2\">";
    echo "</div>
    ";
    echo "
    <div class=\"bottom\">
        <div class=\"starContainer\">
            " . rating($rating) . "
        </div>
        <a href=\"$domain\" target=\"_top\"><button>← Back</button></a>";
    if (isset($user)) {
        echo '
            <button type="button" data-toggle="collapse" data-target="#collapseRandom" aria-expanded="false" aria-controls="collapseRandom" onclick="stopSlide();">&nbsp;<span class="glyphicon glyphicon-picture"></span></button>
            <div class="collapse slideContainer" id="collapseRandom">
                <div class="card card-body">
                    <form action="/view" method="GET">
                    <label>Random</label> <input type="checkbox" name="random" checked="' . $random . '">
                    <button type="submit">Start</button><br>
                        <label>Delay</label> <input type="number" class="darkInput" name="slide" value="';
        if ($slide == 0) echo "3";
        else echo $slide;
        echo '" min="0" step="0.5" placeholder="seconds" style="width:80px;">
                        <input type="hidden" name="id" value="' . $_GET["id"] . '">
                        <input type="hidden" name="q" value="' . $q . '">';
        echo '</form>
                </div>
            </div>';
    }
    echo "<span>Uploaded by: <a href=\"$domain/list?q=u%3A$userId\" target=\"_top\">$username</a></span>
    </div>";
    if ($slide > 0) {
        $slide *= 1000;
        echo '
        <script>slide = setTimeout(next, ' . $slide . ');
        function next(){document.querySelector("#next").click(); }
        </script>';
    }
    echo '
    <div class="bottom right"><div class="tagContainer">';
    $sql = $conn->prepare("SELECT tags.id AS tagID, tags.name AS tagName FROM tagfile INNER JOIN tags ON tags.id = tagfile.tagId WHERE tagFile.fileId = ? ORDER by tags.name");
    $sql->bind_param("i", $fileId);
    $sql->execute();
    if ($sql === false)
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->errno . ' ' . $conn->error, E_USER_ERROR);
    $result = $sql->get_result();
    while ($rows = $result->fetch_object()) {
        echo "<div class=\"sugg\"><a href=\"$domain/list?q=tag%3A$rows->tagName\" target=\"_top\">$rows->tagName</a>";
        if (isset($user))
        // if($user->isAdmin)//if user is admin
            echo "<span class=\"deleteTag glyphicon glyphicon-remove\"></span>";
        echo "</div>";
    }
    // if(isset($user)&&$user->isAdmin){//if user is admin
    if (isset($user)) {
        echo '
        <input list="tagList" placeholder="add tag" class="tagInput disableHotkeys darkInput">
        <button class="ogButton">+</button>';
        printDatalistTags();
    }
    ?>
    </div>
    <a onclick="$('.tagContainer').toggle()" class="toggleTagsLink">toggle tags</a>
    </div>
</body>

</html>


<?php
function rating($rating)
{
    if ($rating == "")
        $rating = 0;
    if ($rating - floor($rating) == 0.5)
        $rating = floor($rating);
    $rating = (int) $rating; // 5.000 -> 5
    return '<img class="star" src="../list/img/' . $rating . '.png">';
}

function filterSearch($find)
{
    $filter = $GLOBALS["filter"];
    $filtered = "";
    $start = strpos($filter, $find);


    if (gettype($start) != "boolean") {
        $len = strpos($filter, " ", $start) - $start - strlen($find);
        if ($len > 0) {
            $filtered = substr($filter, $start + strlen($find), $len);
            $GLOBALS["filter"] = substr_replace($filter, "", $start, strlen($find) + $len + 1);
        } else {
            $filtered = substr($filter, $start + strlen($find));
            $GLOBALS["filter"] = substr_replace($filter, "", $start);
        }
    }
    return $filtered;
}
?>