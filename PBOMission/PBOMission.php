<?php

require_once('PBOFile/PBOFile.php');
require_once('Mission/Mission.php');

class PBOMission {
  public PBOFile $pbo;
  public Mission $mission;
  public bool $error;
  public string $errorReason;

  private static $errorReasons = array(
    'EMPTY_MISSION' => 'Błąd odczytu pliku misji (mission.sqm) lub jego brak.',
    'INVALID_MAP' => 'Niepoprawna nazwa mapy. Sprawdź nazwę pliku.',
  );

  function __construct(string $filepath) {
    $this->error = false;
    $this->pbo = new PBOFile($filepath);

    if ($this->pbo->error) {
      $this->error = true;
      return $this->errorReason = $this->pbo->errorReason;
    }

    $missionContent = $this->pbo->getFileContent('mission.sqm');

    if (!isset($missionContent) || $missionContent == '') {
      $this->error = true;
      return $this->errorReason = self::$errorReasons['EMPTY_MISSION'];
    }

    $nameElements = explode('.', $this->pbo->name);
    $map = end($nameElements);

    if ($map == '') {
      $this->error = true;
      return $this->errorReason = self::$errorReasons['INVALID_MAP'];
    }

    $this->mission = new Mission($missionContent, $map);

    if ($this->mission->error) {
      $this->error = true;
      return $this->errorReason = $this->mission->errorReason;
    }

    //$stringtable = $pbo->getFileContent('stringtable.xml');
  }

  public function export(): array {
    return array(
      'pbo' => $this->pbo->info,
      'mission' => $this->mission->export()
    );
  }
}

?>
