<?php
  // Header entry ending offset
  define('HEADER_ENTRY_END_OFFSET', 21);

  class PBOHeaderEntry {
    public string $filename;
    public int $packingMethod;
    public int $originalSize;
    public int $reserved;
    public int $timestamp;
    public int $dataSize;
    public int $length;
    public int $dataOffset;

    function __construct(string $filename, array $entryParams) {
      $this->filename = $filename;
      $this->packingMethod = $entryParams[0];
      $this->originalSize = $entryParams[1];
      $this->reserved = $entryParams[2];
      $this->timestamp = $entryParams[3];
      $this->dataSize = $entryParams[4];
      $this->length = strlen($this->filename) + HEADER_ENTRY_END_OFFSET;
    }

    public function setDataOffset(int $offset) {
      $this->dataOffset = $offset;
    }
  }

  class PBOHeader {
    public array $entries = array();
    public int $length = 0;
    private int $currentDataOffset = 0;

    public function addEntry(PBOHeaderEntry $entry) {
      $entry->dataOffset = $this->currentDataOffset;

      // Update data offset for next entry
      $this->currentDataOffset += $entry->dataSize;

      // Update header length
      $this->length += $entry->length;

      $this->entries[$entry->filename] = $entry;
    }
  }

  class PBOFile {
    private PBOHeader $header;
    private string $filepath;

    function __construct(string $filepath) {
      $this->filepath = $filepath;
      $this->header = new PBOHeader();

      $pboContent = file_get_contents($filepath);

      while($headerEntry = $this->getHeaderEntry($pboContent, $this->header->length)) {
          $this->header->addEntry($headerEntry);
      }
    }

    private function getHeaderEntry(string $pboContent, int $offset = 0): ?PboHeaderEntry {
        $entryData = unpack('Z*', $pboContent, $offset);
        if(!isset($entryData[1]) || $entryData[1] == "") return null;
        $filename = $entryData[1];

        return new PboHeaderEntry($filename, array_values(unpack('L5', $pboContent, $offset + strlen($filename) + 1)));
    }

    public function getFileContent(string $filename): ?string {
      if(!isset($this->header->entries[$filename])) return null;

      $pboContent = file_get_contents($this->filepath);

      $entry = $this->header->entries[$filename];
      $offset = $this->header->length + HEADER_ENTRY_END_OFFSET + $entry->dataOffset;

      return substr($pboContent, $offset, $entry->dataSize);
    }
  }
?>
