<?php
  require_once('SQMConfig/SQMConfig.php');
  require_once('MissionElements/MissionGroup.php');
  require_once('MissionElements/MissionUnit.php');
  require_once('MissionElements/MissionObject.php');
  require_once('MissionElements/MissionMarker.php');

  class Mission {
    public bool $error = false;
    public string $errorReason;

    private string $name;
    private string $map;
    private string $description;
    private string $date;
    private string $time;
    private string $author;
    private int $slotCount = 0;
    private array $weather;
    private array $dependencies;
    private array $groups = array();
    private array $markers = array();
    private array $resistance = array();
    private array $stats = array();
    private bool $curatorPresent = false;
    private array $curators;
    private bool $hasStringtable = false;
    private array $stringtable;

    function __construct(SQMClass $config, string $map, ?array $stringtable) {
      $this->map = $map;

      if (isset($stringtable)) {
        // Using hasStringtable for faster exit from translate function
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

    private function parseIntel(SQMCLass $intel) {
      $attributes = $intel->attributes;

      if (isset($attributes['briefingName']))
        $this->name = $this->translate($attributes['briefingName']->value);

      if (isset($attributes['year'], $attributes['month'], $attributes['day'])) $this->date = sprintf(
        '%04d-%02d-%02d',
        $attributes['year']->value,
        $attributes['month']->value,
        $attributes['day']->value,
      );

      if (isset($attributes['hour'], $attributes['minute'])) {
        $minute = $attributes['minute']->value;
        // Arma saves minutes after 30 as negative values
        if ($minute < 0) $minute = 60 + $minute;

        $this->time = sprintf('%s:%s', $attributes['hour']->value, sprintf("%02d", $minute));
      }

      $weather = array();
      foreach (array(
        'startWeather' => 'overcast',
        'startWind' => 'wind',
        'startGust' => 'gust',
        'startFog' => 'fog',
        'startFogDecay' => 'fogDecay',
        'startRain' => 'rain',
        'startLightnings' => 'lightnings',
        'startWaves' => 'waves',
      ) as $attrKey => $key) {
        if (isset($attributes[$attrKey])) $weather[$key] = $attributes[$attrKey]->value;
      }

      if (count($weather) > 0) $this->weather = $weather;
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
      foreach(array('name','map','description','author','date','time','weather','dependencies','slotCount','curatorPresent') as $key) {
        if (isset($this->{$key})) $data[$key] = $this->{$key};
      }

      return $data;
    }
  }

?>
