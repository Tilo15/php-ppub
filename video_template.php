<?php

function content_start($ppub, $path, $video) {
    $metadata = $ppub->metadata;
    ?>

<!DOCTYPE html>
<html lang="<?php echo($metadata["language"] ?? SITE_LANGUAGE);?>">
    <head>
        <meta charset="utf-8">
        <title><?php echo(htmlentities($metadata["title"]));?> - <?php echo(SITE_NAME);?></title>
        <meta name="description" content="<?php echo(htmlentities($metadata["description"]));?>">
        <meta name="author" content="<?php echo(htmlentities($metadata["author"]));?>">
        <link rel="stylesheet" href="<?php echo(SITE_URL);?>/vanilla.css">
        <link rel="alternate" type="application/x-ppub" title="<?php echo(htmlentities($metadata["title"]));?> (as PPUB)" href="?download=true" />
        <style type="text/css">
            .player {
                position: relative;
                height: 0;
                padding-bottom: 56.25%;
                background: #2d2d2d;
            }
            .player iframe{
                position: absolute;
                top: 0; left: 0;
                width: 100%;
                height: 100%;
            }
        </style>
    </head>
    <body>
    <header>
        <h1>
            <a style="color: var(--text-color); text-decoration: none; display: inline-block;" href="<?php echo(SITE_URL);?>"><?php echo(SITE_NAME);?></a>
            <!-- <small style="font-weight: light; margin: 0px 5px 0px 5px;" aria-label="<?php echo(PUBLICATION_NAME);?> title:">/</small> -->
            <!-- <small><a style="color: var(--text-color); text-decoration: none; display: inline-block;" href="<?php echo(SITE_URL);?>/<?php echo($path);?>"><?php echo(htmlentities($metadata["title"]));?></a></small> -->
        </h1>
    </header>

    <?php

    include("video_player.php");
    generate_embed($path, $video);
}

function content_html($content) {
    echo $content;
}

function content_end($ppub) {
    ?>
    <footer>
        <hr>
        <p><strong><?php echo(htmlentities($ppub->metadata["title"]));?></strong>
        <?php if($ppub->metadata["author"] != null) {
            preg_match("/^([^<]*(?= *<|$))<*([^>]*)>*/", $ppub->metadata["author"], $author);
         ?>
        <br/><?php echo(PUBLICATION_NAME);?> authored by <?php
            if(isset($author[2]) && $author[2] != '') {
                echo("<a href=\"mailto:".$author[2]."\">");
                echo(htmlentities(trim($author[1])));
                echo("</a>");
            } else {
                echo(htmlentities($ppub->metadata["author"]));
            }
        ?>.
        <?php } if ($ppub->metadata["tags"] != null and USE_PPIX) { ?>
        <br/>Tagged with: 
        <?php
            foreach(explode(" ", $ppub->metadata["tags"]) as $tag) {
                ?>
                <a href="<?php echo(SITE_URL);?>/?tag=<?php echo(urlencode($tag));?>"><?php echo(htmlentities($tag));?></a>
                <?php
            }
        ?>
        <?php } if ($ppub->metadata["date"] != null) { ?>
        <br/>Last updated on <?php echo(htmlentities((new DateTime($ppub->metadata["date"]))->format(DATE_FORMAT)));?>.
        <br/><?php } if ($ppub->metadata["copyright"] != null) { ?>
        <?php echo($ppub->metadata["copyright"]);?>
        <?php } if ($ppub->metadata["licence"] != null) { ?>
        <a href="<?php echo($ppub->metadata["licence"]);?>">See Licence</a>
        <?php } ?></p>
        <p><a href="<?php echo(SITE_URL);?>/">Return to <?php echo(PUBLICATION_NAME);?> Index</a> | <a href="<?php echo(SITE_URL);?>/feed.rss">Subscribe to <?php echo(SITE_NAME);?> RSS</a> | <a href="?download=true">Download <?php echo(PUBLICATION_NAME);?> PPUB</a>
        <br/><small>Powered by <a href="https://github.com/Tilo15/php-ppub">php-ppub</a> and <a href="https://parsedown.org">Parsedown</a>, styled with <a href="https://vanillacss.com/">Vanilla CSS</a>.</small></p>
    </footer>
    </body>
</html>
    <?php
}