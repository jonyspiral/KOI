<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/avanzado/buscar/')) { ?>
<?php

try {
	Html::jsonSuccess();
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>