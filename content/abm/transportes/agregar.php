<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/transportes/agregar/')) { ?>
<?php

$nombre = Funciones::post('nombre');
$telefono = Funciones::post('telefono');
$email = Funciones::post('email');
$calle = Funciones::post('calle');
$numero = Funciones::post('numero');
$piso = Funciones::post('piso');
$dpto = Funciones::post('dpto');
$idPais = Funciones::post('idPais');
$idProvincia = Funciones::post('idProvincia');
$idLocalidad = Funciones::post('idLocalidad');
$codPostal = Funciones::post('codPostal');
$horario = Funciones::post('horarioAtencion');
$cuit = Funciones::post('cuit');

try {
	$transporte = Factory::getInstance()->getTransporte();

	$transporte->nombre = $nombre;
	$transporte->telefono = $telefono;
	$transporte->email = $email;
	$transporte->direccionCalle = $calle;
	$transporte->direccionNumero = $numero;
	$transporte->direccionPiso = $piso;
	$transporte->direccionDepartamento = $dpto;
	$transporte->direccionCodigoPostal = $codPostal;
	$transporte->direccionPais = Factory::getInstance()->getPais($idPais);
	$transporte->direccionProvincia = Factory::getInstance()->getProvincia($idPais, $idProvincia);
	$transporte->direccionLocalidad = Factory::getInstance()->getLocalidad($idPais, $idProvincia, $idLocalidad);	
	$transporte->horario = $horario;
	$transporte->cuit = $cuit;	

	$transporte->guardar()->notificar('abm/transportes/agregar/');
	Html::jsonSuccess('El transporte fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el transporte');
}

?>
<?php } ?>