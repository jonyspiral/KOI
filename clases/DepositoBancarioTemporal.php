<?php

/**
 * @property Usuario								$usuario
 * @property Caja									$caja
 * @property CuentaBancaria							$cuentaBancaria
 * @property Array									$cheques
 * @property FormularioDepositoBancarioTemporal		$formulario
 */

class DepositoBancarioTemporal extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idCaja;
	protected	$_caja;
	public		$idCuentaBancaria;
	protected	$_cuentaBancaria;
	public		$fecha;
	public		$ventaCheque;
	public		$numeroBoleta;
	public		$efectivo;
	public		$idCheques;
	protected	$_cheques;
	public		$chequesNuevos;
	public		$idUsuario;
	protected	$_usuario;
	public		$confirmado;
	private		$formulario;
	public		$anulado;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;
	public		$idUsuarioBaja;
	public		$idUsuarioUltimaMod;

	public function addCheque($cheque) {
		$this->chequesNuevos[] = $cheque;
	}

	public function confirmar($funcionalidad = false) {
		$this->confirmado = 'S';
		parent::guardar();
	}

	public function esVentaCheque() {
		return $this->ventaCheque == 'S';
	}

	public function guardar(){
		$arrayAuxCheques = array();
		$posta = array();

		foreach($this->cheques as $cheque){
			$cheque->concluido = 'N';
			$arrayAuxCheques[$cheque->id] = $cheque;
		}

		foreach($this->chequesNuevos as $cheque){
			/** @var Cheque	$cheque */
			if(!isset($arrayAuxCheques[$cheque->id])){
				$arrayAuxCheques[$cheque->id] = $cheque;
			}
			$posta[] = clone($cheque);
			$arrayAuxCheques[$cheque->id]->concluido = 'S';
		}

		foreach($arrayAuxCheques as $cheque){
			/** @var Cheque	$cheque */
			$cheque->guardar();
		}

		$this->cheques = $posta;

		$this->validarCheques();
		$this->validarCampos();

		$arrayCheques = array();
		foreach($this->cheques as $cheque){
			/** @var Cheque	$cheque */
			$arrayCheques[] = $cheque->id;
		}

		$this->idCheques = json_encode($arrayCheques);
		return parent::guardar();
	}

	public function borrar() {
		foreach($this->cheques as $cheque){
			/** @var Cheque	$cheque */
			$cheque->concluido = 'N';
			$cheque->guardar();
		}

		return parent::borrar();
	}

	public function revertirEstadoCheques(){
		foreach($this->cheques as $cheque){
			/** @var Cheque	$cheque */
			$cheque->esperandoEnBanco = null;
			$cheque->guardar();
		}
	}

	private function validarCheques(){
		foreach($this->cheques as $cheque){
			/** @var Cheque $cheque */
			$verdaderaFechaDeVencimiento = Funciones::sumarTiempo($cheque->fechaVencimiento, 1, 'months', 'd-m-Y');
			$fechaDeHoy = Funciones::hoy('d-m-Y');

			if($this->caja->id != $cheque->cajaActual->id)
				throw new FactoryExceptionCustomException('No puede utilizar cheques de diferentes cajas.');

			if(Funciones::esFechaMenor($verdaderaFechaDeVencimiento, $fechaDeHoy))
				throw new FactoryExceptionCustomException('El cheque Nº ' . $cheque->numero . ' no puede depositarse por estar vencido');

			if($cheque->esPropio())
				throw new FactoryExceptionCustomException('El cheque Nº ' . $cheque->numero . ' no puede depositarse por ser propio');

			if($cheque->rechazado())
				throw new FactoryExceptionCustomException('El cheque Nº ' . $cheque->numero . ' no puede depositarse por estar rechazado');

			if($cheque->anulado())
				throw new FactoryExceptionCustomException('El cheque Nº ' . $cheque->numero . ' no puede depositarse por estar anulado');

			if($cheque->concluido() && !(in_array($cheque->id, json_decode($this->idCheques))))
				throw new FactoryExceptionCustomException('El cheque Nº ' . $cheque->numero . ' no puede depositarse por estar concluido');
		}
	}

	private function validarCampos(){
		if($this->esVentaCheque()){
			if(count($this->cheques) == 0)
				throw new FactoryExceptionCustomException('No puede realizar una venta de cheques sin seleccionar cheques.');
		}else{
			if(empty($this->efectivo) && count($this->cheques) == 0)
				throw new FactoryExceptionCustomException('El deposito bancario debe tener al menos un cheque y/o un importe en efectivo.');

			if(count($this->cheques) > 6)
				throw new FactoryExceptionCustomException('El deposito bancario puede tener un máximo de 6 cheques.');

			if(is_null($this->numeroBoleta))
				throw new FactoryExceptionCustomException('Complete todos los campos obligatorios.');

			if($this->efectivo < 0)
				throw new FactoryExceptionCustomException('No se puede ingresar un importe en efectivo negativo.');

			if($this->numeroBoleta < 0 || is_float($this->numeroBoleta))
				throw new FactoryExceptionCustomException('Formato de número de boleta incorrecto');

			if($this->caja->importeEfectivoFinal + abs($this->caja->importeDescubierto) < $this->efectivo)
				throw new FactoryExceptionCustomException('No hay efectivo suficiente en caja para realizar el deposito.');
		}
	}

	//formulario
	public function abrir() {
		$this->comprobaciones();
		$this->crearFormulario();
		$this->llenarFormulario();
		$this->formulario->abrir();
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioDepositoBancarioTemporal();
	}

	protected function llenarFormulario() {
		$this->formulario->id = $this->id;
		$this->formulario->caja = $this->caja;
		$this->formulario->cuentaBancaria = $this->cuentaBancaria;
		$this->formulario->fecha = $this->fecha;
		$this->formulario->esVentaCheque = $this->esVentaCheque();
		$this->formulario->numeroBoleta = $this->numeroBoleta;
		$this->formulario->efectivo = $this->efectivo;
		$this->formulario->cheques = $this->cheques;
		$this->formulario->esDepositoTemporal = true;
	}

	protected function comprobaciones() {
		if($this->anulado())
			throw new FactoryExceptionCustomException('No se puede generar porque el registro fue anulado');

		if($this->confirmado())
			throw new FactoryExceptionCustomException('No se puede generar porque el registro ya fue confirmado');
	}

	public function confirmado() {
		return $this->confirmado == 'S';
	}

	//GETS Y SETS
	protected function getCaja() {
		if (!isset($this->_caja)){
			$this->_caja = Factory::getInstance()->getCaja($this->idCaja);
		}
		return $this->_caja;
	}
	protected function setCaja($caja) {
		$this->_caja = $caja;
		return $this;
	}

	public function getCheques() {
		if (!isset($this->_cheques)){
			$this->_cheques = array();
			$arrayCheques = json_decode($this->idCheques);
			foreach($arrayCheques as $idCheque){
				$this->_cheques[] = Factory::getInstance()->getCheque($idCheque);
			}
		}
		return $this->_cheques;
	}
	protected function setCheques($cheques) {
		$this->_cheques = $cheques;
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
}

?>