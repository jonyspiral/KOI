<?php

/**
 * @property TareaProduccionItem	$tareaProduccionItem
 * @property Usuario				$usuario
 * @property Usuario				$usuarioBaja
 * @property Usuario				$usuarioUltimaMod
 */

class ConfirmacionStock extends Base implements OperacionStock {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idOrdenDeFabricacion;
	public		$numeroTarea;
	public		$idSeccionProduccion;
	protected	$_tareaProduccionItem;
	public		$cantidadTotal;
	public		$cantidad;				//Array de 1 a 10
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$fechaAlta;
	public		$fechaBaja;

	public function guardar() {
		$mutex = new Mutex(Funciones::getType($this) . '_verificaciones');
		try {
			$mutex->lock();
			Factory::getInstance()->beginTransaction();

			$tareaActual = Factory::getInstance()->getTareaProduccionItem($this->tareaProduccionItem->idOrdenDeFabricacion, $this->tareaProduccionItem->numeroTarea, $this->tareaProduccionItem->idSeccionProduccion);
			for ($i = 1; $i <= 10; $i++) {
				if ($this->cantidad[$i] > $tareaActual->pendiente[$i]) {
					throw new FactoryExceptionCustomException('La cantidad pendiente de la tarea en la posición ' . $i . ' (' . ($tareaActual->pendiente[$i]) . ') no es menor que la cantidad que se quiere confirmar (' . $this->cantidad[$i] . ')');
				}
				$tareaActual->pendiente[$i] -= $this->cantidad[$i];
			}
			$tareaActual->pendienteTotal = Funciones::sumaArray($tareaActual->pendiente);
			$tareaActual->guardar();

			for ($i = 1; $i <= 10; $i++) {
				$this->cantidad[$i] = Funciones::toInt($this->cantidad[$i]);
			}
			parent::guardar(); //Lo hago primero por el ID (para el $keyObjeto)
			$this->stock();

			Factory::getInstance()->commitTransaction();
			$mutex->unlock();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			$mutex->unlock();
			throw $ex;
		}

		return $this;
	}

	public function borrar() {
		$mutex = new Mutex(Funciones::getType($this) . '_verificaciones');
		try {
			$mutex->lock();
			Factory::getInstance()->beginTransaction();

			$tareaActual = Factory::getInstance()->getTareaProduccionItem($this->tareaProduccionItem->idOrdenDeFabricacion, $this->tareaProduccionItem->numeroTarea, $this->tareaProduccionItem->idSeccionProduccion);
			for ($i = 1; $i <= 10; $i++) {
				if (($this->cantidad[$i] + $tareaActual->pendiente[$i]) > $tareaActual->cantidad[$i]) {
					throw new FactoryExceptionCustomException('La cantidad que se intenta restablecer excede el total original de la tarea. Por favor, actualice la página e inténtelo nuevamente');
				}
				$tareaActual->pendiente[$i] += $this->cantidad[$i];
			}
			$tareaActual->pendienteTotal = Funciones::sumaArray($tareaActual->pendiente);
			$tareaActual->guardar();

			parent::borrar();
			$this->stock();

			Factory::getInstance()->commitTransaction();
			$mutex->unlock();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			$mutex->unlock();
			throw $ex;
		}

		return $this;
	}

	protected function validarGuardar() {
		if (!Funciones::sumaArray($this->cantidad)) {
			throw new FactoryExceptionCustomException('No puede hacer una confirmación vacía (todas las columnas están en cero). Por favor, actualice la página e inténtelo nuevamente');
		}
	}

	/************************************** STOCK **************************************/

	public function stock() {
		return Stock::registrarOperacion($this);
	}

	public function stockTipoMovimiento() {
		return ($this->modo == Modos::delete) ? TiposMovimientoStock::negativo : TiposMovimientoStock::positivo;
	}

	public function stockTipoOperacion() {
		return TiposOperacionStock::confirmacionStock;
	}

	public function stockKeyObjeto() {
		return $this->getPKSerializada();
	}

	public function stockObservacion() {
		return 'Tarea Nº ' . $this->tareaProduccionItem->idOrdenDeFabricacion . '-' . $this->tareaProduccionItem->numeroTarea . ' | Conf. Nº ' . $this->id;
	}

	public function stockDetalle() {
		return array(
			$this->tareaProduccionItem->almacen->id => array(
				$this->tareaProduccionItem->articulo->id => array(
					$this->tareaProduccionItem->colorPorArticulo->id => $this->cantidad
				)
			)
		);
	}

	/************************************** ***** **************************************/

	//GETS y SETS
	protected function getTareaProduccionItem() {
		if (!isset($this->_tareaProduccionItem)){
			$this->_tareaProduccionItem = Factory::getInstance()->getTareaProduccionItem($this->idOrdenDeFabricacion, $this->numeroTarea, $this->idSeccionProduccion);
		}
		return $this->_tareaProduccionItem;
	}
	protected function setTareaProduccionItem($tareaProduccionItem) {
		$this->_tareaProduccionItem = $tareaProduccionItem;
		return $this;
	}
}

?>