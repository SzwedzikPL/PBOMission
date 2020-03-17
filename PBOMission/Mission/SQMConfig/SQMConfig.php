<?php

define('READ_MODE_CLASS', 0);
define('READ_MODE_ATTRIBUTE', 1);

class SQMAttribute  {
  public string $name;
  public bool $isArray;
  public $value;

  function __construct(string $name, bool $isArray) {
    $this->name = $name;
    $this->isArray = $isArray;
    $this->value = $isArray ? array() : '';
  }
}

class SQMClass {
  public string $name;
  public array $classes;
  public array $attributes;
  public ?SQMClass $parentClass;

  function __construct(string $name, ?SQMClass $parentClass) {
    $this->name = $name;
    $this->parentClass = $parentClass;
    $this->classes = array();
    $this->attributes = array();
  }
}

class SQMConfig {
  private SQMClass $root;
  private array $lines;
  private int $index;
  private string $mode;
  private SQMClass $currentClass;
  private SQMAttribute $currentAttribute;
  private array $classes;

  public bool $error;
  public string $errorReason;

  private static $errorReasons = array(
    'STRUCTURE_ERROR' => 'Błąd składniowy w pliku misji (mission.sqm).',
    'BINARIZED_FILE' => 'Błąd odczytu pliku misji (mission.sqm). Prawdopodobnie plik jest zbinaryzowany.'
  );

  function __construct($data) {
    // Check is file readable
    if (mb_ord($data[0]) == 0) {
      $this->error = true;
      $this->errorReason = self::$errorReasons['BINARIZED_FILE'];
      return;
    }

    $this->error = false;
    $this->lines = array_map('trim', preg_split("/[\r\n]+/", $data));
    $this->index = 0;

    $this->root = new SQMClass('_root_', null);
    $this->classes[] = $this->root;
    $this->currentClass = $this->root;
    $this->mode = READ_MODE_CLASS;

    // Parse config
    while (!$this->error && array_key_exists($this->index, $this->lines)) {
      $this->parseLine($this->lines[$this->index]);
      $this->index++;
    }

    if ($this->error) return;

    // Check if parser not ended on root (becouse it always shoud)
    if ($this->currentClass != $this->root) {
      $this->error = true;
      $this->errorReason = self::$errorReasons['STRUCTURE_ERROR'];
      return;
    }

    // Cleanup temporary data & references
    foreach ($this->classes as $class) {
    	unset($class->parentClass);
    }

    unset($this->classes);
    unset($this->currentClass);
    unset($this->currentAttribute);
    unset($this->lines);
    unset($this->index);

    //Remove unused data from config
    if (isset($this->root->classes['EditorData']))
      unset($this->root->classes['EditorData']);

    if (isset($this->root->classes['AddonsMetaData']))
      unset($this->root->classes['AddonsMetaData']);
  }

  private function parseLine($line) {
    //Filter out empty lines, comments and config defines
    if ($line == '' || $line[0] == '/' || $line[0] == '#') return;
    // Remove semicolon ending
    if ($line[-1] == ';') $line = substr($line, 0, -1);

    if ($line == '}') {
      if ($this->mode == READ_MODE_CLASS) {
        // Check if we are within structure
        if ($this->currentClass->parentClass == null) {
          $this->error = true;
          $this->errorReason = self::$errorReasons['STRUCTURE_ERROR'];
          return;
        }

        $this->currentClass = $this->currentClass->parentClass;
      }

      $this->mode = READ_MODE_CLASS;
      return;
    }

    if ($this->mode == READ_MODE_ATTRIBUTE) {
      // Check for safety is attr an array, should be in 100% of cases
      if ($this->currentAttribute->isArray) {
        if ($line[-1] == ',') $line = substr($line, 0, -1);
        $this->currentAttribute->value[] = trim($line, '"');
      } else {
        $this->currentAttribute->value = trim($line, '"');
      }

      return;
    }

    if (substr($line, 0, 6) == 'class ') {
      $this->mode = 'class';
      $class = new SQMClass(substr($line, 6), $this->currentClass);
      $this->currentClass->classes[$class->name] = $class;
      $this->currentClass = $class;
      $this->classes[] = $class;

      // Skip class entry line
      if ($this->lines[$this->index + 1] == '{') $this->index += 1;

      return;
    }

    if (strpos($line, '=') !== false) {
      $attributeArray = explode('=', $line, 2);

      $isMultiLineArray = $attributeArray[1] == '' && $this->lines[$this->index + 1] == '{';
      $isArray = $isMultiLineArray || $attributeArray[1][0] == '{';

      $attribute = new SQMAttribute($attributeArray[0], $isArray);

      if ($isMultiLineArray) {
        $this->mode = READ_MODE_ATTRIBUTE;
        $this->currentAttribute = $attribute;
        // Skip array entry line
        $this->index += 1;
      } else {
        $value = $attributeArray[1];
        if ($isArray) {
          $value = explode(',', trim($value, '{}'));
        } else {
          $value = trim($value, '"');
        }

        $attribute->value = $value;
      }

      $this->currentClass->attributes[$attribute->name] = $attribute;

      return;
    }
  }
}

?>
