<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/transportes/editar/')) { ?>
<?php


$idTransporte = Funciones::post('idTransporte');
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
	if (!isset($idTransporte))
		throw new FactoryExceptionRegistroNoExistente();
	
	$transporte = Factory::getInstance()->getTransporte($idTransporte);
	
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

	$transporte->guardar()->notificar('abm/transportes/editar/');
	Html::jsonSuccess('El transporte fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El transporte que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el transporte');
}
?>
<?php } ?>