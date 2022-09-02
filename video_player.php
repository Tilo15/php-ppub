<?php

function ppvm_player($ppub, $path, $video) {
    $metadata = $ppub->metadata;
    $short_title = $metadata["title"];
    if(strlen($short_title) > 30) {
        $short_title = substr($short_title, 0, 30);
        $short_title .= "…";
    }


    ?>
<!DOCTYPE html>
<html lang="<?php echo($metadata["language"] ?? SITE_LANGUAGE);?>">
    <head>
        <meta charset="utf-8">
        <title><?php echo(htmlentities($metadata["title"]));?> - <?php echo(SITE_NAME);?></title>
        <meta name="description" content="<?php echo(htmlentities($metadata["description"]));?>">
        <meta name="author" content="<?php echo(htmlentities($metadata["author"]));?>">
        <link rel="alternate" type="application/x-ppub" title="<?php echo(htmlentities($metadata["title"]));?> (as PPUB)" href="?download=true" />
        <script type="text/javascript" src="<?php echo(SITE_URL);?>/ppvm_player.js"></script>
        <style type="text/css">
            body {
                margin: 0px;
                background-color: #000;
            }

            video {
                display: block;
                height: auto; 
                width: 100vw;
                height: 100vh;
            }

            .additional-contols {
                background: rgba(45, 45, 45, 0.8);
                padding: 6px;
                color: #ffffff;
                height: 24px;
                position: absolute;
                top: -36px;
                left: 0;
                right: 0;
                opacity: 0;
                transition: 0.2s all;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }

            body:hover .additional-contols.javascript, .paused.javascript, .additional-contols.noscript {
                top: 0px;
                opacity: 1;
            }

            body, input {
                font: 1rem -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto, Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji", "Segoe UI Symbol";
            }

            dialog {
                background: rgba(45, 45, 45, 1);
                color: #ffffff;
                border: 1px solid aliceblue;
                max-width: 75%;
            }

            dialog p{
                margin-top: 2px;
            }

            dialog::backdrop {
                background: rgba(45, 45, 45, 0.8);
            }

            dialog .contents {
                margin: -15px;
                margin-bottom: 0px;
                padding: 15px;
                padding-bottom: 0px;
                max-height: calc(75vh - 55px);
                overflow-y: auto;
            }

            dialog .controls {
                border-top: 1px solid dimgrey;
                padding-top: 10px;
            }

            a {
                color: skyblue;
            }
            a:visited {
                color: aquamarine;
            }

            .additional-contols a {
                color: #ffffff;
            }
            .additional-control {
                display: inline-block;
            }

            .filler {
                flex: 1;
            }

            .site a {
                text-decoration: none;
                color: #ffffff;
                margin: 8px;
                height: 18px;
                line-height: 20px;
            }
            .site a:hover {
                color: skyblue;
            }

            .additional-control button, .additional-control select {
                background: rgba(45, 45, 45, 0);
                border: 1px solid rgba(45, 45, 45, 0);
                color: #ffffff;
                cursor: pointer;
                text-decoration: underline;
                text-align: right;
                margin-top: 0px;
                margin-bottom: 2px;
                margin-left: 2px;
                margin-right: 2px;
                padding-left: 6px;
                padding-right: 6px;
                padding-bottom: 3px;
                padding-top: 1px;
                height: 23px;
                -webkit-appearance: none;
            }
            .additional-control button:hover, .additional-control select:hover {
                color: skyblue;
            }
            .additional-control button:focus, .additional-control select:focus  {
                color: skyblue;
            }


            .additional-control select option {
                color: #000000;
            }

            summary {
                cursor: pointer;
            }

            #share-modal textarea, pre {
                width: 500px;
                background: rgb(30, 30, 30);
                color: #fff;
                border: none;
                padding: 8px;
                margin-top: 8px;
                margin-bottom: 18px;
                border-radius: 4px;
                overflow: scroll;
                white-space: pre;
            }

            #download-modal li {
                margin-bottom: 8px;
            }


        </style>
    </head>
    <body>

        <video controls id="player" poster="<?php echo($video->metadata["poster"]);?>" preload="metadata" src="<?php echo($video->metadata["master"]);?>">
        </video>

        <div id="no-script" class="additional-contols noscript">
            <div class="additional-control site">
                <a href="<?php echo(SITE_URL);?>/<?php echo($_GET["ppub"]);?>/<?php echo($_GET["asset"]);?>" target="_blank" title="<?php echo(htmlentities($metadata["title"]));?>"><strong><?php echo(htmlentities($short_title));?></strong></a>
            </div>
            <div class="additional-control noscript">
                For the best experience, please enable JavaScript.
            </div>
        </div>

        <div id="controls" class="additional-contols paused">
            <div class="additional-control site">
                <a href="<?php echo(SITE_URL);?>/<?php echo($_GET["ppub"]);?>/<?php echo($_GET["asset"]);?>" target="_blank" title="<?php echo(htmlentities($metadata["title"]));?>"><strong><?php echo(htmlentities($short_title));?></strong></a>
                <button onclick="showInfo()">Info</button>
                <button onclick="shareVideo()">Share</button>
                <button onclick="downloadVideo()">Download</button>
            </div>
            <div class="additional-control quality">
                <select name="quality" id="quality-selector" onchange="qualitySelected()">  
                </select>
            </div>
        </div>


        <dialog id="download-modal">
            <form method="dialog">
                <div class="contents">
                <p><strong>Download video</strong><br/>
                <a href="<?php echo($video->metadata["master"]); ?>" download>Download the full quality version of this video</a> or select a different version to suit your needs below.
                </p>

                <details>
                    <summary>Other formats and versions</summary>
                    <ul>
<?php

                    foreach ($video->entries as $entry) {
                        $entry_asset = $ppub->asset_index[$entry->filename];
                        echo("                        <li><a href=\"" . $entry->filename . "\" download>" . htmlentities($entry->label)  . "</a><br/>\n");
                        echo("                            <small>" . round(($entry_asset->end_location - $entry_asset->start_location) / 1000000, 2) . "MB, " . $entry_asset->mimetype . "</small></li>\n");
                    }

?>
                    </ul>
                </details>
                <p>
                <?php if ($ppub->metadata["copyright"] != null) { ?>
                <br/>
                <?php echo($ppub->metadata["copyright"]);?>
                <?php } if ($ppub->metadata["licence"] != null) { ?>
                <br/>
                <a href="<?php echo($ppub->metadata["licence"]);?>">See Licence</a>
                <?php } ?></p>
                </div>
                <div class="controls">
                <button value="cancel">Close</button>
                </div>
            </form>
        </dialog>

        <dialog id="info-modal">
            <form method="dialog">  
                <div class="contents"> 
                <p><strong><?php echo(htmlentities($ppub->metadata["title"]));?></strong>
                <?php if($ppub->metadata["author"] != null) {
                    preg_match("/^([^<]*(?= *<|$))<*([^>]*)>*/", $ppub->metadata["author"], $author);
                ?>
                <br/>By <?php
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
                        <a href="<?php echo(SITE_URL);?>/?tag=<?php echo(urlencode($tag));?>" target="_blank"><?php echo(htmlentities($tag));?></a>
                        <?php
                    }
                ?>
                <?php } if ($ppub->metadata["date"] != null) { ?>
                <br/>Last updated on <?php echo(htmlentities((new DateTime($ppub->metadata["date"]))->format(DATE_FORMAT)));?>.
                <br/><?php } if ($ppub->metadata["copyright"] != null) { ?>
                <?php echo($ppub->metadata["copyright"]);?>
                <?php } if ($ppub->metadata["licence"] != null) { ?>
                <a href="<?php echo($ppub->metadata["licence"]);?>" target="_blank">See Licence</a>
                <?php } ?></p>
                <br/><small>Powered by <a href="https://github.com/Tilo15/php-ppub" target="_blank">php-ppub</a></small></p>
                </div>
                <div class="controls">
                <button value="cancel">Close</button>
                </div>
            </form>
        </dialog>

        <dialog id="share-modal">
            <form method="dialog">
                <div class="contents">
                <p><strong>Share this video</strong><br/>
                With your friends, colleagues, distant family, or strangers on the internet.
                </p>
                <div>
                    <label>Link:</label>
                    <pre><?php echo(SITE_URL);?>/<?php echo($_GET["ppub"]);?>/<?php echo($_GET["asset"]);?></pre>
                </div>
                <div>
                    <label>Embed:</label><br/>
                    <textarea readonly rows="3"><?php generate_embed($path, $video);?></textarea>
                </div>
                </div>
                <div class="controls">
                <button value="cancel" autofocus>Close</button>
                </div>
            </form>
        </dialog>

        <dialog id="unplayable-modal">
            <form method="dialog">
                <div class="contents">
                <p><strong>Unable to playback content</strong><br/>
                Your browser does not appear to support any of the available codecs.
                </p>
                </div>
                <div class="controls">
                <button value="cancel">Close</button>
                </div>
            </form>
        </dialog>


        <script type="text/javascript">
        setup_playback({
            entries: [
<?php
                foreach ($video->entries as $entry) {
                    $entry_asset = $ppub->asset_index[$entry->filename];
                    echo("            { type: \"" . $entry->type . "\",  mimetype: \"" . $entry_asset->mimetype . "\", path: \"" . $entry->filename . "\", label: \"" . $entry->label . "\", metadata: { ");
                    foreach ($entry->metadata as $key => $val) {
                        echo($key . ": \"" . $val . "\", ");
                    }
                    echo("}},\n");
                }
?>
            ]
        });
        </script>
    </body>
</html>

    <?php
}

function generate_embed($path, $video) {
    $percent = 56.25;
    if(isset($video->metadata["ratio"])) {
        $ratio = explode(":", $video->metadata["ratio"]);
        $percent = (min($ratio[0], $ratio[1]) / max($ratio[0], $ratio[1])) * 100;
    }
    ?>
<div class="ppvm-player" style="position: relative; height:0; background: #2d2d2d; padding-bottom: <?php echo($percent);?>%">
    <iframe src="<?php echo(SITE_URL);?>/<?php echo($_GET["ppub"]);?>/<?php echo($_GET["asset"]);?>?embed=true/" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
</div><?php
}

?>