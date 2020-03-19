<?php
  class MissionObject {
    public int $id;
    public string $class;
    public bool $simpleObject = false;

    function __construct(SQMClass $objectClass) {

    }
  }
?>
