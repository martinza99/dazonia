<?php
    session_start();
    require_once "../login/sql.php";
    require_once '../login/functions.php';
    checkLogin();

    $filter = "";
    $q = "";
    if (isset($_GET["q"])) {
        $filter = $_GET["q"];
        $q = $_GET["q"];
    }
    $p = 1;
    if (isset($_GET["p"]))
        $p = intval(htmlspecialchars($_GET["p"]));
    if ($p < 1)
        $p = 1;
    $offset = 100 * ($p - 1);
    require_once "../header.php";
?>
<title>File List</title>
<link rel="stylesheet" type="text/css" media="screen" href="list.css<?php echo "?$hash" ?>" />
<script src="pics.js<?php echo "?$hash" ?>"></script>
</head>

<body>
    <?php
    $dir = scandir("../bg/");
    if (count($dir) > 3) {
        $randBG;
        do
            $randBG = $dir[rand(2, count($dir) - 1)];
        while ($randBG == "index.php");
        echo "<img src=\"/bg/$randBG\" style=\"position:fixed; right:0; bottom:0; z-index:-1; max-width: 35%; max-height:90%; opacity: 0.7;\">";
    }
    #region SQL
    $paramValues = array();
    $paramType = "";

    $searchTag = filterSearch("tag:");
    if ($searchTag != "") {
        array_push($paramValues, $searchTag);
        $paramType .= "s";
    }

    $searchParent = filterSearch("p:");
    if ($searchParent != "") {
        array_push($paramValues, $searchParent);
        $paramType .= "i";
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
    if (isset($_GET["debug"])) {
        var_dump($paramValues);
    }

    array_push($paramValues, $offset);
    $paramType .= "i"; {
        $sql = "
        SELECT
            subtable.*,
            lTable.lRating,
            mTable.mRating,
            cTable.cRating,
            rTable.rRating
        FROM
            (
            SELECT
                files.id AS fileID,
                files.name AS fileName,
                files.ogName AS fileOgName,
                files.userId AS fileUserId,
                DATE_FORMAT(files.created, '%d-%m-%Y') AS fCreated,
                users.name AS username,
                AVG(userrating.rating) AS avgrating,
                tags.name AS tagname
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            LEFT JOIN tagFile ON tagfile.fileId = files.id
            LEFT JOIN tags ON tags.id = tagfile.tagId
            WHERE
                files.name = files.name ";
        if ($searchTag != "")
            $sql .= "AND tags.name = ? ";
        if ($searchParent != "")
            $sql .= "AND tags.parentId = ? ";
        if ($searchFile != "")
            $sql .= "AND files.name LIKE concat('%',?,'%') ";
        if ($searchUser != "")
            $sql .= "AND users.id = ? ";
        $sql .= "GROUP BY
                files.id
        ) AS subtable
        LEFT JOIN(
            SELECT
                files.id AS lfileID,
                userrating.rating AS lRating
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            WHERE
                userrating.userID = 0
        ) AS lTable
        ON
            lTable.lfileID = subtable.fileID
        LEFT JOIN(
            SELECT
                files.id AS mfileID,
                userrating.rating AS mRating
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            WHERE
                userrating.userID = 1
        ) AS mTable
        ON
            mTable.mfileID = subtable.fileID
        LEFT JOIN(
            SELECT
                files.id AS cfileID,
                userrating.rating AS cRating
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            WHERE
                userrating.userID = 6
        ) AS cTable
        ON
        cTable.cfileID = subtable.fileID
        LEFT JOIN(
            SELECT
                files.id AS rfileID,
                userrating.rating AS rRating
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            WHERE
                userrating.userID = 8
        ) AS rTable
        ON
            rTable.rfileID = subtable.fileID
        WHERE
            fileName = fileName ";
        if ($searchRating > 0)
            $sql .= "AND avgrating >= ? ";
        if ($searchRating === "0")
            $sql .= "AND avgrating IS NULL ";
        if ($filter != "")
            $sql .= "AND fileOgName LIKE concat('%',?,'%') ";
        $sql .= "ORDER BY subtable.fileID DESC
        LIMIT 100 OFFSET ?";
    }
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
    $conn->close();

    #endregion SQL

    echo '<div class="listTableDiv">';
    $q = urlencode($q);
    $navButtons = '<div class="navButtons"><a href="' . $domain . '/list?p=' . ($p - 1);
    if(!empty($q))
        $navButtons .= "&q=$q";
    $navButtons .= '" target="_top"><button>←</button></a><span> ' . $p . ' </span><a href="' . $domain . '/list?p=' . ($p + 1);
    if(!empty($q))
        $navButtons .= "&q=$q";
    $navButtons .= '" target="_top"><button>→</button></a>';
    echo $navButtons;
    echo '<button onClick="tagSelect(this)";>add tags</button></div>';
    echo '<table class="listTable">
        <tr>
            <th>Preview</th>
            <th>Rating</th>
            <th>Filename</th>
            <th>Title</th>
            <th class="listUploader">Uploader</th>
            <th class="listUploadDate">Upload Date</th>';
    echo "<th>";
    echo "<button class=\"deleteAllButton\">X</button>";
    if ($user->isAdmin) {
        switch ($user->id) {
            case 0:
                $temp = "l";
                break;
            case 1:
                $temp = "m";
                break;
            case 6:
                $temp = "c";
                break;
            case 8:
                $temp = "r";
                break;
        }
        echo "<script>var USERNAME = '$temp';</script>";
    }
    echo "</th></tr>";
    while ($rows = $result->fetch_object()) {
        echo "<tr id=\"$rows->fileName\">";
        echo "<td><a href=\"$domain/view/$rows->fileName";
        if(!empty($q))
            echo "?q=$q";
        echo "\" target=\"_top\"><div class=\"picsList\">";
        if (substr($rows->fileName, -4) == ".gif")
            echo '<button class="thumbButton listView">►</button>';
        echo "<img class=\"thumb\" src=\"../thumbnails/$rows->fileName\" alt=\"$rows->fileName\">"; //print thumbnail
        echo "</div></a></td><td>";
        echo "<div class=\"starContainer\">";
        if ($user->isAdmin) {
            echo rating($rows->lRating, "l");
            echo rating($rows->mRating, "m");
            echo rating($rows->avgrating, "");
            echo rating($rows->cRating, "c");
            echo rating($rows->rRating, "r");
        }
        else
            echo rating($rows->avgrating, "");
        echo "</div></td>";
        echo "<td><a href=\"$domain/files/$rows->fileName\" target=\"_top\">$rows->fileName</a></td>"; //print filename
        echo "<td class=\"og\"><div class=\"fileName"; //print ogName
        if ($user->isAdmin) //add click listener if admin
            echo " changeName";
        echo "\">$rows->fileOgName</div>"; //print ogName
        if ($user->isAdmin) //print replace input element
            echo "<div class=\"changeNameInput\"><input type=\"text\" value=\"$rows->fileOgName\"><button class=\"updateName\">Update</button></div></td>"; //print input
        echo "<td class=\"listUploader\">";
        if ($rows->username == null)
            $rows->username = "deleted<br>user[$rows->fileUserId]";
        echo "<a href=\"$domain/list?q=u%3A$rows->fileUserId\" target=\"_top\">$rows->username</a>";
        echo "</td>";
        echo "<td class=\"listUploadDate\">" . substr($rows->fCreated, 0, 10) . "</td>";
        echo "<td>";
        if ($user->isAdmin || $_SESSION["userId"] == $rows->fileUserId)
            echo "<button class=\"deleteButton\">X</button>";
        echo "</td></tr>";
    }
    if ($result->num_rows == 0)
        echo "<tr><th colspan=\"7\">No Results</th></tr>";
    echo "</table>";
    echo $navButtons;
    echo '</div>';

    function rating($rating, $u)
    {
        if ($rating == "")
            $rating = 0;
        if ($u != "") {
            $u = $u . "star userStar";
        } else {
            $u = "star";
        }
        if ($rating - floor($rating) == 0.5)
            $rating = floor($rating);
        $rating = (int) $rating; // 5.000 -> 5
        return '<img class="' . $u . '" src="img/' . $rating . '.png">';
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
</body>

</html>