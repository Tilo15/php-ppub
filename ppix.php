<?php

class Ppix {

    private $handle;

    private $locations;

    public function __construct($handle) {
        $this->handle = $handle;
        fseek($handle, 0);
        if(fread($handle, 5) != "PPIX\x00") {
            throw new Exception("File did not start with PPIX magic number", 1);
        }

        $this->locations = unpack("Vpub/Vcol/Vtag/Vtre", fread($handle, 16));
    }

    public function get_publication_count() {
        fseek($this->handle, $this->locations["pub"]);
        return unpack("V", fread($this->handle, 4))[1];
    }

    public function get_publication_by_id($id) {
        $location = $this->locations["pub"] + 4 + ($id * 6);
        fseek($this->handle, $location);
        $string_info = unpack("Vloc/vlen", fread($this->handle, 6));
        fseek($this->handle, $string_info["loc"]);
        return fread($this->handle, $string_info["len"]);
    }

    public function get_collection_by_id($id) {
        $location = $this->locations["col"] + ($id * 6);
        fseek($this->handle, $location);
        $collection_info = unpack("Vloc/vcount", fread($this->handle, 6));
        fseek($this->handle, $collection_info["loc"]);
        $values = array();
        for ($i=0; $i < $collection_info["count"]; $i++) { 
            $values[$i] = unpack("V", fread($this->handle, 4))[1];
        }
        return $values;
    }

    public function get_tags_count() {
        fseek($this->handle, $this->locations["tag"]);
        return unpack("v", fread($this->handle, 2))[1];
    }

    public function get_tags() {
        $count = $this->get_tags_count();
        $tags = array();
        for ($i=0; $i < $count; $i++) { 
            $tag_data = unpack("Cstrlen/Vcolid", fread($this->handle, 5));
            $tag = fread($this->handle, $tag_data["strlen"]);
            $tags[$tag] = $tag_data["colid"];
        }
        return $tags;
    }

    public function find_word_matches($word) {
        $binarr = $this->string_to_bin_arr($word);
        $node = $this->read_tree_node($this->locations["tre"]);

        foreach ($binarr as $bit) {
            if(!$bit and $node["zero"] != 0) {
                $node = $this->read_tree_node($node["zero"]);
            }
            else if($bit and $node["one"] != 0) {
                $node = $this->read_tree_node($node["one"]);
            }
            else {
                return null;
            }
        }

        if($node["has"] == 255) {
            return $node["col"];
        }
        return null;
    }

    private function find_partial_matches($bin_word) {
        $binarr = $this->string_to_bin_arr($word);
        $node = $this->read_tree_node($this->locations["tre"]);
        $built_key = array();

        foreach ($binarr as $bit) {
            if(!$bit and $node["zero"] != 0) {
                array_push($built_key, $bit);
                $node = $this->read_tree_node($node["zero"]);
            }
            else if($bit and $node["one"] != 0) {
                array_push($built_key, $bit);
                $node = $this->read_tree_node($node["one"]);
            }
        }

        if($node["has"] == 255) {
            return $node["col"];
        }
        return null;
    }

    private function get_subkeys($key, $node) {
        $subkeys = array();
        if($node["has"] == 255) {
            array_push($subkeys, $key);
        }
        if($node["one"] != 0) {
            $nkey = array_merge($key);
            array_push($nkey, true);
            array_merge($subkeys, $this->get_subkeys($nkey, $node));
        }
        if($node["zero"] != 0) {
            $nkey = array_merge($key);
            array_push($nkey, false);
            array_merge($subkeys, $this->get_subkeys($nkey, $node));
        }
        return $subkeys;
    }

    private function read_tree_node($location) {
        fseek($this->handle, $location);
        $data = unpack("Vzero/Chas/Vcol/Vone", fread($this->handle, 13));
        return $data;
    }

    private function string_to_bin_arr($string) {
        $data = array();
        $refbits = array(1,2,4,8,16,32,64,128);
        for ($i=0; $i < strlen($string) * 8; $i++) { 
            $char = ord($string[intdiv($i,8)]);
            $ref = $refbits[$i%8];
            $data[$i] = ($char & $ref) == $ref;
        }
        return $data;
    }

    public function do_search($query) {
        $words = explode(" ", $query);
        $results = null;
        foreach($words as $word) {
            $col = $this->find_word_matches($word);
            if($col == null){
                return array();
            }
            $col = $this->get_collection_by_id($col);
            if($results == null){
                $results = $col;
            }
            else {
                $results = array_intersect($results, $col);
            }
            if(count($results) == 0) {
                return $results;
            }            
        }
        return array_values($results);
    }
}


?>