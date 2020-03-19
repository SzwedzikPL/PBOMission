<?php
  class MissionGroup {
    // All groups
    public int $id;
    public int $unitsCount;
    public bool $playable;
    // Filled only if playable units present
    public string $side;
    public string $name;
    public array $playableUnits;
    public array $crewLinks;

    function __construct(SQMClass $groupClass) {

    }
  }
?>
