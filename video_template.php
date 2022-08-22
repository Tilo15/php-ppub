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
        <script type="text/javascript" src="<?php echo(SITE_URL);?>/pvpd_player.js"></script>
        <style type="text/css">
            video {
                display: block;
                height: auto; 
                max-width: 100%; 
            }
            .additional-contols {
                background: var(--text-color);
                padding: 6px;
                font-size: 12px;
                color: #ffffff;
            }
            .additional-contols a {
                color: #ffffff;
            }
            .additional-control {
                display: inline-block;
                margin-right: 8px;
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

    <video controls id="player" poster="<?php echo($video->files["poster"]);?>">
    </video>
    <div class="additional-contols">
        <div class="additional-control quality">
            <label for="quality">Playback Quality: </label>
            <select name="quality" id="quality-selector" onchange="qualitySelected()">  
            </select>
        </div>
        <div class="additional-control download">
            <a href="<?php echo($video->files["master"]);?>" download>Download Full Quality Video</a>
        </div>
    </div>

    <script type="text/javascript">
    setup_playback({
        entries: [
<?php
            foreach ($video->entries as $entry) {
                $entry_asset = $ppub->asset_index[$entry->filename];
                echo("            { mimetypeWithCodec: \"" . $entry_asset->mimetype . " codecs=\\\"" . $entry->codecs . "\\\"\", relativePath: \"" . $entry->filename . "\", label: \"" . $entry->label . "\" },\n");
            }
?>
        ]
    });
    </script>

    <?php
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

?>