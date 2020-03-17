<?php

require_once('PBOFile/PBOFile.php');
require_once('Mission/Mission.php');

class PBOMission {
  function __construct(string $filepath) {
    $pbo = new PBOFile($filepath);

    print('<br><pre>');
    print_r($pbo);
    print('</pre><br><br>');

    //$pbo->name;
    $mission = $pbo->getFileContent('mission.sqm');

    print($mission);

    //$stringtable = $pbo->getFileContent('stringtable.xml');


  }
}

?>
