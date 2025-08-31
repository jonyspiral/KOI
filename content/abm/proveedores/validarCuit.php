<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/buscar/')) { ?>
<?php

$cuit = Funciones::get('cuit');

try {
	if (isset($cuit)){
		$proveedores = Factory::getInstance()->getListObject('Proveedor', 'cuit = ' . Datos::objectToDB($cuit));
		if (count($proveedores) == 0) {
			if(Funciones::validarCuit($cuit)){
				HTML::jsonSuccess();
			} else {
				HTML::jsonError($msgExiste . 'El cuit ingresado no es válido.');
			}
		} else {
			$proveedor = $proveedores[0];
			$msgExiste = 'El cuit ingresado ya existe. Corresponde al cliente "' . $proveedor->id . ' - ' . $proveedor->razonSocial . '" ';
			if ($proveedor->anulado == 'S' && $proveedor->autorizado == 'S')
				HTML::jsonError($msgExiste . 'y está anulado.');
			elseif ($proveedor->anulado == 'N' && $proveedor->autorizado == 'S')
				HTML::jsonError($msgExiste . '.');
			elseif ($proveedor->anulado == 'S' && $proveedor->autorizado == 'N')
				HTML::jsonError($msgExiste . 'y aún no fue autorizado.');
			elseif ($proveedor->anulado == 'N' && $proveedor->autorizado == 'N')
				HTML::jsonError($msgExiste . 'y está anulado y desautorizado (situación extraña).');
		}
	}
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar verificar el cuit');
}
?>
<?php } ?>