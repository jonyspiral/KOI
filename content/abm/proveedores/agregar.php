<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/proveedores/agregar/')) { ?>
<?php


$razonSocial = Funciones::post('razonSocial');
$rubro = Funciones::post('rubro');
$calle = Funciones::post('calle');
$numero = Funciones::post('numero');
$piso = Funciones::post('piso');
$dpto = Funciones::post('dpto');
$pais = Funciones::post('pais');
$provincia = Funciones::post('provincia');
$localidad = Funciones::post('localidad');
$codPostal = Funciones::post('codPostal');
$telefono1 = Funciones::post('telefono1');
$telefono2 = Funciones::post('telefono2');
$email = Funciones::post('email');
$fax = Funciones::post('fax');
$horarioDeAtencion = Funciones::post('horarioDeAtencion');
$paginaWeb = Funciones::post('paginaWeb');
$tipoProveedor = Funciones::post('tipoProveedor');
$cuit = Funciones::post('cuit');
$contactos = Funciones::post('contactos');
$transporte = Funciones::post('transporte');
$condicionIva = Funciones::post('condicionIva');
$nombre = Funciones::post('nombre');
$observaciones = Funciones::post('observaciones');
$imputacionGeneral = Funciones::post('imputacionGeneral');
$imputacionEspecifica = Funciones::post('imputacionEspecifica');
$imputacionHaber = Funciones::post('imputacionHaber');
$retenerImpuestoGanancias = Funciones::post('retenerImpuestoGanancias');
$conceptoImpuestoGanancias = Funciones::post('conceptoImpuestoGanancias');
$plazoPago = Funciones::post('plazoPago');

try {
	if(is_null($nombre) || is_null($razonSocial) || is_null($condicionIva) || is_null($imputacionGeneral) ||
	   is_null($imputacionEspecifica) || is_null($imputacionHaber) || is_null($plazoPago) || ($retenerImpuestoGanancias == 'S' && is_null($conceptoImpuestoGanancias))){
		throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');
	}

	$proveedor = Factory::getInstance()->getProveedor();
	
	$proveedor->razonSocial = $razonSocial;
	$proveedor->rubroPalabra = $rubro;
	$proveedor->direccionCalle = $calle;
	$proveedor->direccionNumero = $numero;
	$proveedor->direccionPiso = $piso;
	$proveedor->direccionDepartamento = $dpto;
	$proveedor->direccionPais = Factory::getInstance()->getPais($pais);
	$proveedor->direccionProvincia = Factory::getInstance()->getProvincia($pais, $provincia);
	$proveedor->direccionLocalidad = Factory::getInstance()->getLocalidad($pais, $provincia, $localidad);
	$proveedor->direccionCodigoPostal = $codPostal;
	$proveedor->telefono1 = $telefono1;
	$proveedor->telefono2 = $telefono2;
	$proveedor->email = $email;
	$proveedor->fax = $fax;
	$proveedor->horariosAtencion = $horarioDeAtencion;
	$proveedor->paginaWeb = $paginaWeb;
	$proveedor->tipoProveedor = Factory::getInstance()->getTipoProveedor($tipoProveedor);
	$proveedor->cuit = $cuit;	
	$proveedor->transporte = Factory::getInstance()->getTransporte($transporte);
	$proveedor->nombre = $nombre;
	$proveedor->observaciones = $observaciones;
	$proveedor->condicionIva = Factory::getInstance()->getCondicionIva($condicionIva);
	$proveedor->imputacionGeneral = Factory::getInstance()->getImputacion($imputacionGeneral);
	$proveedor->imputacionEspecifica = Factory::getInstance()->getImputacion($imputacionEspecifica);
	$proveedor->imputacionHaber = Factory::getInstance()->getImputacion($imputacionHaber);
	$proveedor->retenerImpuestoGanancias = $retenerImpuestoGanancias;
	$proveedor->plazoPago = $plazoPago;

	if($retenerImpuestoGanancias == 'S'){
		$proveedor->conceptoRetenGanancias = Factory::getInstance()->getConceptoRetencionGanancias($conceptoImpuestoGanancias)->id;
	}else{
		$proveedor->conceptoRetenGanancias = null;
	}

	$proveedor->guardar()->notificar('abm/proveedores/agregar/');
	Html::jsonSuccess('El proveedor fue guardado correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el proveedor');
}
?>
<?php } ?>