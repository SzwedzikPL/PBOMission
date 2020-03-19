<?php
  $startTime = hrtime(true);

  require_once('../PBOMission/PBOMission.php');

  $pboMission = new PBOMission('parser_test_mission.Stratis.pbo');
  $summary = $pboMission->export();
  $summary['parsingTime'] = (hrtime(true) - $startTime)/1e+6.' ms';
  $summary['memoryPeakUsage'] = PBOMissionHelper::getReadableSize(memory_get_peak_usage());

  print_r($summary);

  //echo json_encode($summary);
?>
