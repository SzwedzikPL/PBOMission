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
    'XML_ERROR' => 'Błąd parsowania xml (stringtable.xml). Powód: %s'
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

    $stringtableContent = $this->pbo->getFileContent('stringtable.xml');
    $stringtable = null;
    if (isset($stringtableContent) && $stringtableContent != '') {
      libxml_use_internal_errors(true);

      $stringtable = simplexml_load_string($stringtableContent);
      foreach (libxml_get_errors() as $error) {
        $this->error = true;
        return $this->errorReason = sprintf(self::$errorReasons['XML_ERROR'], $error->message);
      }
    }

    $this->mission = new Mission($missionContent, $map, $stringtable);

    if ($this->mission->error) {
      $this->error = true;
      return $this->errorReason = $this->mission->errorReason;
    }
  }

  public function export(): array {
    return array(
      'pbo' => $this->pbo->info,
      'mission' => $this->mission->export()
    );
  }
}

?>
