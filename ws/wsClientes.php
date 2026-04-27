<?php require_once('../premaster.php'); ?>
<?php

function goMail($msg) {
	$mail = new PHPMailer();
	$mail->Subject = 'Error al actualizar clientes en la web';
	$mail->AddAddress('jony@spiralshoes.com.ar');
	$mail->AddAddress('leandro@spiralshoes.com.ar');
	$mail->Body = 'Ocurrió un error al intentar actualizar el archivo de clientes de la web: ' . $msg;
	$mail->IsHTML(true);
	$mail->Send();
	$mail->ClearAddresses();
}

function armoSuc($suc) {
	$ret = array();
	$ret['clienteRazonSocial'] = $suc->cliente->razonSocial;
	$ret['clienteNombre'] = $suc->cliente->nombre;
	$ret['sucursalNombre'] = $suc->nombre;
	$ret['sucursalCalle'] = $suc->direccionCalle;
	$ret['sucursalNumero'] = $suc->direccionNumero;
	$ret['sucursalLocalidad'] = $suc->direccionLocalidad->nombre;
	$ret['sucursalProvincia'] = $suc->direccionProvincia->nombre;
	return $ret;
}

try {
	$arr = array();
	$zonas = Factory::getInstance()->getListObject('Zona');
	foreach($zonas as $zona) {
		$sucs = Factory::getInstance()->getListObject('Sucursal', 'anulado = \'N\' AND punto_venta = \'S\' AND cod_zona_geo = ' . Datos::objectToDB($zona->id));
		foreach($sucs as $suc) {
			$arr[$zona->id][] = armoSuc($suc);
		}
	}
	Html::jsonEncode('', $arr);

	/*
	$sucs['capital'] = Factory::getInstance()->getListObject('Sucursal', 'anulado = \'N\' AND (cod_zona = \'01\')');
	$sucs['norte'] = Factory::getInstance()->getListObject('Sucursal', 'anulado = \'N\' AND (cod_zona = \'04\')');
	$sucs['oeste'] = Factory::getInstance()->getListObject('Sucursal', 'anulado = \'N\' AND (cod_zona = \'06\')');
	//Localidad 54 es La Plata, que es el único que en ZONA tiene expreso
	//El resto de los expresos son INTERIOR
	$sucs['sur'] = Factory::getInstance()->getListObject('Sucursal', 'anulado = \'N\' AND (cod_zona = \'02\' OR cod_zona = \'03\' OR cod_localidad_nro = 54)');
	$sucs['interior'] = Factory::getInstance()->getListObject('Sucursal', 'anulado = \'N\' AND (cod_zona = \'05\' AND cod_localidad_nro <> 54)');

	$arr = array();
	foreach($sucs as $zona => $sucursales)
		foreach($sucursales as $suc)
			$arr[$zona][] = armoSuc($suc);
	Html::jsonEncode($arr);
	*/
} catch (Exception $ex) {
	goMail($ex->getMessage());
	Html::jsonNull();
}

?>