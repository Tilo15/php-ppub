<?php
class Asset {
    public $path = "";
    public $mimetype = "";
    public $start_location = 0;
    public $end_location = 0;
    public $flags = array();

    public function __construct($path, $mimetype, $slocation, $elocation, $flags) {
        $this->path = $path;
        $this->mimetype = $mimetype;
        $this->start_location = $slocation;
        $this->end_location = $elocation;
        $this->flags = $flags;
    }
}


class Ppub {
    public $metadata = array();
    public $asset_index = array();
    public $asset_list = array();
    public $default_asset = null;

    private $handle = null;
    private $blob_start = 0;

    public function read_file($file_path) {
        $handle = fopen($file_path, "rb");
        if(fread($handle, 5) != "ppub\n"){
            throw new Exception("File did not start with PPUB magic number", 1);
        }

        $head_size_string = "";
        $next_char = '';
        while($next_char != "\n"){
            $head_size_string .= $next_char;
            $next_char = fread($handle, 1);
        }
        
        $index_length = intval($head_size_string);
        $index_data = fread($handle, $index_length);

        $this->handle = $handle;
        $this->blob_start = strlen($head_size_string) + $index_length + 6; 
        $this->build_asset_list($index_data);
        $this->build_metadata($this->read_asset($this->asset_list[0]));
    }

    public function read_asset($asset) {
        $start_location = $asset->start_location + $this->blob_start;
        $length = $asset->end_location - $asset->start_location;
        fseek($this->handle, $start_location);
        $data = fread($this->handle, $length);
        if(in_array("gzip", $asset->flags)) {
            $data = gzdecode($data);
        }
        return $data;
    }

    public function get_asset_size($asset) {
        $start_location = $asset->start_location + $this->blob_start;
        $length = $asset->end_location - $asset->start_location;
        return $length;
    }

    public function can_stream_asset($asset) {
        return !in_array("gzip", $asset->flags);
    }

    public function stream_asset($asset, $start = 0, $end = -1) {
        $start_location = $asset->start_location + $this->blob_start + $start;
        $length = ($end >= 0) ? $asset->end_location - $asset->start_location : $end;
        fseek($this->handle, $start_location);
        $pos = 0;
        while($pos < $length) {
            $chunksize = min(1024 * 1024, $length - $pos);
            echo(fread($this->handle, $chunksize));
            flush();
            $pos += $chunksize;
        }
    }

    private function build_asset_list($data) {
        $asset_list = array();
        $lines = explode("\n", $data);
        for ($i=0; $i < sizeof($lines); $i++) { 
            if(trim($lines[$i]) == ''){
                continue;
            }
            $keyval = explode(": ", $lines[$i], 2);
            $vals = explode(" ", $keyval[1]);
            
            $asset = new Asset($keyval[0], $vals[0], intval($vals[1]), intval($vals[2]), array_slice($vals, 3));
            array_push($asset_list, $asset);
            $this->asset_index[$asset->path] = $asset;
        }

        $this->asset_list = $asset_list;
    }

    private function build_metadata($data) {
        $data_list = array();
        $lines = explode("\n", $data);
        for ($i=0; $i < sizeof($lines); $i++) { 
            if(trim($lines[$i]) == ''){
                continue;
            }
            $keyval = explode(": ", $lines[$i], 2);            
            $data_list[$keyval[0]] = $keyval[1];
        }
        $this->metadata = $data_list;
    }
}


?>