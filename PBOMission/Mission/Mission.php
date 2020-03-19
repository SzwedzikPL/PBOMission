<?php
  require_once('SQMConfig/SQMConfig.php');
  require_once('MissionElements/MissionGroup.php');
  require_once('MissionElements/MissionUnit.php');
  require_once('MissionElements/MissionObject.php');
  require_once('MissionElements/MissionMarker.php');

  class Mission {
    public bool $error = false;
    public string $errorReason;

    private array $stringtable;
    private bool $hasStringtable = false;
    private string $name;
    private string $map;
    private string $description;
    private string $date;
    private string $time;
    private string $author;
    private int $slotCount;
    private array $weatherSettings;
    private array $dependencies;
    private array $groups;
    private array $markers;
    private array $resistanceSettings;
    private array $stats;
    private bool $curatorPresent;
    private array $curators;

    function __construct(SQMClass $config, string $map, ?array $stringtable) {
      $this->map = $map;

      if (isset($stringtable)) {
        $this->hasStringtable = true;
        $this->stringtable = $stringtable;
      }

      // Parse mission
      if (isset($config->attributes['addons[]']))
        $this->dependencies = $config->attributes['addons[]']->value;

      if (isset($config->classes['ScenarioData']))
        $this->parseScenarioData($config->classes['ScenarioData']);

      if (isset($config->classes['Mission'])) {
        $missionClasses = $config->classes['Mission']->classes;

        if (isset($missionClasses['Intel']))
          $this->parseIntel($missionClasses['Intel']);

        if (isset($missionClasses['Entities']))
          $this->parseEntities($missionClasses['Entities']);
      }

      // Process links
      // TODO: zeus module links, crew links
    }

    private function parseEntities(SQMCLass $entitiesClass) {

    }

    private function parseIntel(SQMCLass $entities) {

    }

    private function parseScenarioData(SQMCLass $scenarioData) {
      $attributes = $scenarioData->attributes;

      if (isset($attributes['author']))
        $this->author = $attributes['author']->value;

      if (isset($attributes['overviewText']))
        $this->description = $this->translate($attributes['overviewText']->value);
    }

    private function translate(string $text): string {
      if (!$this->hasStringtable || $text[0] != '@') return $text;
      $key = substr($text, 1);
      if (!isset($this->stringtable[$key])) return $text;
      return $this->stringtable[$key];
    }

    public function export(): array {
      $data = array();

      // Simle values
      if (isset($this->name)) $data['name'] = $this->name;
      if (isset($this->description)) $data['description'] = $this->description;
      if (isset($this->author)) $data['author'] = $this->author;
      if (isset($this->date)) $data['date'] = $this->date;
      if (isset($this->time)) $data['time'] = $this->time;

      return $data;
    }
  }

?>
