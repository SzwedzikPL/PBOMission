<?php
  class MissionObject {
    public int $id;
    public string $class;
    public bool $isSimple = false;

    function __construct(SQMClass $object) {
      $this->id = $object->attribute('id');
      $this->class = $object->attribute('type');

      if ($object->hasClass('Attributes'))
        $this->isSimple = (bool) $object->class('Attributes')->attribute('createAsSimpleObject', false);
    }
  }
?>
