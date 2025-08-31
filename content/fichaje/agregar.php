<?php
require_once('../../premaster.php');

$arregloHoratio = 1 * 3600;

function checkIp() {
	global $id, $lugarFichado;
	//Compruebo IP y veo lugar de fichaje
	switch (Funciones::getIpAddress()) {
		case '192.168.2.114': //Administración
			$lugarFichado = 'A';
			break;
		case '192.168.2.250': //Fichaje fábrica
			$lugarFichado = 'B';
			break;
		case '192.168.2.49': //Fichaje aparado
			$lugarFichado = 'C';
			break;
		case '192.168.2.20': //Mascherpa fábrica
			$lugarFichado = 'M';
			break;
		case '192.168.2.94': //Leandro
			$lugarFichado = 'L';
			break;
		case '192.168.2.72': //Corte
			$lugarFichado = 'T';
			break;
		case '192.168.2.19': //Recepcion
			$lugarFichado = 'C';
			break;
		default:
			$personal = Factory::getInstance()->getListObject('Personal', 'legajo_nro = ' . Datos::objectToDB($id) . ' AND anulado = \'N\'');
			if (count($personal) == 1) {
				$personal = $personal[0];
				$cuerpo = 'El personal "' . $personal->idPersonal . ' - ' . $personal->nombreApellido . '" ';
				$cuerpo .= 'intentó fichar desde una ubicación no permitida: ' . Funciones::getIpAddress();
				Email::enviar(
					 array(
						 'para' => array('leandro@spiralshoes.com', 'gg@spiralshoes.com'),
						 'asunto' => 'Intento de fichaje ilegal!!!',
						 'contenido' => $cuerpo
					 )
				);
			}
			return 'Debe fichar en los lugares correspondientes';
			break;
	}
	return false;
}

function entrada($tipo) {
	global $id, $lugarFichado, $arregloHoratio;
	$fichaje = Factory::getInstance()->getFichaje();
	//Seteo el ID antes de pedir FICHAJE DEL DIA ANTEIOR porque necesito saber de quién busco el fichaje del día anterior para el lazy loading
	$fichaje->legajo = $id;
	//Evaluar anomalías del día anterior
	$fichaje->anomalias = 'N';
	if (!isset($fichaje->fichajeDiaAnterior->horaSalida))
		$fichaje->anomalias = 'S';
	$fichaje->fecha = Funciones::hoy();
	$fichaje->horaEntrada = Funciones::ahora('H:i');
	//$fichaje->horaEntrada = date('H:i', time() - $arregloHoratio); TODO Arreglar la config para que la hora no esté 1 hora atrasada
	if ($tipo == 'ENT')
		$fichaje->diferenciaEntrada = Funciones::diferenciaMinutos($fichaje->horaEntrada, $fichaje->personal->fajaHoraria->horarioEntrada);
	$fichaje->tipo = $tipo;
	$fichaje->lugarEntrada = $lugarFichado;

	Factory::getInstance()->persistir($fichaje);
}

function salida($fichaje) {
	global $lugarFichado, $arregloHoratio;
	//$fichaje->horaSalida = date('H:i', time() - $arregloHoratio); TODO Arreglar la config para que la hora no esté 1 hora atrasada
	$fichaje->horaSalida = Funciones::ahora('H:i');

	//Antes de guardar compruebo que no se hayan confundido y fichado dos veces.
	comprobarRepeticion($fichaje->horaEntrada, $fichaje->horaSalida);
	
	$fichaje->diferenciaSalida = Funciones::diferenciaMinutos($fichaje->personal->fajaHoraria->horarioSalida, $fichaje->horaSalida);
	$fichaje->lugarSalida = $lugarFichado;
	Factory::getInstance()->persistir($fichaje);
}

function reEntrada($fichajeAnterior) {
	//Antes de guardar compruebo que no se hayan confundido y fichado dos veces.
	comprobarRepeticion($fichajeAnterior->horaSalida, Funciones::ahora('H:i'));

	//Agarro el [0] de $fichajes y le pongo diferencia de salida en NULL
	$fichajeAnterior->diferenciaSalida = null;
	Factory::getInstance()->persistir($fichajeAnterior);

	//Creo un nuevo fichaje de tipo REI y con hora de entrada AHORA
	entrada('REI');
}

function comprobarRepeticion($horaUno, $horaDos) {
	//Comprueba que haya pasado un mínimo de tiempo entre dos fichadas
	$diferencia = Funciones::diferenciaMinutos($horaDos, $horaUno);
	if ($diferencia < Fichaje::MINUTOS_ERROR)
		throw new FactoryException('Fichaje repetido. Espere ' . (Fichaje::MINUTOS_ERROR - $diferencia) . ' minutos.');
}

$id = Funciones::post('id');

try {
	$id = Funciones::toInt(substr($id, 3));

	$lugarFichado = 'A'; //Por defecto
	$error = checkIp();
	if ($error)
		throw new FactoryException($error);
	
	//Compruebo si existe el personal ingresado
	$personal = Factory::getInstance()->getListObject('Personal', 'legajo_nro = ' . Datos::objectToDB($id) . ' AND anulado = \'N\'');
	if (count($personal) != 1)
		throw new FactoryException('El personal ingresado no existe o está dado de baja');
	$where = 'legajo_nro = ' . Datos::objectToDB($id) . ' AND fecha = dbo.getPrimeraHora(GETDATE()) ';
	$order = 'ORDER BY movimiento_tipo DESC, fecha DESC, entrada_horario DESC';
	$fichajes = Factory::getInstance()->getListObject('Fichaje', $where . $order);
	$salida = false;
	switch(count($fichajes)) {
		case 0:
			entrada('ENT');
			break;
		default:
			if (isset($fichajes[0]->horaSalida))
				reEntrada($fichajes[0]);
			else {
				salida($fichajes[0]);
				$salida = true;
			}
			break;
	}
	/** @var Personal $personal */
	$personal = $personal[0];
	Html::jsonSuccess(($salida ? 'Adios' : 'Hola') . ', ' . $personal->nombreApellido . '. Tu ' . ($salida ? 'salida' : 'entrada') . ' se registró correctamente');
} catch (FactoryException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar registrar el horario');
}
?>
<?php //} ?>