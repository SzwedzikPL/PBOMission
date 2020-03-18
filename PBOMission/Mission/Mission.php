<?php
  include_once('SQMConfig/SQMConfig.php');

  class Mission {
    public bool $error;
    public string $errorReason;

    public string $name;
    public string $map;
    public string $description;
    public string $date;
    public string $time;
    public string $author;
    public int $slotCount;
    public array $weatherSettings;
    public array $dependencies;
    public array $groups;
    public array $markers;
    public array $resistanceSettings;
    public array $stats;

    function __construct(SQMConfig $config, string $map, ?SimpleXMLElement $stringtable) {
      $this->error = false;
      $this->map = $map;

      // Prepare stringtable for use in mission parser
      if (isset($stringtable)) {

      }

      // Parse mission

    }

    public function export(): array {
      return array();
    }
  }

?>
