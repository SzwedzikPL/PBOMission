<?php

  include_once('SQMConfig/SQMConfig.php');

  class Mission {
    public bool $error;
    public string $errorReason;

    function __construct(string $data) {
      $this->error = false;
      // Parse config
      $config = new SQMConfig($data);

      if ($config->error) {
        $this->error = $config->error;
        $this->errorReason = $config->errorReason;
        return;
      };

      // Parse mission


    }
  }

?>
