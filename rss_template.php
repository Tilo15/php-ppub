<?php

function index_start() {
?>
<?xml version='1.0' encoding='UTF-8'?>
<rss version='2.0'>
    <channel> 

        <title><?php echo(SITE_NAME);?></title>
        <link><?php echo(SITE_URL);?></link>
        <description><?php echo(SITE_TAGLINE);?></description>
        <language><?php echo(SITE_LANGUAGE);?></language>
<?php
}

function index_listing($ppub, $url) {
?>
        <item>
            <title><?php echo(htmlentities($ppub->metadata["title"]));?></title>
            <link><?php echo(SITE_URL . "/" . $url)?></link>
            <description><?php echo(htmlentities($ppub->metadata["description"]));?></description>
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