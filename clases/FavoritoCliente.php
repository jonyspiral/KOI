<?php

require_once __DIR__ . '/Base.php';
require_once __DIR__ . '/BasePhp.php';

/**
 * @property ClienteTodos		$cliente
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 */
class FavoritoCliente extends Base {
    protected	$__table = 'favoritos_cliente';
    protected	$__primaryKey = array('idCliente', 'idArticulo', 'idColorPorArticulo');
    protected	$__autoIncrement = false;
    protected	$__softDelete = false;

    public		$idCliente;
    protected	$_cliente;
    public		$idArticulo;
    protected	$_articulo;
    public		$idColorPorArticulo;
    protected	$_colorPorArticulo;
    public		$cantidades = array();	//Array de 1 a 10
    public		$curvas;                // JSON con las curvas y sus cantidades
    public		$idUsuario;
    public		$fechaAlta;
    public		$fechaUltimaMod;

    public      $formulario;

    protected $__dbMappings = array(
        'idCliente',
        'idArticulo',
        'idColorPorArticulo' => array('db' => 'cod_color_articulo'),
        // 'cant_N', // Esto lo manejo extendiendo algunos mï¿½todos (fill, getQueryX)
        'curvas',
        'idUsuario',
        'fechaAlta',
        'fechaUltimaMod'
    );

    public static function find($idCliente = -1, $idArticulo = -1, $idColorPorArticulo = -1) {
        $obj = new FavoritoCliente();
        return $obj->baseFind(func_get_args());
    }

    protected function fill($dr) {
    parent::fill($dr);

    // Aseguro estructura de cantidades
    if (!isset($this->cantidades) || !is_array($this->cantidades)) {
        $this->cantidades = array();
    }
    for ($i = 1; $i <= 10; $i++) {
        // Si la columna no estÃ¡ en $dr, dejo NULL (se cargarÃ¡ mÃ¡s adelante)
        $this->cantidades[$i] = isset($dr['cant_' . $i]) ? $dr['cant_' . $i] : null;
    }

    // Curvas puede venir como string JSON, array o null
    if (is_string($this->curvas)) {
        $this->curvas = json_decode($this->curvas, true);
    } elseif (!is_array($this->curvas)) {
        $this->curvas = null;
    }

    return $this;
}

 private function cantidadesToDB($values) {
    // Aseguro estructura de cantidades antes de mapear a columnas
    if (!isset($this->cantidades) || !is_array($this->cantidades)) {
        $this->cantidades = array();
    }
    for ($i = 1; $i <= 10; $i++) {
        // si no estÃ¡ definida, va NULL en el INSERT/UPDATE (no 0, salvo que lo prefieras)
        $val = isset($this->cantidades[$i]) && $this->cantidades[$i] !== '' ? $this->cantidades[$i] : null;
        $values['cant_' . $i] = Datos::objectToDB($val);
    }

    // Curvas a JSON si hace falta (DB espera string)
    $values['curvas'] = is_string($values['curvas']) ? $values['curvas'] : json_encode($values['curvas']);

    return $values;
}

    protected function getQueryInsertValues() {
        $values = parent::getQueryInsertValues();
        $values = $this->cantidadesToDB($values);
        return $values;
    }

    protected function getQueryUpdateValues() {
        $values = parent::getQueryUpdateValues();
        $values = $this->cantidadesToDB($values);
        return $values;
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
	protected function getCliente() {
		if (!isset($this->_cliente)){
			$this->_cliente = Factory::getInstance()->getClienteTodos($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
        $this->idCliente = $cliente->id;
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

    // Reporte (Formulario)
    public function descargarReporte($usuarioId) {
        $this->formulario = new FormularioFavoritoCliente();
        $this->formulario->setUsuarioId($usuarioId);
        $this->formulario->abrir();
    }

    public function setFormulario(Formulario $obj)
    {

    }

    public function getFormulario()
    {

    }
}

?>