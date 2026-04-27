<?php

/**
 * @property Banco|BancoPropio		$banco
 * @property Cliente				$cliente
 * @property Proveedor				$proveedor
 * @property Array					$historia
 * @property Usuario				$usuario
 * @property Caja					$cajaActual
 * @property CuentaBancaria			$cuentaBancaria
 * @property RechazoChequeCabecera	$rechazoCheque
 */

class Cheque extends Importe {
	public		$numero;
	public		$idBanco;
	protected	$_banco;
	public		$idCuentaBancaria;
	public		$_cuentaBancaria;
	public		$idCliente;
	protected	$_cliente;
	public		$idProveedor;
	protected	$_proveedor;
	public		$idRechazoCheque;
	protected	$_rechazoCheque;
	public		$libradorNombre;
	public		$libradorCuit;
	public		$noALaOrden;
	public		$cruzado;
	public		$concluido;
	public		$fechaCreditoDebito;
	public		$fechaEmision;
	public		$fechaVencimiento;
	public		$esperandoEnBanco;
	public		$idCajaActual;
	protected	$_cajaActual;
	public		$diasVencimiento;
	protected	$_historia;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	//public	$nro_documento_destino; (?)

	public function getTipoImporte(){
		return TiposImporte::cheque;
	}

	public function borrar() {
		if($this->esperandoEnBanco || $this->rechazado())
			throw new FactoryExceptionCustomException('El cheque no puede ser anulado porque no se encuentra en cartera');

		return parent::borrar();
	}

	public function expand(){
		/** @noinspection PhpUnusedLocalVariableInspection */
		$uselessVar = $this->banco;
		/** @noinspection PhpUnusedLocalVariableInspection */
		$uselessVar = $this->cuentaBancaria;
		return $this;
	}

	public function rechazado() {
		return isset($this->idRechazoCheque);
	}

	public function noDebitadoAcreditado() {
		return !isset($this->fechaCreditoDebito);
	}

	public function esPropio() {
		return isset($this->idCuentaBancaria);
	}

	public function esDeCliente() {
		return isset($this->idCliente);
	}

	public function entregadoProveedor() {
		return isset($this->idProveedor);
	}

	public function concluido() {
		return $this->concluido == 'S';
	}

	public function noALaOrden() {
		return $this->noALaOrden == 'S';
	}

	public function cruzado() {
		return $this->cruzado == 'S';
	}

	public function esperandoEnBancoDebito() {
		return $this->esperandoEnBanco == 'D';
	}

	public function esperandoEnBancoCredito() {
		return $this->esperandoEnBanco == 'C';
	}

	public function simularComoImporte(){
		$importeCheque = array();
		$importeCheque['banco'] = array();
		$importeCheque['cliente'] = array();
		$importeCheque['cuentabancaria'] = array();

		$importeCheque['id'] = $this->id;
		$importeCheque['banco']['idBanco'] = $this->idBanco;
		$importeCheque['cliente']['id'] = ($this->esDeCliente() ? $this->idCliente : '');
		$importeCheque['cuentabancaria']['id'] = ($this->esPropio() ? $this->cuentaBancaria->id : '');
		$importeCheque['numero'] = $this->numero;
		$importeCheque['importe'] = $this->importe;
		$importeCheque['fechaEmision'] = $this->fechaEmision;
		$importeCheque['fechaVencimiento'] = $this->fechaVencimiento;
		$importeCheque['noALaOrden'] = $this->noALaOrden;
		$importeCheque['cruzado'] = $this->cruzado;
		$importeCheque['libradorCuit'] = $this->libradorCuit;
		$importeCheque['libradorNombre'] = $this->libradorNombre;
		$importeCheque['chequePropio'] = ($this->esPropio() ? 1 : 0);

		return $importeCheque;
	}

	public static function validar($obj) {

		parent::validar($obj);

		$returnObj = Factory::getInstance()->getCheque();
		if (isset($obj['id'])) {
			$returnObj = Factory::getInstance()->getCheque($obj['id']);
			$obj['importe'] = $obj['importe'] ? $obj['importe'] : $returnObj->importe;
			$obj['fechaVencimiento'] = $obj['fechaVencimiento'] ? $obj['fechaVencimiento'] : $returnObj->fechaVencimiento;
			$obj['fechaEmision'] = $obj['fechaEmision'] ? $obj['fechaEmision'] : $returnObj->fechaEmision;
			$obj['importe'] = $obj['importe'] ? $obj['importe'] : $returnObj->importe;
		} else {
			if($obj['chequePropio'] == 1){
				if(!empty($obj['numero'])) {
					$chequeraItem = Factory::getInstance()->getChequeraItem($obj['numero']);
					$chequeraItem->utilizar();
					$obj['numero'] = $chequeraItem->numero;
				}
				$obj['libradorCuit'] = Config::CUIT_SPIRAL;
				$obj['libradorNombre'] = Config::RAZON_SPIRAL;
				$obj['banco'] = array();
				$obj['banco']['idBanco'] = Factory::getInstance()->getCuentaBancaria($obj['cuentaBancaria']['id'])->banco->idBanco;
				$returnObj->idCuentaBancaria = $obj['cuentaBancaria']['id'];
			}
			if (!isset($obj['importe']) || !isset($obj['fechaEmision']) || !isset($obj['fechaVencimiento']) || !isset($obj['banco']['idBanco']) || !isset($obj['numero'])
			|| !isset($obj['noALaOrden']) || !isset($obj['libradorCuit']) || !isset($obj['libradorNombre'])) {
				throw new FactoryExceptionCustomException('No se reconoce el formato de un cheque.');
			}
			$returnObj->id = $obj['id'];
			$returnObj->idUsuario = Usuario::logueado()->id;
		}

		if($obj['importe'] <= 0)
			throw new FactoryExceptionCustomException('Los cheques no pueden tener un importe menor o igual a cero.');

		if(Funciones::esFechaMenor($obj['fechaVencimiento'], $obj['fechaEmision']))
			throw new FactoryExceptionCustomException('Cheque número ' . Funciones::padLeft($obj['numero'], 8, 0) . ': la fecha de emisión no puede ser mayor a la de vencimiento.');

		if(Funciones::diferenciaFechas($obj['fechaVencimiento'], $obj['fechaEmision']) > 365)
			throw new FactoryExceptionCustomException('Cheque número ' . Funciones::padLeft($obj['numero'], 8, 0) . ': la diferencia entre la fecha de vencimiento y de emisión no puede superar 1 año.');

		$obj['importe'] && $returnObj->importe = Funciones::toFloat($obj['importe']);
		$obj['fechaEmision'] && $returnObj->fechaEmision = $obj['fechaEmision'];
		$obj['fechaVencimiento'] && $returnObj->fechaVencimiento = $obj['fechaVencimiento'];
		$obj['banco']['idBanco'] && $returnObj->banco = Factory::getInstance()->getBanco($obj['banco']['idBanco']);
		$obj['cliente']['id'] && $returnObj->cliente = Factory::getInstance()->getCliente($obj['cliente']['id']);
		$obj['numero'] && $returnObj->numero = Funciones::padLeft($obj['numero'], 8, 0);
		$obj['cruzado'] && $returnObj->cruzado = $obj['cruzado'];
		$obj['noALaOrden'] && $returnObj->noALaOrden = $obj['noALaOrden'];
		$obj['libradorCuit'] && $returnObj->libradorCuit = $obj['libradorCuit'];
		$obj['libradorNombre'] && $returnObj->libradorNombre = $obj['libradorNombre'];
		return $returnObj;
	}

	public static function validarExistencia(Caja $caja, $cheques) {
		foreach ($cheques as $idCheque) {
			$cheque = $caja->tieneCheque($idCheque);
			if ($cheque) {
				if ($cheque->rechazado() || $cheque->anulado == 'S') {
					throw new FactoryExceptionCustomException('El cheque número ' . $cheque->numero . ' está anulado o rechazado');
				}
			} else {
				$cheque = Factory::getInstance()->getCheque($idCheque);
				throw new FactoryExceptionCustomException('El cheque número ' . $cheque->numero . ' no pertenece a la caja');
			}
		}
		return true;
	}

	public function getImputacion() {
		/** @noinspection PhpUnusedLocalVariableInspection */
		$return = false;
		if ($this->esPropio()) {
			$return = $this->cuentaBancaria->idImputacion;
		} else {
			$parametro = Factory::getInstance()->getParametroContabilidad(ParametrosContabilidad::valoresADepositar);
			$return = $parametro->idImputacion;
		}
		return $return;
	}

	public function getObservacionContabilidad() {
		return 'Nº de cheque: ' . $this->numero . ' (' . $this->libradorNombre . ')';
	}

	//GETS y SETS
	protected function getBanco() {
		if (!isset($this->_banco)){
			$this->_banco = Factory::getInstance()->getBanco($this->idBanco);
		}
		return $this->_banco;
	}
	protected function setBanco($banco) {
		$this->_banco = $banco;
		return $this;
	}
	protected function getCajaActual() {
		if (!isset($this->_cajaActual)){
			$this->_cajaActual = Factory::getInstance()->getCaja($this->idCajaActual);
		}
		return $this->_cajaActual;
	}
	protected function setCajaActual($cajaActual) {
		$this->_cajaActual = $cajaActual;
		return $this;
	}
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getCliente($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
		return $this;
	}
	protected function getCuentaBancaria() {
		if (!isset($this->_cuentaBancaria)){
			$this->_cuentaBancaria = Factory::getInstance()->getCuentaBancaria($this->idCuentaBancaria);
		}
		return $this->_cuentaBancaria;
	}
	protected function setCuentaBancaria($cuentaBancaria) {
		$this->_cuentaBancaria = $cuentaBancaria;
		return $this;
	}
	protected function getHistoria() {
		if (!isset($this->_historia)){
			$where = 'tipo_importe = ' . Datos::objectToDB($this->getTipoImporte()) . ' AND ';
			$where .= 'cod_importe = ' . Datos::objectToDB($this->id) . ' ';
			$order = 'ORDER BY cod_importe_operacion ASC';
			$ixod = Factory::getInstance()->getListObject('ImportePorOperacionItem', $where . $order);
			$this->_historia = $ixod;
		}
		return $this->_historia;
	}
	protected function setHistoria($historia) {
		$this->_historia = $historia;
		return $this;
	}
	protected function getProveedor() {
		if (!isset($this->_proveedor)){
			$this->_proveedor = Factory::getInstance()->getProveedor($this->idProveedor);
		}
		return $this->_proveedor;
	}
	protected function setProveedor($proveedor) {
		$this->_proveedor = $proveedor;
		return $this;
	}
	protected function getRechazoCheque() {
		if (!isset($this->_rechazoCheque)){
			$this->_rechazoCheque = Factory::getInstance()->getRechazoChequeCabecera($this->idRechazoCheque, $this->empresa);
		}
		return $this->_rechazoCheque;
	}
	protected function setRechazoCheque($rechazoCheque) {
		$this->_rechazoCheque = $rechazoCheque;
		return $this;
	}
	protected function getUsuario() {
		if (!isset($this->_usuario)){
			$this->_usuario = Factory::getInstance()->getUsuario($this->idUsuario);
		}
		return $this->_usuario;
	}
	protected function setUsuario($usuario) {
		$this->_usuario = $usuario;
		return $this;
	}
	private function getDocumento($importePorOperacionItem){
		/** @var ImportePorOperacionItem $importePorOperacionItem */
		$ultimoImportePorOperacion = $importePorOperacionItem->importePorOperacion;
		$nombreClase = $ultimoImportePorOperacion->getNombreClaseDocumento();

		$documentos = Factory::getInstance()->getListObject($nombreClase, 'cod_importe_operacion =' . Datos::objectToDB($ultimoImportePorOperacion));

		return $documentos[0];
	}
	public function getPrimerDocumento(){
		$historia = $this->getHistoria();
		$importePorOperacionItem = $historia[count($historia) - 1];
		return $this->getDocumento($importePorOperacionItem);
	}
	public function getUltimoDocumento(){
		$historia = $this->getHistoria();
		$importePorOperacionItem = $historia[0];
		return $this->getDocumento($importePorOperacionItem);
	}
}

?>