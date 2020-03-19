<?php
  $startTime = hrtime(true);

  require_once('../PBOMission/PBOMission.php');

  $pboMission = new PBOMission('parser_test_mission.Stratis.pbo');
  $summary = $pboMission->export();
  $summary['parsingTime'] = (hrtime(true) - $startTime)/1e+6.' ms';

  $memoryPeak = memory_get_peak_usage();
  $unit=array('B','KB','MB','GB','TB','PB');
  $summary['memoryPeakUsage'] = @round($memoryPeak/pow(1024,($i=floor(log($memoryPeak,1024)))),2).$unit[$i];

  print_r($summary);

  //echo json_encode($summary);
?>
