<?php
  define('MISSION_UNIT_EXPORT_KEYS',
  array('class','description','position','primaryWeapon','vehicle','curator'));

  class MissionUnit {
    // All units
    public bool $playable = false;
    public bool $curator = false;
    // Present only if playable
    public int $id;
    public string $class;
    public ?string $variable;
    public ?string $primaryWeapon;
    public ?array $position;
    public ?string $description;
    public ?string $vehicle;

    function __construct(SQMClass $unitClass) {
      global $unitDefaultWeapons;


    }

    public function export(): array {
      $data = array();

      foreach (MISSION_UNIT_EXPORT_KEYS as $key)
        if (isset($this->{$key})) $data[$key] = $this->{$key};

      return $data;
    }
  }
?>
