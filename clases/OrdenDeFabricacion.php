<?php

/**
 * @property LoteDeProduccion			$loteDeProduccion
 * @property Articulo					$articulo
 * @property ColorPorArticulo			$colorPorArticulo
 * @property Patron						$patron
 * @property TareaProduccion[]			$tareas
 * @property CurvaProduccionPorArticulo	$curvaDeProduccion
 */

class OrdenDeFabricacion extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$idLoteDeProduccion;
	protected	$_loteDeProduccion; //Lote
    public		$tipoOrden;			//"P" o "A" (Plan de produccion o Libre)
	public		$idArticulo;
	protected	$_articulo;
	public		$idColorPorArticulo;
	protected	$_colorPorArticulo;
	public		$version;
    protected	$_patron;
	public		$confirmada;        //"S" o "N"
    public		$idCurvaDeProduccion;
    protected	$_curvaDeProduccion;
    public		$cantidadOptimaProduccion;
	public		$cantidadTotal;
	public		$cantidad;			//Array de 1 a 10
	protected	$_tareas;
    public		$anulado;
	public		$fechaInicio;
	public		$fechaFin;

	public		$lanzar;            // Es un atributo auxiliar y temporal para el controller

    public function confirmada() {
        return $this->confirmada == 'S';
    }

    public function lanzar() {
        Factory::getInstance()->beginTransaction();
        try {
            if (!$this->articulo->idRutaProduccion) {
                throw new FactoryExceptionCustomException('Para lanzar las tareas del artículo ' . $this->articulo->getIdNombre() . ' deberá configurarle una ruta');
            }
            // Armo un detalle modelo según el artículo
            $detalleModelo = array();
            foreach ($this->articulo->rutaProduccion->pasos as $paso) {
                $tareaDetalle = Factory::getInstance()->getTareaProduccionItem();
                $tareaDetalle->seccionProduccion = $paso->seccionProduccion;
                $tareaDetalle->ejecucion = $paso->ejecucion;
                $tareaDetalle->numeroPaso = $paso->nroPaso;
                $tareaDetalle->subPaso = $paso->nroSubPaso;
                $tareaDetalle->fechaEntradaProgramada = Funciones::hoy();
                $tareaDetalle->duracionPaso = $paso->duracion;
                $tareaDetalle->cumplidoPaso = 'N';
                $tareaDetalle->entradaConfirmada = 'N';
                $tareaDetalle->rendido = 'N';

                $detalleModelo[] = $tareaDetalle;
            }

            // Inicializo contadores
            $totalAux = $this->cantidadTotal;
            $cantidadAux = $this->cantidad;
            $nroTarea = 1;
            while ($totalAux > 0) {
                $tarea = Factory::getInstance()->getTareaProduccion();
                $tarea->ordenDeFabricacion = $this;
                $tarea->numero = $nroTarea;
                $tarea->situacion = 'P';
                $tarea->impresa = 'N';
                $tarea->cantidadModulos = 1;
                $tarea->ultimoPasoCumplido = 0;
                $tarea->fechaProgramacion = Funciones::getDate();

                $cAuxs = array();
                for ($i = 1; $i <= 10; $i++) {
                    $c = $totalAux > $this->cantidadOptimaProduccion ? $this->curvaDeProduccion->cantidad[$i] : $cantidadAux[$i];
                    if ($c > $cantidadAux[$i]) {
                        throw new FactoryExceptionCustomException('Hay un error con las cantidades de la orden de producción Nº ' . $this->id . '. Por favor, edite la orden y vuelva a intentarlo');
                    }
                    $cAuxs[$i] = $c;
                    $cantidadAux[$i] -= $c;
                }
                $tarea->cantidad = $cAuxs;

                // Agrego el detalle de la tarea y mando a guardar
                $tarea->detalle = $detalleModelo;
                $tarea->guardar();

                // Finalizo la vuelta
                $totalAux -= $tarea->cantidadTotal;
                $nroTarea++;
            }

            $this->confirmada = 'S';
            $this->guardar();

            Factory::getInstance()->commitTransaction();
        } catch (Exception $ex) {
            Factory::getInstance()->rollbackTransaction();
            throw $ex;
        }
    }

	//GETS y SETS
	protected function getArticulo() {
		if (!isset($this->_articulo)){
			$this->_articulo = Factory::getInstance()->getArticulo($this->idArticulo);
		}
		return $this->_articulo;
	}
	protected function setArticulo($articulo) {
		$this->_articulo = $articulo;
		return $this;
	}
	protected function getColorPorArticulo() {
		if (!isset($this->_colorPorArticulo)){
			$this->_colorPorArticulo = Factory::getInstance()->getColorPorArticulo($this->idArticulo, $this->idColorPorArticulo);
		}
		return $this->_colorPorArticulo;
	}
	protected function setColorPorArticulo($colorPorArticulo) {
		$this->_colorPorArticulo = $colorPorArticulo;
		return $this;
	}
	protected function getCurvaDeProduccion() {
		if (!isset($this->_curvaDeProduccion)){
			$this->_curvaDeProduccion = Factory::getInstance()->getCurvaProduccionPorArticulo($this->idCurvaDeProduccion);
		}
		return $this->_curvaDeProduccion;
	}
	protected function setCurvaDeProduccion($curvaDeProduccion) {
		$this->_curvaDeProduccion = $curvaDeProduccion;
		return $this;
	}
    protected function getPatron() {
        if (!isset($this->_patron)){
            $this->_patron = Factory::getInstance()->getPatron($this->idArticulo, $this->idColorPorArticulo, $this->version);
        }
        return $this->_patron;
    }
    protected function setPatron($patron) {
        $this->_patron = $patron;
        return $this;
    }
	protected function getLoteDeProduccion() {
		if (!isset($this->_loteDeProduccion)){
			$this->_loteDeProduccion = Factory::getInstance()->getLoteDeProduccion($this->idLoteDeProduccion);
		}
		return $this->_loteDeProduccion;
	}
	protected function setLoteDeProduccion($loteDeProduccion) {
		$this->_loteDeProduccion = $loteDeProduccion;
		return $this;
	}
    protected function getTareas() {
        if (!isset($this->_tareas) && $this->confirmada == 'S' && $this->id) {
            $where = 'nro_orden_fabricacion = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N') . ' ORDER BY nro_tarea';
            $this->_tareas = Factory::getInstance()->getListObject('TareaProduccion', $where);
        }
        return $this->_tareas;
    }
    protected function setTareas($tareas) {
        $this->_tareas = $tareas;
        return $this;
    }
}

?>