<?php

/**
 * @property Catalogo			$catalogo
 * @property LineaProducto		$lineaProducto
 * @property FamiliaProducto	$familiaProducto
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 */
class CatalogoSeccionFamiliaArticulo extends Base {
	protected	$__table = 'catalogo_seccion_familia_articulos';
	protected	$__primaryKey = array('idCatalogo', 'idLineaProducto', 'idFamiliaProducto', 'idArticulo', 'idColorPorArticulo');
    protected	$__autoIncrement = false;
    protected	$__softDelete = false;

	public		$idCatalogo;
    protected	$_catalogo;
    public		$idLineaProducto;
    protected	$_lineaProducto;
    public		$idFamiliaProducto;
    protected	$_familiaProducto;
    public		$idArticulo;
    protected	$_articulo;
    public		$idColorPorArticulo;
    protected	$_colorPorArticulo;
    public		$orden;

    protected $__dbMappings = array(
        'idCatalogo',
        'idLineaProducto',
        'idFamiliaProducto',
        'idArticulo',
        'idColorPorArticulo' => array('db' => 'cod_color_articulo'),
        'orden'
    );

    public static function find($idCatalogo = -1, $idLineaProducto = -1, $idFamiliaProducto = -1, $idArticulo = -1, $idColorPorArticulo = -1) {
        $obj = new CatalogoSeccionFamiliaArticulo();
        return $obj->baseFind(func_get_args());
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
        $this->idArticulo = $articulo->id;
        return $this;
    }
    protected function getCatalogo() {
        if (!isset($this->_catalogo)) {
            $this->_catalogo = Catalogo::find($this->idCatalogo);
        }
        return $this->_catalogo;
    }
    protected function setCatalogo($catalogo) {
        $this->_catalogo = $catalogo;
        $this->idCatalogo = $catalogo->id;
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
        $this->idArticulo = $colorPorArticulo->idArticulo;
        $this->idColorPorArticulo = $colorPorArticulo->id;
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
        $this->idFamiliaProducto = $familiaProducto->id;
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
        $this->idLineaProducto = $lineaProducto->id;
        return $this;
    }
}

?>