<?php

/**
 * @property ColorPorArticulo[]				$colores
 * @property Horma							$horma
 * @property LineaProducto					$lineaProducto
 * @property FamiliaProducto				$familiaProducto
 * @property Marca							$marca
 * @property Proveedor						$proveedor
 * @property Cliente						$cliente
 * @property RangoTalle						$rangoTalle
 * @property RubroIva						$rubroIva
 * @property RutaProduccion					$rutaProduccion
 * @property Temporada						$temporada
 * @property CurvaProduccionPorArticulo[]	$curvasDeProduccion
 */

class Articulo extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	public		$vigente;
	protected	$_colores;
	public		$fechaDeLanzamiento;
	public		$fechaDePrecioActual;
	public		$idHorma;
	protected	$_horma;
	public		$idLineaProducto;
	protected	$_lineaProducto;
	public		$idFamiliaProducto;
	protected	$_familiaProducto;
	public		$idMarca;
	protected	$_marca;
	public		$naturaleza;				// "PT": ProductoTerminado || "SE": SemiElaborado
	public		$origen;					// "N"acional || "I"mportado
	/* Se usan los precios de ColorPorArticulo
	public		$precioDistribuidor;
	public		$precioListaDistribuidor;
	public		$precioLista;
	public		$precioListaOriginal;		// Es una copia de precioLista. Sirve para comparar si cambió el precio.
	public		$precioListaAumento;
	public		$precioListaMayorista;
	public		$precioRecargado;
	*/
	public		$idCliente;
	protected	$_cliente;
	public		$idProveedor;
	protected	$_proveedor;
	public		$idRangoTalle;
	protected	$_rangoTalle;
	public		$idRubroIva;
	protected	$_rubroIva;
	public		$idRutaProduccion;
	protected	$_rutaProduccion;
	public		$idTemporada;
	protected	$_temporada;
	protected	$_curvasDeProduccion;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;


	public function vigente() {
		return $this->vigente == 'S';
	}

	public function addItem(ColorPorArticulo $item) {
		if (!isset($this->_colores)) {
			$this->_colores = array();
		}
		$item->idArticulo = $this->id;
		$this->_colores[] = $item;
	}

	public function borrar() {
		foreach ($this->colores as $color) {
			Factory::getInstance()->marcarParaBorrar($color);
		}
		return parent::borrar();
	}

	//GETS y SETS
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
	protected function getColores() {
		if (!isset($this->_colores) && isset($this->id)){
			$this->_colores = Factory::getInstance()->getListObject('ColorPorArticulo', 'cod_articulo = ' . Datos::objectToDB(Funciones::toString($this->id)) . ' AND vigente = ' . Datos::objectToDB('S'));
		}
		return $this->_colores;
	}
	protected function setColores($colores) {
		$this->_colores = $colores;
		return $this;
	}
	protected function getCurvasDeProduccion() {
		if (!isset($this->_curvasDeProduccion) && isset($this->id)){
            $where = 'cod_articulo = ' . Datos::objectToDB(Funciones::toString($this->id)) . ' AND tipo_modulo = ' . Datos::objectToDB('P') . ' AND activo = ' . Datos::objectToDB('S');
            $order = ' ORDER BY cod_modulo ASC';
			$this->_curvasDeProduccion = Factory::getInstance()->getListObject('CurvaProduccionPorArticulo', $where . $order);
		}
		return $this->_curvasDeProduccion;
	}
	protected function setCurvasDeProduccion($curvasDeProduccion) {
		$this->_curvasDeProduccion = $curvasDeProduccion;
		return $this;
	}
    protected function getFamiliaProducto() {
        if (!isset($this->_familiaProducto)) {
            $this->_familiaProducto = FamiliaProducto::find($this->idFamiliaProducto);
        }
        return $this->_familiaProducto;
    }
    protected function setFamiliaProducto($familiaProducto) {
        $this->_familiaProducto = $familiaProducto;
        return $this;
    }
	protected function getHorma() {
		if (!isset($this->_horma)){
			$this->_horma = Factory::getInstance()->getHorma($this->idHorma);
		}
		return $this->_horma;
	}
	protected function setHorma($horma) {
		$this->_horma = $horma;
		return $this;
	}
	protected function getLineaProducto() {
		if (!isset($this->_lineaProducto)){
			$this->_lineaProducto = Factory::getInstance()->getLineaProducto($this->idLineaProducto);
		}
		return $this->_lineaProducto;
	}
	protected function setLineaProducto($lineaProducto) {
		$this->_lineaProducto = $lineaProducto;
		return $this;
	}
	protected function getMarca() {
		if (!isset($this->_marca)){
			$this->_marca = Factory::getInstance()->getMarca($this->idMarca);
		}
		return $this->_marca;
	}
	protected function setMarca($marca) {
		$this->_marca = $marca;
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
	protected function getRangoTalle() {
		if (!isset($this->_rangoTalle)){
			$this->_rangoTalle = Factory::getInstance()->getRangoTalle($this->idRangoTalle);
		}
		return $this->_rangoTalle;
	}
	protected function setRangoTalle($rangoTalle) {
		$this->_rangoTalle = $rangoTalle;
		return $this;
	}
	protected function getRubroIva() {
		if (!isset($this->_rubroIva)){
			$this->_rubroIva = Factory::getInstance()->getRubroIva($this->idRubroIva);
		}
		return $this->_rubroIva;
	}
	protected function setRubroIva($rubroIva) {
		$this->_rubroIva = $rubroIva;
		return $this;
	}
	protected function getRutaProduccion() {
		if (!isset($this->_rutaProduccion)){
			$this->_rutaProduccion = Factory::getInstance()->getRutaProduccion($this->idRutaProduccion);
		}
		return $this->_rutaProduccion;
	}
	protected function setRutaProduccion($rutaProduccion) {
		$this->_rutaProduccion = $rutaProduccion;
		return $this;
	}
	protected function getTemporada() {
		if (!isset($this->_temporada)){
			$this->_temporada = Factory::getInstance()->getTemporada($this->idTemporada);
		}
		return $this->_temporada;
	}
	protected function setTemporada($temporada) {
		$this->_temporada = $temporada;
		return $this;
	}
}

?>