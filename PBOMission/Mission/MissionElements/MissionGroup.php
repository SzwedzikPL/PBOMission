<?php
  define('MISSION_GROUP_EXPORT_KEYS', array('name','side'));

  class MissionGroup {
    // All groups
    public int $id;
    public int $unitsCount = 0;
    public int $waypointsCount = 0;
    public bool $playable = false;
    // Filled only if playable units present
    public string $side;
    public string $name;
    public array $playableUnits = array();
    public array $crewLinks = array();

    function __construct(SQMClass $group) {
      $this->id = $group->attribute('id');
      if (!$entities = $group->class('Entities')) return;

      foreach ($entities->classes as $entitie) {
        $dataType = $entitie->attribute('dataType');

        if ($dataType == 'Object') {
          $unit = new MissionUnit($entitie);
          if ($unit->playable) $this->playableUnits[] = $unit;
          $this->unitsCount++;
          continue;
        }

        if ($dataType == 'Waypoint') {
          $this->waypointsCount++;
          continue;
        }
      }

      // Is group playable
      if (!isset($this->playableUnits) || !$this->playableUnits) return;
      $this->playable = true;

      // Get lowercased side name
      $this->side = $group->attribute('side');
      if ($this->side) $this->side = mb_strtolower($this->side);

      // Get group custom name
      if ($customAttributes = $group->class('CustomAttributes')) {
        foreach ($customAttributes->classes as $customAttribute) {
          if ($customAttribute->attribute('property') != 'groupID') continue;
          if (!$customAttribute->hasClassPath('Value','data')) break;
          $this->name = $customAttribute->class('Value')->class('data')->attribute('value');
        }
      }

      // Process crew links
      if (!$group->hasClassPath('CrewLinks', 'Links')) return;
      foreach ($group->class('CrewLinks')->class('Links')->classes as $link) {
        $linkUnit = $link->attribute('item0');
        $linkVehicle = $link->attribute('item1');
        if (!isset($linkUnit, $linkVehicle)) continue;
        $this->crewLinks[$linkUnit] = $linkVehicle;
      }
    }

    public function export(): array {
      $data = array();

      foreach (MISSION_GROUP_EXPORT_KEYS as $key)
        if (isset($this->{$key})) $data[$key] = $this->{$key};

      $data['units'] = array();
      foreach ($this->playableUnits as $unit)
        $data['units'][] = $unit->export();

      return $data;
    }
  }
?>
