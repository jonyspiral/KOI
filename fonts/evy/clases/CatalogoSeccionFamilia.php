<?php

/**
 * @property Catalogo							$catalogo
 * @property LineaProducto						$lineaProducto
 * @property FamiliaProducto					$familiaProducto
 * @property CatalogoSeccionFamiliaArticulo[]	$articulos
 */
class CatalogoSeccionFamilia extends Base {
	protected	$__table = 'catalogo_seccion_familias';
	protected	$__primaryKey = array('idCatalogo', 'idLineaProducto', 'idFamiliaProducto');
    protected	$__autoIncrement = false;
    protected	$__softDelete = false;

	public		$idCatalogo;
    protected	$_catalogo;
    public		$idLineaProducto;
    protected	$_lineaProducto;
    public		$idFamiliaProducto;
    protected	$_familiaProducto;
    public		$orden;
    public		$descripcion;
    public		$imagenLateral;
    protected	$_articulos;

    protected $__dbMappings = array(
        'idCatalogo',
        'idLineaProducto',
        'idFamiliaProducto',
        'orden',
        'descripcion',
        'imagenLateral'
    );

    protected $__relations = array(
        'articulos' => array(
            'cascadeDelete' => true
        )
    );

    public static function find($idCatalogo = -1, $idLineaProducto = -1, $idFamiliaProducto = -1) {
        $obj = new CatalogoSeccionFamilia();
        return $obj->baseFind(func_get_args());
    }

	//GETS y SETS
    protected function getArticulos() {
        if (!isset($this->_articulos)) {
            $where = 'cod_catalogo = ' . Datos::objectToDB($this->catalogo->id);
            $where .= ' AND cod_linea_producto = ' . Datos::objectToDB($this->lineaProducto->id);
            $where .= ' AND cod_familia_producto = ' . Datos::objectToDB($this->familiaProducto->id);

            $this->_articulos = Base::getListObject('CatalogoSeccionFamiliaArticulo', $where . ' ORDER BY orden ASC');
        }
        return $this->_articulos;
    }
    protected function setArticulos($articulos) {
        $this->_articulos = $articulos;
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