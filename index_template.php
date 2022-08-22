<?php

function index_start($index_type, $arg) {

    ?>

<!DOCTYPE html>
<html lang="<?php echo(SITE_LANGUAGE);?>">
    <head>
        <meta charset="utf-8">
        <title><?php echo(PUBLICATION_NAME)?> Index - <?php echo(SITE_NAME);?></title>
        <link rel="stylesheet" href="<?php echo(SITE_URL);?>/vanilla.css">
        <link rel="alternate" type="application/rss+xml" title="<?php echo(SITE_NAME);?> RSS Feed" href="<?php echo(SITE_URL);?>/feed.rss" />
        <style type="text/css">
            aside {
                float: right;
                border: 1px solid var(--text-color);
                padding: 16px;
                max-width: 100vw;
                width: 316px;
                margin: 0px 0px 15px 15px;

            }
            ul.tags {
                list-style: none;
                padding-left: 0px;
                margin: 0px;
            }
            ul.tags li {
                display: inline;
            }
            details summary {
                cursor: pointer;
            }
            dd img {
                width: 33%;
                margin-top: 0px;
                margin-bottom: 15px;
            }
            @media screen and (max-width: 75ch) {
                aside {
                    float: none;
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>

    <header>
        <h1><a style="color: var(--text-color); text-decoration: none;" href="<?php echo(SITE_URL);?>"><?php echo(SITE_NAME);?></a></h1>
    </header>

    <?php
    if(USE_PPIX and $index_type == INDEX_TYPE_MAIN) {
        ?>
        <aside>
            <form action="./" >
                <label for="q">Search this site:</label><br>
                <input style="width: 100%;" type="text" id="q" name="q" placeholder="Search...">
            </form> 
            <?php
            if(count($arg) > 0) { ?>
            <details>
                <summary>Browse by tags</summary>
                <ul class="tags">
                <?php
                foreach ($arg as $tag) {
                    ?>
                    <li><a href="?tag=<?php echo(urlencode($tag));?>"><?php echo(htmlentities($tag));?></a></li>
                    <?php
                }
                ?>
                </ul>
            </details>
            <?php } ?>
        </aside>
        <?php
    }

    if($index_type == INDEX_TYPE_MAIN) {
        ?>
         <h2><?php echo(PUBLICATION_NAME)?> Index</h2>
        <?php
    }

    if($index_type == INDEX_TYPE_TAG) {
        ?>
        <h2>Tagged with <em><?php echo(htmlentities($arg));?></em></h2>
       <?php
    }

    if($index_type == INDEX_TYPE_SEARCH) {
        ?>
        <form action="./">
            <label for="q">Search query:</label><br>
            <input style="width: 100%;" type="text" id="q" name="q" value="<?php echo(htmlentities($arg));?>">
        </form> 
        <!-- <hr/> -->
        <!-- <h2>Search Results</h2> -->
       <?php
    }

    ?>
        <dl>
    <?php
}

function index_no_content($index_type, $arg) {
    if($index_type == INDEX_TYPE_SEARCH) {
        echo("<p>Nothing found for search query &quot;" . htmlentities($arg) . "&quot.</p>");
    }
}

function index_listing($ppub, $url) {
    ?>
            <dt><a href="<?php echo($url);?>"><?php echo(htmlentities($ppub->metadata["title"]));?></a></dt>
            <dd>
                <?php echo(htmlentities($ppub->metadata["description"]));?>
                <?php 
                if($ppub->metadata["poster"] != null) {
                    echo("<img src=\"" . $url . "/" . $ppub->metadata["poster"] . "\" alt='' />");
                }
                ?>
            </dd>
    <?php
}

function index_end() {
    ?>
    </dl>
    <footer>
        <hr>
        <p><strong><?php echo($_SERVER['SERVER_NAME'])?></strong> | <a href="<?php echo(SITE_URL);?>/feed.rss">Subscribe to <?php echo(SITE_NAME);?> RSS</a>
        <br/><small>Powered by <a href="https://github.com/Tilo15/php-ppub">php-ppub</a> and <a href="https://parsedown.org">Parsedown</a>, styled with <a href="https://vanillacss.com/">Vanilla CSS</a>.</small></p>
    </footer>
    </body>
</html>
    <?php
}

?>