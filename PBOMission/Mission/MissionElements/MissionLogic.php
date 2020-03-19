<?php
  class MissionLogic {
    public int $id;
    public string $variable;
    public string $class;
    public bool $playable;
    public string $description;

    function __construct(SQMClass $logicClass) {

    }
  }
?>
