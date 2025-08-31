<?php

/**
 * @property CatalogoSeccion[]	$secciones
 * @property Usuario			$usuario
 * @property Usuario			$usuarioBaja
 * @property Usuario			$usuarioUltimaMod
 */
class Catalogo extends Base {
	protected	$__table = 'catalogos';
	protected	$__primaryKey = array('id');

	public		$id;
	public		$nombre;
	public		$descripcion;
	protected	$_secciones;
    public		$anulado;
    public		$idUsuario;
    public		$idUsuarioBaja;
    public		$idUsuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

    protected $__dbMappings = array(
        'id',
        'nombre',
        'descripcion',
        'anulado',
        'idUsuario',
        'idUsuarioBaja',
        'idUsuarioUltimaMod',
        'fechaAlta',
        'fechaBaja',
        'fechaUltimaMod'
    );

    protected $__relations = array(
        'secciones' => array(
            'cascadeDelete' => true
        )
    );

    public static function find($id = -1) {
        $obj = new Catalogo();
        return $obj->baseFind(func_get_args());
    }

    /**
     * @return Catalogo
     */
    public static function ultimo() {
        $catalogos = Base::getListObject('Catalogo', 'anulado = ' . Datos::objectToDB('N') . ' ORDER BY id DESC');
        return $catalogos[0];
    }

	//GETS y SETS
    protected function getSecciones() {
        if (!isset($this->_secciones) && isset($this->id)) {
            $this->_secciones = Base::getListObject('CatalogoSeccion', 'cod_catalogo = ' . Datos::objectToDB($this->id) . ' ORDER BY orden ASC');
        }
        return $this->_secciones;
    }
    protected function setSecciones($secciones) {
        $this->_secciones = $secciones;
        return $this;
    }
}

?>