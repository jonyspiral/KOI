<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('sistema/avanzado/editar/')) { ?>
<?php

try {
	shell_exec('cmd /c /xampp/apache_restart.bat');
} catch (Exception $ex){
	Html::jsonError($ex->getMessage());
}

?>
<?php } ?>