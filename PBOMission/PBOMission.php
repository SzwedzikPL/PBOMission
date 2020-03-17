<?php

require_once('PBOFile/PBOFile.php');
require_once('Mission/Mission.php');

class PBOMission {
  function __construct(string $filepath) {
    $pbo = new PBOFile($filepath);

    print_r($pbo);

    //$pbo->name;
    //$mission = $pbo->getFileContent('mission.sqm');
    //$stringtable = $pbo->getFileContent('stringtable.xml');


  }
}

?>
