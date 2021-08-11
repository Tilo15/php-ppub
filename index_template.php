<?php

function index_start() {

    ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo(PUBLICATION_NAME)?> Index - <?php echo(SITE_NAME);?></title>
        <link rel="stylesheet" href="/vanilla.css">
        <link rel="alternate" type="application/rss+xml" title="<?php echo(SITE_NAME);?> RSS Feed" href="/feed.rss" />
    </head>
    <body>
        <h1><?php echo(SITE_NAME);?></h1>
        <h2><?php echo(PUBLICATION_NAME)?> Index</h2>
        <dl>
    <?php
}

function index_listing($ppub, $url) {
    ?>
            <dt><a href="<?php echo($url);?>"><?php echo(htmlentities($ppub->metadata["title"]));?></a></dt>
            <dd><?php echo(htmlentities($ppub->metadata["description"]));?></dd>
    <?php
}

function index_end() {
    ?>
    </dl>
    </body>
</html>
    <?php
}

?>