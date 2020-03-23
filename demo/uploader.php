<?php
  header('Content-Type: application/json');

  $startTime = hrtime(true);
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

  if (!$mission->error) {
    $fileHash = md5_file($missionFile["tmp_name"]);

    $response['fileHash'] = $fileHash;
    $response['parsingTime'] = (hrtime(true) - $startTime)/1e+6;
    $response['memoryPeakUsage'] = PBOMissionHelper::getReadableSize(memory_get_peak_usage());

    if (!is_dir('mission_exports')) mkdir('mission_exports');
    file_put_contents('mission_exports'.DIRECTORY_SEPARATOR.$fileHash.'.json', json_encode($response));
  }

  // End of parsing

  try {
    $log = '['.date('Y-m-d H:i:s', time()).'] file='.$filename.' ';

    if ($mission->error) {
      // Move pbo with error for debug
      if (!is_dir('failed_missions')) mkdir('failed_missions');
      move_uploaded_file($missionFile["tmp_name"], 'failed_missions'.DIRECTORY_SEPARATOR.$filename);
      // Log error
      $log .= 'error='.$mission->errorReason;
    } else {
      // Log parsing stats
      $log .= sprintf(
        'time=%s memory=%s pbo=%s sqm=%s',
        $response['parsingTime'],
        $response['memoryPeakUsage'],
        $response['pbo']['size'],
        $response['pbo']['files']['mission.sqm']['size']
      );
    }

    file_put_contents('parsing'.($mission->error ? '_error' : '').'.log', $log.PHP_EOL, FILE_APPEND);
  } catch (Exception $e) {}

  print json_encode($response);
?>
