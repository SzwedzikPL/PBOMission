<?php
  class MissionUnit {
    // All units
    public bool $playable;
    // Present only if playable
    public int $id;
    public string $class;
    public ?string $variable;
    public ?string $primaryWeapon;
    public ?array $position;
    public ?string $description;
    public ?string $vehicle;
    public bool $curator = false;

    function __construct(SQMClass $unitClass) {
      global $unitDefaultWeapons;


    }

    public function export(): array {
      return array();
    }
  }
?>
