<?php

function index_start($type, $arg) {
?>
<?xml version='1.0' encoding='UTF-8'?>
<rss version='2.0'>
    <channel> 

        <title><?php echo(SITE_NAME);?></title>
        <link><?php echo(SITE_URL);?></link>
        <description><?php echo(SITE_TAGLINE);?></description>
        <language><?php echo(SITE_LANGUAGE);?></language>
        <generator>php-ppub</generator>
<?php
}

function index_listing($ppub, $url) {
?>
        <item>
            <title><?php echo(htmlentities($ppub->metadata["title"]));?></title>
            <link><?php echo(SITE_URL . "/" . $url)?></link>
            <description><?php echo(htmlentities($ppub->metadata["description"]));?></description>
            <?php if($ppub->metadata["date"] != null) { ?>
            <pubDate><?php echo(htmlentities((new DateTime($ppub->metadata["date"]))->format("D, d M Y H:i:s O")));?></pubDate>
            <?php } ?>
        </item>
<?php
}

function index_end() {
?>
    </channel>
</rss>
<?php
}

?>