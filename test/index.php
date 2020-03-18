<?php
  $startTime = hrtime(true);

  require_once('../PBOMission/PBOMission.php');

  $pboMission = new PBOMission('parser_test_mission.Stratis.pbo');

  if ($pboMission->error) {
    return print($pboMission->errorReason);
  }

  $summary = $pboMission->export();
  $summary['parsingTime'] = (hrtime(true) - $startTime)/1e+6.' ms';

  print_r($summary);
?>
