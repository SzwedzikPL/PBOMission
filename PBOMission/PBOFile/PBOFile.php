<?php

  define('ENTRY_END_OFFSET', 21);

  class PBOHeaderEntry {
    public string $filename;
    public int $packingMethod;
    public int $originalSize;
    public int $reserved;
    public int $timestamp;
    public int $dataSize;

    function __construct(string $filename, array $entryParams) {

      $this->filename = $filename;
      $this->packingMethod = $entryParams[0];
      $this->originalSize = $entryParams[1];
      $this->reserved = $entryParams[2];
      $this->timestamp = $entryParams[3];
      $this->dataSize = $entryParams[4];
    }

    public function getLength(): int {
        return strlen($this->filename) + ENTRY_END_OFFSET;
    }
  }

  class PBOHeader {
    public array $entries = array();

    public function addEntry(PBOHeaderEntry $entry) {
      $this->entries[] = $entry;
    }

    public function getLength(): int {
      return array_reduce($this->entries, function(int $length, PBOHeaderEntry $entry) {
        return $length + $entry->getLength();
      }, 0);
    }
  }

  class PBOFile {
    private PBOHeader $header;

    function __construct(string $filepath) {
      $this->header = new PBOHeader();

      $pboContent = file_get_contents($filepath);

      while($headerEntry = $this->getHeaderEntry($pboContent, $this->header->getLength())) {
          $this->header->addEntry($headerEntry);
      }
    }

    private function getHeaderEntry(string $pboContent, int $offset = 0): ?PboHeaderEntry {
        $filename = unpack('Z*', $pboContent, $offset);
        if($filename[1] == "" || !isset($filename[1])) return null;
        return new PboHeaderEntry($filename[1], array_values(unpack('L5', $pboContent, $offset + strlen($filename[1]) + 1)));
    }

    public function getFileContent(string $filename) {
      
    }
  }
?>
