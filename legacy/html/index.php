<?php
include('../common/voolevels.class.php');
include('html.class.php');

$levels = new VooLevels('voo', 'ISGTZRAM');
$data = $levels->getArray();

$html = new VooTable($data);
echo $html->render();
?>
