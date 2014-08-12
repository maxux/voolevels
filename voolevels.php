<?php
include('voolevels.class.php');
include('vooconsole.class.php');

$levels = new VooLevels('voo', 'PASSWORD');
$data = $levels->getArray();

$console = new VooConsole($data);
$console->render();
?>
