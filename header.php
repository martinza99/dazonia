<?php
require_once "login/sql.php";
echo "
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css\">
<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js\"></script>
<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js\"></script>
<link rel=\"shortcut icon\" href=\"/favicon.png\" type=\"image/x-icon\">
</head>";
exec('git rev-parse --verify HEAD', $output);
echo "<!-- $output[0] -->";
echo "
<body>

<nav class=\"navbar navbar-inverse\">
<div class=\"container-fluid\">
    <div class=\"navbar-header\">
        <a class=\"navbar-brand\" href=\"$domain\" target=\"_top\"><img src=\"/logo.png\" style=\"margin-top: -12px;\"></a>
    </div>
    <ul class=\"nav navbar-nav\">
        <li><a href=\"$domain/list\" target=\"_top\">List View</a></li>
        <li><a href=\"#\" target=\"_top\">Prof Pics</a></li>
        <li><a href=\"#\" target=\"_top\">Safe Mode</a></li>
    </ul>
    <form class=\"navbar-form navbar-left\" action=\"$domain/list/\" method=\"GET\" autocomplete=\"off\">
    <div class=\"input-group\">
        <input type=\"text\" class=\"form-control\" placeholder=\"Search\" name=\"q\" style=\"background-color: #04013c; border-color: #1e1b7b;\">
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
</nav>";
?>