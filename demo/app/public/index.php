<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>A3C Reborn: Parser misji</title>
  </head>
  <body>
    <noscript>
      <strong>Brak Javascriptu</strong>
    </noscript>
    <script>
    <?php
      if (isset($_GET['mission']) && strlen($_GET['mission']) == 32) {
        $jsonName = 'mission_exports'.DIRECTORY_SEPARATOR.$_GET['mission'].'.json';
        if (is_file($jsonName)) echo 'window.mission = '.file_get_contents($jsonName).';';
      }
    ?>
    </script>
    <div id="app"></div>
  </body>
</html>
