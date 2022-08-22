<?php
class VideoEntry {
    public $filename = null;
    public $label = null;
    public $codecs = null;

    public function from_string($str) {
        $parts = explode(",", $str, 3);
        $this->label = trim($parts[0]);
        $this->filename = trim($parts[1]);
        $this->codecs = trim($parts[2]);
    }
}


class Pvpd {
    public $entries = array();
    public $description = null;
    public $files = array();

    public function from_string($str) {
        $parts = explode("\n\n", $str, 3);
        $this->description = $parts[2];

        $lines = explode("\n", $parts[0]);
        foreach ($lines as $line) {
            if($line != "PVPD"){
                $entry = explode(":", $line, 2);
                $this->files[$entry[0]] = $entry[1];
            }
        }

        $lines = explode("\n", $parts[1]);
        foreach ($lines as $line) {
            $entry = new VideoEntry();
            $entry->from_string($line);
            array_push($this->entries, $entry);
        }
    }
}


?>