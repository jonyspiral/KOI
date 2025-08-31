<?php
/**
 * @property RangoTalle			$rango
 * @property Articulo			$articulo
 * @property PatronItem[]		$patronItems
 * @property UnidadDeMedida		unidadDeMedida
 * @property UnidadDeMedida		unidadDeMedidaCompra
 */
class Material extends Base {
	const		_primaryKey = '["id"]';

	public		$id;

	public		$nombre;
	public		$idRubro;
	protected	$_rubro;
	public		$idSubrubro;
	public		$_subRubro;
	public		$usaRango;
	public		$precioPorTalle;
	public		$idRango;
	protected	$_rango;
	public		$idUnidadMedida;
	protected	$_unidadMedida;
	public		$idUnidadMedidaCompra;
	protected	$_unidadMedidaCompra;
	public		$factorConversion;
	public		$loteMinimo;
	public		$loteMultiplo;
	public		$anticipacionCompra;
	public		$fechaUltimaMod;
	public		$produccionInterna;
	public		$fotografia;
	public		$packaging;
	public		$espesor;
	public		$textura;
	public		$soporte;
	public		$materialPredomina;
	public		$trazabilidadOblig;
	public		$muestraEnPlanificacion;
	public		$tieneCorrimiento;
	public		$idArticulo;
	protected	$_articulo;
	public		$naturaleza;
	protected	$_patronItems;

	public function usaRango() {
		return $this->usaRango == 'S';
	}

	public function esSemielaborado() {
		return $this->naturaleza == 'SE';
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
	protected function getPatronItems() {
		if (!isset($this->_patronItems)){
			if($this->esSemielaborado()){
				$where = 'cod_articulo = ' . Datos::objectToDB($this->articulo->id);
				$orderBy = ' ORDER BY version DESC';
				$patrones = Factory::getInstance()->getListObject('Patron', $where . $orderBy);

				if(count($patrones) == 0){
					throw new FactoryExceptionCustomException('No existen patrones para el material "[' . $this->id .'] ' . $this->nombre . '"');
				}
				$patron = $patrones[0];
				/** @var Patron $patron */
				$this->_patronItems = $patron->detalle;
			}else {
				$this->_patronItems = array();
			}
		}
		return $this->_patronItems;
	}
	protected function getRubro() {
		if (!isset($this->_rubro)){
			$this->_rubro = Factory::getInstance()->getRubro($this->idRubro);
		}
		return $this->_rubro;
	}
	protected function setRubro($rubro) {
		$this->_rubro = $rubro;
		return $this;
	}
	protected function getSubRubro() {
		if (!isset($this->_subRubro)){
			$this->_subRubro = Factory::getInstance()->getRubro($this->idRubro);
		}
		return $this->_subRubro;
	}
	protected function setSubRubro($subRubro) {
		$this->_subRubro = $subRubro;
		return $this;
	}
	protected function getRango() {
		if (!isset($this->_rango)){
			$this->_rango = Factory::getInstance()->getRangoTalle($this->idRango);
		}
		return $this->_rango;
	}
	protected function setRango($rango) {
		$this->_rango = $rango;
		return $this;
	}
	protected function getUnidadDeMedida() {
		if (!isset($this->_unidadMedida)){
			$this->_unidadMedida = Factory::getInstance()->getUnidadDeMedida($this->idUnidadMedida);
		}
		return $this->_unidadMedida;
	}
	protected function setUnidadDeMedida($unidadMedida) {
		$this->_unidadMedida = $unidadMedida;
		return $this;
	}
	protected function getUnidadDeMedidaCompra() {
		if (!isset($this->_unidadMedidaCompra)){
			$this->_unidadMedidaCompra = Factory::getInstance()->getUnidadDeMedida($this->idUnidadMedidaCompra);
		}
		return $this->_unidadMedidaCompra;
	}
	protected function setUnidadDeMedidaCompra($unidadMedidaCompra) {
		$this->_unidadMedidaCompra = $unidadMedidaCompra;
		return $this;
	}
}

?>