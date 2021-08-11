<?php

function content_start($ppub) {
    $metadata = $ppub->metadata;
    ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo(htmlentities($metadata["title"]));?> - <?php echo(SITE_NAME);?></title>
        <meta name="description" content="<?php echo(htmlentities($metadata["description"]));?>">
        <meta name="author" content="<?php echo(htmlentities($metadata["author"]));?>">
        <link rel="stylesheet" href="/vanilla.css">
        <link rel="alternate" type="application/x-ppub" title="<?php echo(htmlentities($metadata["title"]));?> (as PPUB)" href="?download=true" />
    </head>
    <body>
    <?php
}

function content_html($content) {
    echo $content;
}

function content_end($ppub) {
    ?>
    <footer role="contentinfo">
        <hr>
        <p><strong><?php echo(htmlentities($ppub->metadata["title"]));?></strong><br/>Post authored by <?php echo(htmlentities($ppub->metadata["author"]));?>.<br/><a href="/">Return to <?php echo(PUBLICATION_NAME);?> Index</a> | <a href="/feed.rss">Subscribe to <?php echo(SITE_NAME);?> RSS</a> | <a href="?download=true">Download <?php echo(PUBLICATION_NAME);?> PPUB</a></p>
        <p><br/><small>Powered by php-ppub, styled with <a href="https://vanillacss.com/">Vanilla CSS</a>.</small></p>
    </footer>
    </body>
</html>
    <?php
}

?>