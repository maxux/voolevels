<?php
include('common/voolevels.class.php');
include('console/console.class.php');

$levels = new VooLevels('voo', 'DYJLUDUD');
$data = $levels->getArray();

$console = new VooConsole($data);
$console->render();
?>
