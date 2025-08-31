<?php

/**
 * @property AreaEmpresa	$areaEmpresa
 * @property KoiTicket		$ticketOriginal
 * @property Usuario		$responsable
 * @property Usuario		$usuario
 * @property Usuario		$usuarioCierre
 */

class KoiTicket extends Base {
	const		IMPORTANCIA_RESPONSABLE = 0.6;
	const		COD_TIPO_NOTIFICACION = 6;
	const		ESTADO_PENDIENTE = 'P';
	const		ESTADO_RESUELTO = 'R';
	const		ESTADO_RECHAZADO = 'Z';
	const		ESTADO_DELEGADO = 'D';

	const		_primaryKey = '["id"]';

	public		$id;
	public		$idAreaEmpresa;
	protected	$_areaEmpresa;
	public		$descripcion;
	public		$respuesta;
	public		$prioridadExterna;
	public		$prioridadInterna;
	public		$prioridad;
	public		$idTicketOriginal;
	protected	$_ticketOriginal;
	public		$idResponsable;
	protected	$_responsable;
	public		$fechaEstimadaResolucion;
	public		$estado;
	public		$idUsuarioCierre;
	protected	$_usuarioCierre;
	public		$fechaCierre;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaUltimaMod;
	public		$fechaBaja;

	protected	$_estadoNombre;
	protected	$_prioridadNombre;

	//Campos extras para el front-end
	public		$esAutor;
	public		$esResponsable;
	public		$colorIndicador;

	private		$linkApp = 'sistema/tickets/';
	private		$delegadoA;

	public function expand(){

		$this->esAutor = $this->esAutor(Usuario::logueado()) ? '1' : '';
		$this->esResponsable = $this->esResponsable(Usuario::logueado()) ? '1' : '';
		$this->colorIndicador = ($this->estado == self::ESTADO_RECHAZADO ? 'rojo' : 'verde');
		$this->usuario;
		$this->usuarioCierre;

		return parent::expand();
	}

	public function guardar() {
		Factory::getInstance()->beginTransaction();

		$this->prioridad = (is_null($this->prioridadInterna) ? $this->prioridadExterna : round($this->prioridadExterna * (1 - self::IMPORTANCIA_RESPONSABLE) + $this->prioridadInterna * (self::IMPORTANCIA_RESPONSABLE) ));

		$nuevo = !($this->id);
		!$nuevo && $this->notificarEdicion();
		parent::guardar();
		$nuevo && $this->notificarNuevo();

		Factory::getInstance()->commitTransaction();

		return $this;
	}

	public function esAutor(Usuario $usuario) {
		return ($usuario->id == $this->idUsuario);
	}

	public function esResponsable(Usuario $usuario) {
		return self::usuarioEsResponsableDelArea($usuario->id, $this->idAreaEmpresa);
	}

	protected function validarGuardar() {
		if ($this->responsable->id && $this->areaEmpresa->id && !self::usuarioEsResponsableDelArea($this->responsable->id, $this->areaEmpresa->id)) {
			throw new FactoryExceptionCustomException('No se puede guardar el ticket dado que el responsable elegido (' . $this->responsable->id . ') no es responsable del área');
		}
	}

	protected function validarBorrar() {

		if ($this->resuelto()) {
			throw new FactoryExceptionCustomException('No se puede borrar un ticket resuelto');
		} elseif ($this->rechazado()) {
			throw new FactoryExceptionCustomException('No se puede borrar un ticket rechazado');
		} elseif ($this->delegado()) {
			throw new FactoryExceptionCustomException('No se puede borrar un ticket delegado');
		}

		if ((Usuario::logueado()->id != $this->idUsuario) && !self::usuarioEsResponsableDelArea(Usuario::logueado()->id, $this->idAreaEmpresa)) {
			throw new FactoryExceptionCustomException('No tiene permisos para borrar este ticket');
		}

		parent::validarBorrar();
	}

	public static function usuarioEsResponsableDelArea($idUsuario, $idArea) {
		try {
			Factory::getInstance()->getUsuarioPorAreaEmpresa($idUsuario, $idArea);
			return true;
		} catch (FactoryExceptionRegistroNoExistente $ex) {
			return false;
		}
	}

	public function resuelto(){
		return $this->estado == self::ESTADO_RESUELTO;
	}

	public function rechazado(){
		return $this->estado == self::ESTADO_RECHAZADO;
	}

	public function delegado(){
		return $this->estado == self::ESTADO_DELEGADO;
	}

	public function original(){
		return is_null($this->idTicketOriginal);
	}

	public function resolver($funcionalidad = false){
		$this->estado = self::ESTADO_RESUELTO;
		$this->guardar()->notificar($funcionalidad);
	}

	public function rechazar($funcionalidad = false, $motivo){
		$this->estado = self::ESTADO_RECHAZADO;
		$this->respuesta = $motivo;
		$this->guardar()->notificar($funcionalidad);
	}

	public function delegar($funcionalidad = false, $idAreaEmpresa){
		$this->estado = self::ESTADO_DELEGADO;
		$nuevoTicket = Factory::getInstance()->getKoiTicket();
		$nuevoTicket->areaEmpresa = Factory::getInstance()->getAreaEmpresa($idAreaEmpresa);
		if (!$nuevoTicket->areaEmpresa->habilitadaTicket()) {
			throw new FactoryExceptionCustomException('No se puede delegar un ticket a un área que no está habilitada para recibir tickets');
		}
		$nuevoTicket->descripcion = 'Delegado de "' . $this->areaEmpresa->nombre . '" - ' . $this->descripcion;
		$nuevoTicket->prioridadExterna = $this->prioridadExterna;
		$nuevoTicket->ticketOriginal = $this;
		$nuevoTicket->estado = KoiTicket::ESTADO_PENDIENTE;
		$nuevoTicket->usuario = $this->usuario;
		$nuevoTicket->guardar()->notificar($this->linkApp . 'agregar/');
		$this->delegadoA = $nuevoTicket->areaEmpresa->nombre;
		$this->guardar()->notificar($funcionalidad);
	}

	private function notificarNuevo() {
		$noti = Factory::getInstance()->getNotificacion();

		$noti->detalle = 'Nuevo Ticket Nº ' . $this->id . ' de ' . $this->usuario->id;
		$noti->tipoNotificacion = Factory::getInstance()->getTipoNotificacion(self::COD_TIPO_NOTIFICACION);
		$noti->keyObjeto = $this->getPKSerializada();
		$noti->link = $this->linkApp . '?' . $this->getPKSerializada();

		$usus = array();
		foreach ($this->areaEmpresa->usuarios as $uxae) {
			/** @var UsuarioPorAreaEmpresa $uxae */
			$nxu = Factory::getInstance()->getNotificacionPorUsuario();
			$nxu->eliminable = 'S';
			$nxu->id = $uxae->id;
			$usus[$nxu->id] = $nxu;
		}
		$nxu = Factory::getInstance()->getNotificacionPorUsuario();
		$nxu->eliminable = 'S';
		$nxu->id = $this->usuario->id;
		$usus[$nxu->id] = $nxu;
		$noti->usuarios = $usus;
		$noti->guardar();
	}

	private function notificarEdicion() {
		$original = Factory::getInstance()->getKoiTicket($this->id);

		$cambioResponsable = ($this->responsable->id != $original->idResponsable);
		$cambioFechaEstimada = ($this->fechaEstimadaResolucion != $original->fechaEstimadaResolucion);
		$cambioRespuesta = ($this->respuesta != $original->respuesta);
		$resuelto = ($this->estado == self::ESTADO_RESUELTO);
		$rechazado = ($this->estado == self::ESTADO_RECHAZADO);
		$delegado = ($this->estado == self::ESTADO_DELEGADO);

		if ($cambioResponsable || $cambioFechaEstimada || $cambioRespuesta || $resuelto || $rechazado || $delegado) {
			$notificaciones = array();

			if ($resuelto || $rechazado || $delegado) {
				$extra = 'resuelto';
				$rechazado && $extra = 'rechazado';
				if ($delegado) {
					$extra = 'delegado a ' . $this->delegadoA;
					$this->respuesta = 'Delegado a "' . $this->delegadoA . '" - ' . $this->respuesta;
				}
				$notificaciones[] = 'Su ticket Nº ' . $this->id . ' fue ' . $extra;
			} else {
				$prefijo = 'Ticket Nº ' . $this->id;
				if ($cambioResponsable) {
					$notificaciones[] = $prefijo . ' - Se asignó responsable: ' . $this->responsable->id;
				}
				if ($cambioFechaEstimada) {
					$notificaciones[] = $prefijo . ' - Fecha estimada: ' . $this->fechaEstimadaResolucion;
				}
				if ($cambioRespuesta) {
					$notificaciones[] = $prefijo . ' - Hay una respuesta. Click para ver';
				}
			}

			foreach ($notificaciones as $n) {
				$noti = Factory::getInstance()->getNotificacion();
				$noti->detalle = $n;
				$noti->tipoNotificacion = Factory::getInstance()->getTipoNotificacion(self::COD_TIPO_NOTIFICACION);
				$noti->keyObjeto = $this->getPKSerializada();
				$noti->link = $this->linkApp . '?' . $this->getPKSerializada();
				$nxu = Factory::getInstance()->getNotificacionPorUsuario();
				$nxu->eliminable = 'S';
				$nxu->id = $this->usuario->id;
				$noti->usuarios = array($nxu->id => $nxu);
				$noti->guardar();
			}
		}
	}

	//GETS y SETS
	protected function getAreaEmpresa() {
		if (!isset($this->_areaEmpresa)){
			$this->_areaEmpresa = Factory::getInstance()->getAreaEmpresa($this->idAreaEmpresa);
		}
		return $this->_areaEmpresa;
	}
	protected function setAreaEmpresa($areaEmpresa) {
		$this->_areaEmpresa = $areaEmpresa;
		return $this;
	}
	protected function getEstadoNombre() {
		if (!isset($this->_estadoNombre)){
			switch ($this->estado){
				case self::ESTADO_PENDIENTE:
					$this->_estadoNombre = 'Pendiente';
					break;
				case self::ESTADO_RESUELTO:
					$this->_estadoNombre = 'Resuelto';
					break;
				case self::ESTADO_RECHAZADO:
					$this->_estadoNombre = 'Rechazado';
					break;
				case self::ESTADO_DELEGADO:
					$this->_estadoNombre = 'Delegado';
					break;
				default:
					$this->_estadoNombre = 'Pendiente';
					break;
			}
		}
		return $this->_estadoNombre;
	}
	protected function getPrioridadNombre() {
		if (!isset($this->_prioridadNombre)){
			switch ($this->prioridad){
				case '1':
					$this->_prioridadNombre = 'Baja';
					break;
				case '2':
					$this->_prioridadNombre = 'Media';
					break;
				case '3':
					$this->_prioridadNombre = 'Alta';
					break;
				case '4':
					$this->_prioridadNombre = 'Urgente';
					break;
				default:
					$this->_prioridadNombre = 'Baja';
					break;
			}
		}
		return $this->_prioridadNombre;
	}
	protected function getResponsable() {
		if (!isset($this->_responsable)){
			$this->_responsable = Factory::getInstance()->getUsuario($this->idResponsable);
		}
		return $this->_responsable;
	}
	protected function setResponsable($responsable) {
		$this->_responsable = $responsable;
		return $this;
	}
	protected function getTicketOriginal() {
		if (!isset($this->_ticketOriginal)){
			$this->_ticketOriginal = Factory::getInstance()->getKoiTicket($this->idTicketOriginal);
		}
		return $this->_ticketOriginal;
	}
	protected function setTicketOriginal($ticketOriginal) {
		$this->_ticketOriginal = $ticketOriginal;
		return $this;
	}
	protected function getUsuarioCierre() {
		if (!isset($this->_usuarioCierre)){
			$this->_usuarioCierre = Factory::getInstance()->getUsuario($this->idUsuarioCierre);
		}
		return $this->_usuarioCierre;
	}
	protected function setUsuarioCierre($usuarioCierre) {
		$this->_usuarioCierre = $usuarioCierre;
		return $this;
	}
}

?>