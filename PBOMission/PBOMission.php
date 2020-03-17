<?php

require_once('PBOFile/PBOFile.php');
require_once('Mission/Mission.php');

class PBOMission {
  public PBOFile $pbo;
  public Mission $mission;

  private static $errorReasons = array(
    'EMPTY_MISSION' => 'Błąd odczytu pliku misji (mission.sqm) lub jego brak.'
  );

  function __construct(string $filepath) {
    $this->pbo = new PBOFile($filepath);

    if ($this->pbo->error) {
      return print($this->pbo->errorReason);
    }

    $missionContent = $this->pbo->getFileContent('mission.sqm');

    if (!isset($missionContent) || $missionContent == '')
      return print(self::$errorReasons['EMPTY_MISSION']);

    $this->mission = new Mission($missionContent);

    if ($this->mission->error) {
      return print($this->mission->errorReason);
    }

    //$stringtable = $pbo->getFileContent('stringtable.xml');


  }
}

?>
