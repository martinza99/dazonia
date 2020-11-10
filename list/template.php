<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../include/head.php"); ?>
    <title>File List</title>
    <link rel="stylesheet" type="text/css" media="screen" href="/static/list/list.css" />
    <script src="/static/list/list.js"></script>
</head>

<body>
    <?php include("../include/nav.php"); ?>
    <?php if (isset($bg)) : ?>
        <img class="background" src="<?= $CDN ?>/bg/<?= $bg ?>" />
    <?php endif; ?>


    <aside class="pageButtons">
        <a href="/list?p=<?= ($p - 1) ?>&q=<?= $q ?>"><button>←</button></a>
        <span><?= $p ?></span>
        <a href="/list?p=<?= ($p + 1) ?>&q=<?= $q ?>"><button>→</button></a>
        <button onClick="tagSelect(this)">add tags</button>
    </aside>
    <main class=" listTableDiv">
        <table class="listTable">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Rating</th>
                    <th>Filename</th>
                    <th>Title</th>
                    <th class="listUploader">Uploader</th>
                    <th class="listUploadDate">Upload Date</th>
                    <th>
                        <button class="deleteAllButton">X</button>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php while ($file = $sql->fetchObject()) : ?>
                    <tr id="<?= $file->filename ?>">
                        <td>
                            <a href="/view/<?= $file->filename ?>?q=<?= $q ?>">
                                <div class="picsList">
                                    <img class="thumb" src="<?= $CDN ?>/thumbnails/<?= $file->filename ?>" alt="<?= $file->filename ?>">
                                </div>
                            </a>
                        </td>
                        <td>
                            <div class="starContainer">
                                <?php if ($file->avgrating) : ?>
                                    <img class="starView" src="/static/list/img/<?= $file->avgrating ?>.png" alt="<?= $file->avgrating ?>">
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <a href="<?= $CDN ?>/files/<?= $file->filename ?>"><?= $file->filename ?></a>
                        </td>
                        <td class="og">
                            <div class="fileName">
                                <?= $file->ogName ?>
                            </div>
                        </td>
                        <td class="listUploader">
                            <?php if ($file->username == null) : ?>
                                deleted<br>user
                            <?php else : ?>
                                <?= $file->username ?>
                            <?php endif ?>
                        </td>
                        <td class="listUploadDate"><?= $file->created ?></td>
                        <td>
                            <button class="deleteButton">X</button>
                        </td>
                    </tr>
                <?php endwhile;
                if ($sql->rowCount() == 0) : ?>
                    <tr>
                        <th colspan="7">No Results</th>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
    <aside class="pageButtons">
        <a href="/list?p=<?= ($p - 1) ?>&q=<?= $q ?>"><button>←</button></a>
        <span><?= $p ?></span>
        <a href="/list?p=<?= ($p + 1) ?>&q=<?= $q ?>"><button>→</button></a>
    </aside>

</body>

</html>