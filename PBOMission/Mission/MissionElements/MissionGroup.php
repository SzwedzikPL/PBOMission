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
    public array $playableUnits;
    public array $crewLinks;

    function __construct(SQMClass $group) {

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
