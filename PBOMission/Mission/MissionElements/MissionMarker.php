<?php
  class MissionMarker {
    public int $id;
    public string $text;
    public string $class;
    public string $color;
    public array $position;
    public float $angle = 0;

    function __construct(SQMClass $markerClass) {

    }
  }
?>
