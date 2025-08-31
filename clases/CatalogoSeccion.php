<?php

/**
 * @property Catalogo					$catalogo
 * @property LineaProducto				$lineaProducto
 * @property CatalogoSeccionFamilia[]	$familias
 */
class CatalogoSeccion extends Base {
	protected	$__table = 'catalogo_secciones';
	protected	$__primaryKey = array('idCatalogo', 'idLineaProducto');
	protected	$__autoIncrement = false;
	protected	$__softDelete = false;

	public		$idCatalogo;
    protected	$_catalogo;
    public		$idLineaProducto;
    protected	$_lineaProducto;
    public		$orden;
    protected	$_familias;

    protected $__dbMappings = array(
        'idCatalogo',
        'idLineaProducto',
        'orden'
    );

    protected $__relations = array(
        'familias' => array(
            'cascadeDelete' => true
        )
    );

    public static function find($idCatalogo = -1, $idLineaProducto = -1) {
        $obj = new CatalogoSeccion();
        return $obj->baseFind(func_get_args());
    }

    //GETS y SETS
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
    protected function getFamilias() {
        if (!isset($this->_familias)) {
            $this->_familias = Base::getListObject('CatalogoSeccionFamilia', 'cod_catalogo = ' . Datos::objectToDB($this->catalogo->id) . ' AND cod_linea_producto = ' . Datos::objectToDB($this->lineaProducto->id) . ' ORDER BY orden ASC');
        }
        return $this->_familias;
    }
    protected function setFamilias($familias) {
        $this->_familias = $familias;
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