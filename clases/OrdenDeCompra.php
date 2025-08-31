<?php

/**
 * @property Almacen					$almacen
 * @property LoteDeProduccion			$loteDeProduccion
 * @property Proveedor					$proveedor
 * @property Usuario					$usuario
 * @property OrdenDeCompraItem[]		$detalle
 * @property Float						$importePendiente
 * @property Float						$importeTotal
 * @property FormularioOrdenDeCompra	$formulario
 */

class OrdenDeCompra extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$codSucursal;
	public		$numero;
	protected	$_nroDocumentoCompleto;
	public		$idProveedor;
	protected	$_proveedor;
	public		$fechaEmision;
	public		$idAlmacen;
	protected	$_almacen;
	public		$usaRango;
	public		$idLoteDeProduccion;
	protected	$_loteDeProduccion;
	protected	$_detalle;
	public		$observaciones;
	protected	$_importePendiente;
	protected	$_importeTotal;
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
	public		$formulario;
	public		$esHexagono;

	protected function validarGuardar() {
		foreach ($this->detalle as $item) {
			if ($item->precioUnitario <= 0){
				throw new FactoryExceptionCustomException('Los precios no pueden ser negativos ni cero');
			}

			for($i = 1; $i < 11; $i++){
				if(!(!empty($item->cantidades[$i]) || $item->precios[$i] <= 0)){
					throw new FactoryExceptionCustomException('Los precios no pueden ser negativos ni cero');
				}
			}
		}
	}

	public function tieneDetalle() {
		return (count($this->detalle) > 0 ? true : false);
	}

	public function addDetalle($item) {
		$this->_detalle[] = $item;
	}

	public function guardar() {
		return parent::guardar();

		/*foreach($this->detalle as $item){
			if($item->material->esSemielaborado()){
				foreach($item->material->patronItems as $patronItem){
					$patronItem->colorMateriaPrima->material->id;
					$patronItem->colorMateriaPrima->idColor;
					$patronItem->cantEntregada;

					$ajusteStockMateriaPrima = Factory::getInstance()->getAjusteStockMateriaPrima();
					$ajusteStockMateriaPrima->tipoMovimiento = 'ANEG';
					$ajusteStockMateriaPrima->efectoMovimiento = 'S';
					$ajusteStockMateriaPrima->material = $patronItem->material;
					$ajusteStockMateriaPrima->colorMateriaPrima = $patronItem->colorMateriaPrima;
					$ajusteStockMateriaPrima->cantidad = $patronItem->consumoPar * $item->cantidad;
					if($ajusteStockMateriaPrima->material->usaRango()){
						for($i = 1; $i < 11; $i++){
							$ajusteStockMateriaPrima->cantidades[$i] = $patronItem->consumoPar * $item->cantidades[$i];
						}
					}
					$ajusteStockMateriaPrima->operador = Factory::getInstance()->getOperador('V00108');
					$ajusteStockMateriaPrima->motivacion = 'Ajuste negativo por generación de órden de compra Nº ' . $this->id . ' (semielaborados)';
					$ajusteStockMateriaPrima->almacen = $this->almacen;

					$ajusteStockMateriaPrima->guardar();
				}
			}
		}*/
	}

	public function borrar() {
		foreach($this->detalle as $item){
			/** @var OrdenDeCompraItem $item */
			if($item->material->usaRango()){
				for($i = 1; $i < 11; $i++){
					if($item->cantidades[$i] != $item->cantidadesPendientes[$i]){
						throw new FactoryExceptionCustomException('No puede borrar una orden de compra que contenga artículos saciados');
					}
				}
			}else {
				if($item->cantidad != $item->cantidadPendiente){
					throw new FactoryExceptionCustomException('No puede borrar una orden de compra que contenga artículos saciados');
				}
			}

			$where = 'cod_orden_de_compra = ' . $item->ordenDeCompra->id . 'AND ';
			$where .= 'nro_item_orden_de_compra = ' . $item->numeroDeItem;

			$presupuestosOrdenCompra = Factory::getInstance()->getListObject('PresupuestoOrdenCompra', $where);

			foreach($presupuestosOrdenCompra as $presupuestoOrdenCompra){
				/** @var PresupuestoOrdenCompra $presupuestoOrdenCompra */
				$presupuestoOrdenCompra->presupuestoItem->saciado = 'N';
				$presupuestoOrdenCompra->presupuestoItem->guardar();
				$presupuestoOrdenCompra->borrar();
			}

			/*if($item->material->esSemielaborado()){
				foreach($item->material->patronItems as $patronItem){
					$patronItem->colorMateriaPrima->material->id;
					$patronItem->colorMateriaPrima->idColor;
					$patronItem->cantEntregada;

					$ajusteStockMateriaPrima = Factory::getInstance()->getAjusteStockMateriaPrima();
					$ajusteStockMateriaPrima->tipoMovimiento = 'APOS';
					$ajusteStockMateriaPrima->efectoMovimiento = 'E';
					$ajusteStockMateriaPrima->material = $patronItem->material;
					$ajusteStockMateriaPrima->colorMateriaPrima = $patronItem->colorMateriaPrima;
					$ajusteStockMateriaPrima->cantidad = $patronItem->consumoPar * $item->cantidad;
					if($ajusteStockMateriaPrima->material->usaRango()){
						for($i = 1; $i < 11; $i++){
							$ajusteStockMateriaPrima->cantidades[$i] = $patronItem->consumoPar * $item->cantidades[$i];
						}
					}
					$ajusteStockMateriaPrima->operador = Factory::getInstance()->getOperador('V00108');
					$ajusteStockMateriaPrima->motivacion = 'Ajuste positivo por anulado de órden de compra Nº ' . $this->id . ' (semielaborados)';
					$ajusteStockMateriaPrima->almacen = $this->almacen;

					$ajusteStockMateriaPrima->guardar();
				}
			}*/
		}

		return parent::borrar();
	}

	//formulario
	public function abrir() {
		$this->crearFormulario();
		$this->llenarFormulario();
		$this->formulario->abrir();
	}

	public function crear() {
		$this->crearFormulario();
		$this->llenarFormulario();
		return $this->formulario->crear();
	}

	protected function crearFormulario() {
		$this->formulario = new FormularioOrdenDeCompra();
	}

	protected function llenarFormulario() {
		$this->formulario->id = $this->id;
		$this->formulario->fecha = $this->fechaAlta;
		$this->formulario->proveedor = $this->proveedor;
		$this->formulario->detalle = $this->detalle;
		$this->formulario->montoTotal = $this->importeTotal;
		$this->formulario->observaciones = $this->observaciones;
	}

	//GETS y SETS
	protected function getAlmacen() {
		if (!isset($this->_proveedor)){
			$this->_almacen = Factory::getInstance()->getAlmacen($this->idAlmacen);
		}
		return $this->_almacen;
	}
	protected function setAlmacen($almacen) {
		$this->_almacen = $almacen;
		return $this;
	}
	protected function getDetalle() {
		if (!isset($this->_detalle)){
			$this->_detalle = Factory::getInstance()->getListObject('OrdenDeCompraItem', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_orden_de_compra = ' . Datos::objectToDB($this->id));
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
	protected function getImportePendiente() {
		if (!isset($this->_importePendiente)){
			$importeTotal = 0;
			foreach($this->detalle as $item){
				$importeTotal += $item->importePendiente;
			}

			$this->_importePendiente = $importeTotal;
		}
		return $this->_importePendiente;
	}
	protected function getImporteTotal() {
		if (!isset($this->_importeTotal)){
			$importeTotal = 0;
			foreach($this->detalle as $item){
				$importeTotal += $item->importe;
			}

			$this->_importeTotal = $importeTotal;
		}
		return $this->_importeTotal;
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
	protected function getNroDocumentoCompleto() {
		if (!isset($this->_nroDocumentoCompleto)){
			$this->_nroDocumentoCompleto = Funciones::padLeft((is_null($this->codSucursal) ? '0' : $this->codSucursal), 4, 0) . '-' . Funciones::padLeft((is_null($this->numero) ? '0' : $this->numero), 8, 0);
		}
		return $this->_nroDocumentoCompleto;
	}
	protected function setNroDocumentoCompleto($nroDocumentoCompleto) {
		$this->_nroDocumentoCompleto = $nroDocumentoCompleto;
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
}

?>