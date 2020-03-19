<?php
  require_once('SQMConfig/SQMConfig.php');
  require_once('MissionElements/MissionObject.php');
  require_once('MissionElements/MissionMarker.php');
  require_once('MissionElements/MissionLogic.php');
  require_once('MissionElements/MissionGroup.php');
  require_once('MissionElements/MissionUnit.php');

  class Mission {
    public bool $error = false;
    public string $errorReason;

    private string $name;
    private string $map;
    private string $description;
    private string $date;
    private string $time;
    private string $author;
    private array $weather;
    private array $dependencies;

    private array $groups = array();
    private array $markers = array();
    private array $resistance = array();
    private array $stats = array(
      'units' => 0,
      'groups' => 0,
      'triggers' => 0,
      'markers' => 0,
      'objects' => 0,
      'simpleObjects' => 0,
      'modules' => 0,
      'aiGenerators' => 0,
      'attackGenerators' => 0
    );
    private int $slotCount = 0;

    private bool $curatorPresent = false;
    private array $curators = array();

    private bool $hasStringtable = false;
    private array $stringtable;

    // Temporary values
    private array $entities = array(); // Calculating links, id => entitie
    private array $unitVariables = array(); // Linking curator modules with units, variable => unit
    private array $curatorVariables = array(); // Variables for linking with units, variable

    function __construct(SQMClass $config, string $map, ?array $stringtable) {
      $this->map = $map;

      if (isset($stringtable) && count($stringtable) > 0) {
        // Using hasStringtable for faster exit from translate function
        $this->hasStringtable = true;
        $this->stringtable = $stringtable;
      }

      // Parse mission
      $this->dependencies = $config->attribute('addons[]');

      if ($config->hasClass('ScenarioData')) {
        $scenarioData = $config->class('ScenarioData');
        $this->author = $scenarioData->attribute('author');
        $this->description = $this->translate($scenarioData->attribute('overviewText'));
      }

      if ($config->hasClass('Mission')) {
        $mission = $config->class('Mission');
        $this->parseIntel($mission->class('Intel'));
        $this->parseEntities($mission->class('Entities'));
      }

      // Process links
      // TODO: zeus module links, crew links

      // Cleanup temporary data
      unset($this->entities);
      unset($this->unitVariables);
      unset($this->curatorVariables);
    }

    private function parseEntities(?SQMCLass $entities) {
      if (!isset($entities)) return;

      foreach ($entities->classes as $entitie) {
        if (!$entitie->hasAttribute('dataType')) continue;
        $dataType = $entitie->attribute('dataType');

        if ($dataType == 'Object') {
          $object = new MissionObject($entitie);
          $this->stats[$object->isSimple ? 'simpleObjects' : 'objects']++;
          $this->entities[$object->id] = $object;
          continue;
        }

        if ($dataType == 'Group') {
          $this->stats['groups']++;
          continue;
        }

        if ($dataType == 'Logic') {
          $logic = new MissionLogic($entitie);
          continue;
        }

        if ($dataType == 'Trigger') {
          $this->stats['triggers']++;
          continue;
        }

        if ($dataType == 'Marker') {
          $this->stats['markers']++;
          continue;
        }
      }
    }

    private function parseIntel(?SQMCLass $intel) {
      if (!isset($intel)) return;

      $this->name = $this->translate($intel->attribute('briefingName'));

      if ($intel->hasAttributes('year','month','day')) $this->date = sprintf(
        '%04d-%02d-%02d',
        $intel->attribute('year'),
        $intel->attribute('month'),
        $intel->attribute('day')
      );

      if ($intel->hasAttributes('hour','minute')) {
        $minute = $intel->attribute('minute');
        // Arma saves minutes after 30 as negative values
        if ($minute < 0) $minute = 60 + $minute;

        $this->time = sprintf('%s:%s', $intel->attribute('hour'), sprintf("%02d", $minute));
      }

      $weather = array('start' => array(), 'forecast' => array());
      foreach (array('weather','wind','gust','fog','fogDecay','rain','lightnings','waves') as $configKey) {
        foreach (array('start','forecast') as $typeKey) {
          $key = $typeKey.ucfirst($configKey);
          if ($intel->hasAttribute($key)) $weather[$typeKey][$configKey] = $intel->attribute($key);
        }
      }
      // Arma saves duration of weather changes in seconds (but sometimes is float "becouse Arma")
      // Range from 30min to 8h
      if ($intel->hasAttribute('timeOfChanges'))
        $weather['timeOfChanges'] = gmdate("H:i:s", floor($intel->attribute('timeOfChanges')));

      if (count($weather['start']) > 0) $this->weather = $weather;
    }

    private function translate(?string $text): ?string {
      if (!$this->hasStringtable || $text == null || $text[0] != '@') return $text;
      $key = substr($text, 1);
      if (!isset($this->stringtable[$key])) return $text;
      return $this->stringtable[$key];
    }

    public function export(): array {
      $data = array();

      // Simle values
      foreach(array('name','map','description','author','date','time','weather','dependencies','stats','slotCount','curatorPresent') as $key) {
        if (isset($this->{$key})) $data[$key] = $this->{$key};
      }

      return $data;
    }
  }

?>
