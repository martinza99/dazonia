<?php
echo "
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css\">
<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js\"></script>
<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js\"></script>
<link rel=\"shortcut icon\" href=\"/favicon.png\" type=\"image/x-icon\">
<script src=\"/main.js?$hash\"></script>
<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"/main.css?$hash\" />
</head>";
exec('git rev-parse --verify HEAD', $output);
echo "<!-- $output[0] -->";
$filter="";
if(isset($_GET["q"]))
    $filter = $_GET["q"];
echo "
<body>
<nav class=\"navbar navbar-inverse\">
<div class=\"container-fluid\">
    <div class=\"navbar-header\">
        <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\".navbar-collapse\">
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
        </button>
        <a class=\"navbar-brand\" href=\"$domain\" target=\"_top\"><img src=\"/logo.png\" style=\"margin-top: -12px;\"></a>
    </div>
    <div class=\" navbar-collapse collapse\">
        <ul class=\"nav navbar-nav\">
            <li><a href=\"$domain/list\" target=\"_top\">List View</a></li>
            <li><a href=\"$domain/tags\" target=\"_top\">Tags</a></li>
            <li><a href=\"$domain/login/users.php\" target=\"_top\">Users</a></li>
            <li class=\"dropdown\">
                <a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"$domain#\">Mode
                <span class=\"caret\"></span></a>
                <ul class=\"dropdown-menu dropdown-menu-inverse\">
                <li><a href=\"$domain/list?q=tag%3Asafe\" target=\"_top\">Peace</a></li>
                <li><a href=\"$domain/list?q=tag%3Aecchi\" target=\"_top\">Ecchi</a></li>
                <li><a href=\"$domain/list?q=tag%3Ahentai\" target=\"_top\">Lewd</a></li>
                <li><a href=\"$domain/list?q=tag%3Ansfw\" target=\"_top\">Porn</a></li>
                </ul>
            </li>
            <li class=\"dropdown\">
                <a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"$domain#\">Browse
                <span class=\"caret\"></span></a>
                <ul class=\"dropdown-menu dropdown-menu-inverse\">
                <li><a href=\"$domain/list/?q=tag%3Aprofile_picture\" target=\"_top\">Profile Pictures</a></li>
                <li><a href=\"$domain/list?q=tag%3Aemote\" target=\"_top\">Emotes</a></li>
                <li><a href=\"$domain/list?q=tag%3Ameme\" target=\"_top\">Memes</a></li>
                <li><a href=\"$domain/list?q=r%3A10\" target=\"_top\">Top</a></li>
                </ul>
            </li>
        </ul>
        <a href=\"$domain/list/?q=\" class=\"searchLink\" target=\"_top\" hidden></a>
        <form class=\"navbar-form navbar-left\" action=\"$domain/list/search.php\" method=\"GET\" autocomplete=\"off\" onsubmit=\"searchFormSubmit();\">
        <div class=\"input-group\">
        <input type=\"search\" class=\"form-control disableHotkeys searchInput\" placeholder=\"Search\" name=\"q\" style=\"background-color: #04013c; border-color: #1e1b7b;\" value=\"$filter\">
            <div class=\"input-group-btn\">
            <button class=\"btn btn-default\" type=\"submit\" style=\"height: 34px; background-color: #131a63; border-color: #1e1b7b;\">
                <i class=\"glyphicon glyphicon-search\" style=\"color: #c5c0c0;\"></i>
            </button>
            </div>

        </div>
        </form>
        <ul class=\"nav navbar-nav navbar-right\">
            <li><a href=\"$domain/upload\" target=\"_top\"><span class=\"glyphicon glyphicon-heart-empty\"></span> Upload</a></li>
        </ul>
    </div>
</div>
</nav>
<div class=\"navbarMargin\"></div>";
?>