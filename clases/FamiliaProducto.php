<?php

/**
 * @property Articulo[]		$articulos
 * @property Usuario		$usuario
 */
class FamiliaProducto extends Base {
	protected	$__table = 'familias_producto';
	protected	$__primaryKey = array('id');

	public		$id;
	public		$nombre;
	public		$descripcion;
	protected	$_articulos;
    public		$anulado;
    public		$idUsuario;
    protected	$_usuario;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

    protected $__dbMappings = array(
        'id',
        'nombre',
        'descripcion',
        'anulado',
        'idUsuario',
        'fechaAlta',
        'fechaBaja',
        'fechaUltimaMod'
    );

    public static function find($id = -1) {
        $obj = new FamiliaProducto();
        return $obj->baseFind(func_get_args());
    }

	//GETS y SETS
    protected function getArticulos() {
        if (!isset($this->_articulos) && isset($this->id)) {
            $this->_articulos = Factory::getInstance()->getListObject('Articulo', 'cod_familia_producto = ' . Datos::objectToDB($this->id));
        }
        return $this->_articulos;
    }
    protected function setArticulos($articulos) {
        $this->_articulos = $articulos;
        return $this;
    }
}

?>