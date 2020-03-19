<?php
  class MissionUnit {
    // All units
    public bool $playable;
    // Present only if playable
    public int $id;
    public string $variable;
    public string $class;
    public string $primaryWeapon;
    public array $position;
    public string $description;
    public string $vehicle;
    public bool $curator;

    function __construct(SQMClass $unitClass) {

    }
  }
?>
