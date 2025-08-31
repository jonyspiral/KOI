<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/vendedores/editar/')) { ?>
<?php
 

$id= Funciones::post('id');
$nombre= Funciones::post('nombre');
$apellido= Funciones::post('apellido');
$dni= Funciones::post('dni');
$direccion= Funciones::post('direccion');
$numero= Funciones::post('numero');
$piso= Funciones::post('piso');
$dpto= Funciones::post('dpto');
$pais= Funciones::post('pais');
$provincia= Funciones::post('provincia');
$localidad= Funciones::post('localidad');
$codPostal= Funciones::post('codPostal');
$telefono= Funciones::post('telefono');
$celular= Funciones::post('celular');
$email= Funciones::post('email');
$antiguedad= Funciones::post('antiguedad');
$fajaHoraria= Funciones::post('fajaHoraria');
$ingreso= Funciones::post('ingreso');
$egreso= Funciones::post('egreso');
$modalidadRetribucion= Funciones::post('modalidadRetribucion');
$comision= Funciones::post('comision');
$valorHora= Funciones::post('valorHora');
$valorMes= Funciones::post('valorMes');
$valorQuincena= Funciones::post('valorQuincena'); 
$fechaNacimiento = Funciones::post('fechaNacimiento');
$cuil = Funciones::post('cuil');
$legajo = Funciones::post('legajo');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	
	$personal = Factory::getInstance()->getVendedor($id);
	
	$personal->nombre = $nombre;
	$personal->apellido = $apellido;
	$personal->dni = $dni;
	$personal->direccionCalle = $direccion;
	$personal->direccionNumero = $numero;
	$personal->direccionPiso = $piso;
	$personal->direccionDepartamento= $dpto;
	$personal->direccionPais = Factory::getInstance()->getPais($pais);
	$personal->direccionProvincia = Factory::getInstance()->getProvincia($pais, $provincia);
	$personal->direccionLocalidad = Factory::getInstance()->getLocalidad($pais, $provincia, $localidad);
	$personal->telefono = $telefono;
	$personal->celular = $celular;
	$personal->email = $email;
	$personal->fechaAntiguedadGremio = $antiguedad;
	$personal->fajaHoraria = Factory::getInstance()->getFajaHoraria($fajaHoraria);
	$personal->fechaIngreso = $ingreso;
	$personal->fechaEgreso = $egreso;
	$personal->modalidadRetribucion = $modalidadRetribucion;
	$personal->porcComisionVtas = $comision;
	$personal->valorHora = $valorHora;
	$personal->valorMes = $valorMes;
	$personal->valorQuincena = $valorQuincena;
	$personal->fechaNacimiento = $fechaNacimiento;
	$personal->cuil = $cuil;
	$personal->direccionCodigoPostal = $codPostal;
	$personal->legajo = $legajo;
	
	
	Factory::getInstance()->persistir($personal);
	Html::jsonSuccess('El personal fue guardado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El personal que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el personal');
}
?>
<?php } ?>