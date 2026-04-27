<?php

require_once('premaster.php');

try {
	$_SESSION['empresa'] = ($_SESSION['empresa'] == 1 ? 2 : 1);
	Html::jsonSuccess();
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>