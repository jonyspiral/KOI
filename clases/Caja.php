<?php

/**
 * @property Imputacion							$imputacion
 * @property Usuario							$responsable
 * @property Caja								$cajaPadre
 * @property PermisoPorUsuarioPorCaja[]			$permisos
 * @property CajaPosiblesTransferenciaInterna[]	$cajasPosiblesTransferenciaInterna
 * @property array								$cheques
 * @property float								$importeCheques
 * @property float								$importeEfectivo
 * @property float								$importeEfectivoFinal
 * @property float								$importeGastitos
 */

class Caja extends Base {
	public		$id;
	public		$nombre;
	public		$fechaLimite;
	public		$diasCierre;
	public		$anulado;
	public		$importeDescubierto;
	public		$importeMaximo;
	public		$idImputacion;
	protected	$_imputacion;
	public		$idCajaPadre;
	protected	$_cajaPadre;
	protected 	$_permisos;
	protected 	$_cajasPosiblesTransferenciaInterna;
	public		$idResponsable;
	public		$esCajaBanco;
	protected	$_responsable; //dueño de la caja
	protected	$_importeEfectivo;
	protected	$_importeEfectivoFinal;
	protected	$_importeGastitos;
	protected	$_importeCheques;
	protected	$_cheques;
	public		$dispParaNegociar;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;


	/**
	 * @param $idCheque
	 *
	 * @return Cheque|bool
	 */
	public function tieneCheque($idCheque) {
		foreach ($this->cheques as $cheque) {
			if ($cheque->id == $idCheque) {
				return $cheque;
			}
		}
		return false;
	}

	public function esUsuario($usuario) {
		try {
			Factory::getInstance()->getPermisoPorUsuarioPorCaja($this->id, $usuario->id, PermisosUsuarioPorCaja::verCaja);
			return true;
		} catch(Exception $ex) {
			return false;
		}
	}

	public function esCajaBanco() {
		return ($this->esCajaBanco == 'S');
	}

	public function usuarioPuede($idUsuario, $permiso) {
		try {
			Factory::getInstance()->getPermisoPorUsuarioPorCaja($this->id, $idUsuario, $permiso);
			return true;
		} catch (FactoryExceptionRegistroNoExistente $ex) {
			return false;
		}
	}

	//GETS y SETS
	protected function getCajaPadre() {
		if (!isset($this->_cajaPadre)){
			$this->_cajaPadre = Factory::getInstance()->getCaja($this->idCajaPadre);
		}
		return $this->_cajaPadre;
	}
	protected function setCajaPadre($cajaPadre) {
		$this->_cajaPadre = $cajaPadre;
		return $this;
	}
	protected function getCajasPosiblesTransferenciaInterna() {
		if (!isset($this->_cajasPosiblesTransferenciaInterna) && isset($this->id)) {
			$this->_cajasPosiblesTransferenciaInterna = Factory::getInstance()->getListObject('CajaPosiblesTransferenciaInterna', 'cod_caja_salida = ' . Datos::objectToDB($this->id));
		}
		return $this->_cajasPosiblesTransferenciaInterna;
	}
	protected function setCajasPosiblesTransferenciaInterna($cajasPosiblesTransferenciaInterna) {
		$this->_cajasPosiblesTransferenciaInterna = $cajasPosiblesTransferenciaInterna;
		return $this;
	}
	protected function getCheques() {
		if (!isset($this->_cheques)){
			$this->_cheques = Factory::getInstance()->getListObject('Cheque', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_caja_actual = ' . Datos::objectToDB($this->id));
		}
		return $this->_cheques;
	}
	protected function setCheques($cheques) {
		$this->_cheques = $cheques;
		return $this;
	}
	protected function getImporteCheques() {
		if (!isset($this->_importeCheques)){
			$this->_importeCheques = 0;
			foreach ($this->getCheques() as $cheque) {
				$this->_importeCheques += $cheque->importe;
			}
		}
		return $this->_importeCheques;
	}
	protected function setImporteCheques($importeCheques) {
		$this->_importeCheques = $importeCheques;
		return $this;
	}
	protected function getImporteEfectivo() {
		if (!isset($this->_importeEfectivo)){
			$this->_importeEfectivo = 0;
		}
		return $this->_importeEfectivo;
	}
	protected function setImporteEfectivo($importeEfectivo) {
		if (abs($importeEfectivo) < 0.01) {
			$importeEfectivo = 0;
		}
		if ($importeEfectivo > $this->importeMaximo) {
			throw new FactoryExceptionCustomException('No se puede superar el monto máximo de la caja (' . Funciones::formatearMoneda($this->importeMaximo) . ')');
		}
		if ($importeEfectivo < 0 && abs($importeEfectivo) > $this->importeDescubierto) {
			throw new FactoryExceptionCustomException('No se puede operar por debajo del descubierto (' . Funciones::formatearMoneda(abs($this->importeDescubierto)) . ')');
		}
		$this->_importeEfectivo = $importeEfectivo;
		return $this;
	}
	protected function getImporteEfectivoFinal() {
		return $this->_importeEfectivo - $this->_importeGastitos;
	}
	/*
	protected function setImporteEfectivoFinal($importeEfectivoFinal) {
		//Sin setter, este campo no es editable
	}
	*/
	protected function getImporteGastitos() {
		return $this->_importeGastitos;
	}
	protected function setImporteGastitos($importeGastitos) {
		if (isset($this->_importeGastitos)) {
			throw new FactoryExceptionCustomException('Error interno del sistema: no se puede cambiar el valor del campo "importeGastitos" de la clase "Caja"');
		}
		$this->_importeGastitos = $importeGastitos;
		return $this;
	}
	protected function getImputacion() {
		if (!isset($this->_imputacion)){
			$this->_imputacion = Factory::getInstance()->getImputacion($this->idImputacion);
		}
		return $this->_imputacion;
	}
	protected function setImputacion($imputacion) {
		$this->_imputacion = $imputacion;
		return $this;
	}
	protected function getPermisos() {
		if (!isset($this->_permisos) && isset($this->id)){
			$this->_permisos = Factory::getInstance()->getListObject('PermisoPorUsuarioPorCaja', 'cod_caja = ' . Datos::objectToDB($this->id));
		}
		return $this->_permisos;
	}
	protected function setPermisos($permisos) {
		$this->_permisos = $permisos;
		return $this;
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
}
