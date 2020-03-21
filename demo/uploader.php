<?php
  $startTime = hrtime(true);

  header('Content-Type: application/json');
  require_once('../PBOMission/PBOMission.php');

  if (!isset($_FILES['missionFile'])) return print json_encode(array(
    'error' => true,
    'errorReason' => 'Błąd uploadu pliku. Spróbuj jeszcze raz.'
  ));

  $missionFile = $_FILES['missionFile'];
  $filename = $missionFile['name'];

  if (pathinfo($filename, PATHINFO_EXTENSION) != 'pbo') return print json_encode(array(
    'error' => true,
    'errorReason' => 'Niepoprawny typ pliku. Tylko pliki PBO są obsługiwane.'
  ));

  $mission = new PBOMission($missionFile["tmp_name"], $filename);
  $response = $mission->export();

  if ($mission->error) {
    //TODO: log error in errors.txt, save mission in folder for examination
  } else {
    $response['parsingTime'] = (hrtime(true) - $startTime)/1e+6.' ms';
    $response['memoryPeakUsage'] = PBOMissionHelper::getReadableSize(memory_get_peak_usage());
  }

  print json_encode($response);
?>
