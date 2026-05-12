<?php

/**
 * @property array				$detalle
 * @property array				$detalleJson
 * @property Usuario			$usuario
 * @property Usuario			$usuarioBaja
 * @property Usuario			$usuarioUltimaMod
 * @property EjercicioContable	$ejercicioContable
 */

class AsientoContable extends Base {
	const		AJUSTE = 0.011;
	const		_primaryKey = '["id"]';

	public		$id;
	public		$empresa;
	public		$nombre;
	public		$fecha;
	public		$importe;
	public		$idEjercicioContable;
	protected	$_ejercicioContable;
	protected	$_detalle;
	protected	$_detalleJson;
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

	public function loadDetalleJson($json = array()) {
		$i = 1;
		$filas = array();
		$importeTotal = 0;
		foreach($json as $f){
			$fila = Factory::getInstance()->getFilaAsientoContable();
			$fila->numeroFila = $i;
			$fila->imputacion = Factory::getInstance()->getImputacion($f['imputacion']);
			$fila->importeDebe = Funciones::formatearDecimales($f['importeDebe'], 2, '.');
			$fila->importeHaber = Funciones::formatearDecimales($f['importeHaber'], 2, '.');
			$fila->fechaVencimiento = $f['fechaVencimiento'];
			$fila->observaciones = $f['observaciones'];
			$filas[] = $fila;
			$importeTotal += $fila->importeDebe;
			$i++;
		}
		$this->_detalle = $filas;
		$this->importe = $importeTotal;
	}

	public function guardar() {
		$this->validar();
		return parent::guardar();
	}

	public function borrar() {
		foreach ($this->detalle as $d) {
			/** @var FilaAsientoContable $d */
			Factory::getInstance()->marcarParaBorrar($d);
		}
		return parent::borrar();
	}

	private function validar() {
		if (count($this->detalle) < 2) {
			throw new FactoryExceptionCustomException('Un asiento contable deberá tener al menos 2 (dos) filas');
		}
		$cantDebe = 0;
		$cantHaber = 0;
		$importeDebe = 0;
		$importeHaber = 0;
		foreach ($this->detalle as $d) {
			/** @var FilaAsientoContable $d */
			$d->importeDebe > 0 && $cantDebe++;
			$d->importeHaber > 0 && $cantHaber++;
			$importeDebe += $d->importeDebe;
			$importeHaber += $d->importeHaber;
		}
		$dif = (Funciones::toFloat($importeDebe, 2) - Funciones::toFloat($importeHaber, 2));
		if ($dif != 0) {
			if (abs($dif) > self::AJUSTE) {
				throw new FactoryExceptionCustomException('La suma de importes en la columna "Debe" tiene que ser igual a la suma de la columna "Haber"');
			}
			$this->ajustar($dif, ($cantDebe <= $cantHaber));
		}
	}

	private function ajustar($dif, $ajustarDebe) {
		//Si $dif < 0, entonces Debe es menor que Haber
		//Si $ajustarDebe es TRUE, entonces deberá ajustarse el Debe. Si no, el haber
		$importeDebe = 0;
		$importeHaber = 0;
		$ajustado = false;
		foreach ($this->detalle as $d) {
			/** @var FilaAsientoContable $d */
			if (!$ajustado) {
				if ($ajustarDebe && $d->importeDebe) {
					$d->importeDebe -= $dif;
					$ajustado = true;
				}
				if (!$ajustarDebe && $d->importeHaber) {
					$d->importeHaber += $dif;
					$ajustado = true;
				}
			}
			$importeDebe += $d->importeDebe;
			$importeHaber += $d->importeHaber;
		}
		$this->importe = $importeDebe;
	}

	//GETS y SETS
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->id)){
			$this->_detalle = Factory::getInstance()->getListObject('FilaAsientoContable', 'cod_asiento = ' . Datos::objectToDB($this->id) . ' ');
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getDetalleJson() {
		if (!isset($this->_detalleJson)){
			$this->_detalleJson = array();
			foreach ($this->detalle as $f) {
				/** @var FilaAsientoContable $f */
				$fila = array();
				$fila['numeroFila'] = $f->numeroFila;
				$fila['imputacion'] = array('id' => $f->imputacion->id, 'nombre' => $f->imputacion->nombre);
				$fila['importeDebe'] = $f->importeDebe;
				$fila['importeHaber'] = $f->importeHaber;
				$fila['fechaVencimiento'] = $f->fechaVencimiento;
				$fila['observaciones'] = $f->observaciones;
				$this->_detalleJson[] = $fila;
			}
		}
		return $this->_detalleJson;
	}
	protected function setDetalleJson($detalleJson) {
		$this->_detalleJson = $detalleJson;
		return $this;
	}
	protected function getEjercicioContable() {
		if (!isset($this->_ejercicioContable)){
			$this->_ejercicioContable = Factory::getInstance()->getEjercicioContable($this->idEjercicioContable);
		}
		return $this->_ejercicioContable;
	}
	protected function setEjercicioContable($ejercicioContable) {
		$this->_ejercicioContable = $ejercicioContable;
		return $this;
	}
}

?>