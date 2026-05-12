<?php

/**
 * @property Ecommerce_OrderStatus		$statusAnterior
 * @property Ecommerce_OrderStatus		$proximoStatus
 * @property Ecommerce_OrderStatus[]	$dependencias
 * @property Usuario					$usuario
 * @property Usuario					$usuarioBaja
 * @property Usuario					$usuarioUltimaMod
 */
class Ecommerce_OrderStatus extends Base {
	const		_primaryKey = '["id"]';
	const		DOCUMENT_FILE_NAME = 'getPdf.php';

	public		$id;
	public		$nombre;
	public		$mostrarEnPanel;
	public		$idStatusAnterior;
	protected	$_statusAnterior;
	public		$idProximoStatus;
	protected	$_proximoStatus;
	public		$idDependencias;
	protected	$_dependencias;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	private static $idNombreMapper = array(
		'1'		=> 'Cobrado',
		'2'		=> 'Pedido',
		'3'		=> 'Predespachado',
		'4'		=> 'Despachado',
		'5'		=> 'Remitido',
		'6'		=> 'Facturado',
		'7'		=> 'FacturadoCae',
		'8'		=> 'EnTransito',
		'9'		=> 'Finalizado',
		'10'	=> 'PendienteDeCambio',
		'11'	=> 'PendienteDeDevolucion'
	);

	public static function forge($idStatus) {
		//También se podría hacer pidiendo un OrderStatus común y después casteando
		$nombreMetodoGetStatus = 'getEcommerce_OrderStatus' . (isset(self::$idNombreMapper[$idStatus]) ? '_' . self::$idNombreMapper[$idStatus] : '');
		$status = Factory::getInstance()->$nombreMetodoGetStatus($idStatus);
		return $status;
	}

	public function esReversible() {
		return isset($this->idStatusAnterior);
	}

	public function tieneProximoStatus() {
		return !is_null($this->idProximoStatus);
	}

	public final function procesar(Ecommerce_Order &$order) {
		//No puedo preguntar acá si tieneProximoStatus ya que a veces necesito procesar uno que no tiene proximoStatus (por dependencias)
		$this->cumplirDependencias($order);
		$this->procesarEsteStatus($order);
		$order->registrarStatusProcesado($this);
	}

	protected function cumplirDependencias(&$order) {
		foreach (explode(',', $this->idDependencias) as $idDep) {
			if (!$order->tieneDependenciaCumplida($idDep)) {
				$statusNoCumplido = Ecommerce_OrderStatus::forge($idDep);
				$statusNoCumplido->procesar($order);
			}
		}
	}
	protected function procesarEsteStatus(Ecommerce_Order &$order) {}

	public function desprocesar(Ecommerce_Order &$order) {
		$this->desprocesarEsteStatus($order);
		$order->registrarStatusDesprocesado($this);
	}
	protected function desprocesarEsteStatus(Ecommerce_Order &$order) {}

	public function getDocumentLinkObject(Ecommerce_Order $order) {
		return false;
	}

	protected function getDocumentBaseUrl() {
		//return Config::urlBase . 'content/';
		return '/content/';
	}

	//GETS y SETS
	protected function getProximoStatus() {
		if (!isset($this->_proximoStatus)){
			//Hago esto para que el próximo status sea de la clase que corresponde
			$this->_proximoStatus = self::forge($this->idProximoStatus);
		}
		return $this->_proximoStatus;
	}
	protected function setProximoStatus($proximoStatus) {
		$this->_proximoStatus = $proximoStatus;
		return $this;
	}
	protected function getStatusAnterior() {
		if (!isset($this->_statusAnterior)){
			//Hago esto para que el próximo status sea de la clase que corresponde
			$this->_statusAnterior = self::forge($this->idStatusAnterior);
		}
		return $this->_statusAnterior;
	}
	protected function setStatusAnterior($statusAnterior) {
		$this->_statusAnterior = $statusAnterior;
		return $this;
	}
	protected function getDependencias() {
		if (!isset($this->_dependencias)){
			$this->_dependencias = array();
			foreach (explode(',', $this->idDependencias) as $idDependencia) {
				$this->_dependencias[] = Factory::getInstance()->getEcommerce_OrderStatus($idDependencia);
			}
		}
		return $this->_dependencias;
	}
	protected function setDependenciasCumplidas($dependencias) {
		$this->_dependencias = $dependencias;

		//Serializo las dependencias cumplidas en el campo $idDependencias
		$deps = array();
		foreach ($dependencias as $dep) {
			$deps[] = $dep->id;
		}
		$this->idDependencias = implode(',', $deps);

		return $this;
	}
}

?>