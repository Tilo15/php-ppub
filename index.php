<?php
include("config.php");
include("ppub.php");

$file = $_GET["ppub"];
$asset = urldecode($_GET["asset"]);

error_log($_SERVER['REQUEST_URI']);
if($asset == '' and $_SERVER['REQUEST_URI'][-1] != '/') {
    header("location: " . $_SERVER['REQUEST_URI'] . "/");
}


if($file == "" or $file == "/" or $file == "feed.rss") {
    if($file == "feed.rss") {
        header("content-type: application/rss+xml");
        include("rss_template.php");
    }
    else {
        header("content-type: text/html");
        include("index_template.php");
    }
    index_start();
    
    $dir = opendir(PUBLICATION_DIR . "/");
    $list = array();
    while($file = readdir($dir)){
        if ($file != '.' and $file != '..'){
            $ctime = filectime(PUBLICATION_DIR . "/" . $file) . ',' . $file;
            $list[$ctime] = $file;
        }
    }
    closedir($dir);
    krsort($list);
    
    foreach ($list as $file) {
        $ppub = new Ppub();
        $ppub->read_file(PUBLICATION_DIR . "/".$file);
        index_listing($ppub, $file);
    }

    index_end();
    exit();
}

$file = str_replace("/", "", $file);
$file_name = $file;
$file = PUBLICATION_DIR . "/" . $file;
if(!file_exists($file)){
    header('HTTP/1.1 404 Not Found');
    include("404.php");
    exit();
}

$accepts = $_SERVER["HTTP_ACCEPT"];
if(($asset == "" or $asset == "/") and strpos($accepts, "application/x-ppub") !== false or isset($_GET["download"])) {
    header("content-type: application/x-ppub");
    if(isset($_GET["download"])) {
        header("Content-Disposition: attachment; filename=\"".$file_name."\"");
    }
    echo(readfile($file));
    exit();
}

$ppub = new Ppub();
$ppub->read_file($file);

if($asset == "" or $asset == "/") {
    $asset = $ppub->asset_list[1];
}
else {
    $asset = $ppub->asset_index[$asset];
}

if(strpos($accepts, "text/html") !== false && $asset->mimetype == "text/markdown") {
    header("content-type: text/html");
    include("content_template.php");
    include("Parsedown.php");
    $pd = new Parsedown();
    content_start($ppub);
    content_html($pd->text($ppub->read_asset($asset)));
    content_end($ppub);
}
else {
    header("content-type: " . $asset->mimetype);
    echo($ppub->read_asset($asset));
}

?>