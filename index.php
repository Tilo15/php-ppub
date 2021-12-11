<?php
include("config.php");
include("ppub.php");

define("INDEX_TYPE_MAIN", 0);
define("INDEX_TYPE_TAG", 1);
define("INDEX_TYPE_SEARCH", 2);

function get_ppub_file_list() {
    if(USE_PPIX) {
        include_once("ppix.php");
        $ppix = new Ppix(fopen(PUBLICATION_DIR . "/lib.ppix", 'rb'));
        if(isset($_GET["q"])) {
            $ids = $ppix->do_search(strtolower(str_replace("/", "", $_GET["q"])));
            $list = array();
            for ($i=0; $i < count($ids); $i++) { 
                $list[$i] = $ppix->get_publication_by_id($ids[$i]);
            }
            return $list;

        } else if(isset($_GET["tag"])) {
            $tag = str_replace("/", "", $_GET["tag"]);
            $tags = $ppix->get_tags();
            $col = $tags[$tag];
            if($col === null) {
                return array();
            }
            $ids = $ppix->get_collection_by_id($col);
            $list = array();
            for ($i=0; $i < count($ids); $i++) { 
                $list[$i] = $ppix->get_publication_by_id($ids[$i]);
            }
            return $list;

        } else {
            $count = $ppix->get_publication_count();
            $list = array();
            for ($i=0; $i < $count; $i++) { 
                $list[$i] = $ppix->get_publication_by_id($i);
            }
            return $list;
        }
    }
    else {
        $dir = opendir(PUBLICATION_DIR . "/");
        $list = array();
        while($file = readdir($dir)){
            if ($file != '.' and $file != '..' and $file != "lib.ppix"){
                $ctime = filectime(PUBLICATION_DIR . "/" . $file) . ',' . $file;
                $list[$ctime] = $file;
            }
        }
        closedir($dir);
        krsort($list);
        return $list;
    }
}

function get_tag_list() {
    if(USE_PPIX) {
        include_once("ppix.php");
        $ppix = new Ppix(fopen(PUBLICATION_DIR . "/lib.ppix", 'rb'));
        return array_keys($ppix->get_tags());
    }
    else {
        return array();
    }
}

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

    $index_type = INDEX_TYPE_MAIN;
    $index_arg = null;

    if(isset($_GET["q"])) {
        $index_type = INDEX_TYPE_SEARCH;
        $index_arg = str_replace("/", "", $_GET["q"]);
    }

    if(isset($_GET["tag"])) {
        $index_type = INDEX_TYPE_TAG;
        $index_arg = str_replace("/", "", $_GET["tag"]);
    }

    if($index_type == INDEX_TYPE_MAIN) {
        $index_arg = get_tag_list();
    }
    
    index_start($index_type, $index_arg);
    $list = get_ppub_file_list();
    
    foreach ($list as $file) {
        $ppub = new Ppub();
        $ppub->read_file(PUBLICATION_DIR . "/".$file);
        index_listing($ppub, $file);
    }

    if(count($list) == 0) {
        index_no_content($index_type, $index_arg);
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
    content_start($ppub, $file_name);
    content_html($pd->text($ppub->read_asset($asset)));
    content_end($ppub);
}
else {
    header("content-type: " . $asset->mimetype);
    echo($ppub->read_asset($asset));
}

?>