<?php
class VideoEntry {
    public $type = null;
    public $label = null;
    public $filename = null;
    public $metadata = array();

    public function from_string($str) {
        $parts = explode(":", $str, 2); 
        $data = explode(",", $parts[1], 3);
        $this->type = trim($parts[0]);
        $this->label = trim($data[0]);
        $this->filename = trim($data[1]);

        $meta = trim($data[2]);
        $properties = explode("\";", $meta);
        foreach ($properties as $property) {
            if($property == ""){
                continue;
            }
            $entry = explode("=\"", $property, 2);
            $this->metadata[trim($entry[0])] = trim($entry[1]);
        }
    }
}


class Ppvm {
    public $entries = array();
    public $metadata = array();

    public function from_string($str) {
        $parts = explode("\n\n", $str, 2);

        $lines = explode("\n", $parts[0]);
        foreach ($lines as $line) {
            if($line != "PPVM"){
                $entry = explode(":", $line, 2);
                $this->metadata[$entry[0]] = trim($entry[1]);
            }
        }

        $lines = explode("\n", $parts[1]);
        foreach ($lines as $line) {
            if($line == "" or $line[0] == "#") {
                continue;
            }
            $entry = new VideoEntry();
            $entry->from_string($line);
            array_push($this->entries, $entry);
        }
    }
}


?>