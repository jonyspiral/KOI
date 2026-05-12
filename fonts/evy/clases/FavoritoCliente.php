<?php

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

    protected $__dbMappings = array(
        'idCliente',
        'idArticulo',
        'idColorPorArticulo' => array('db' => 'cod_color_articulo'),
        // 'cant_N', // Esto lo manejo extendiendo algunos métodos (fill, getQueryX)
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
        for ($i = 1; $i <= 10; $i++) {
            $this->cantidades[$i] = $dr['cant_' . $i];
        }
        $this->curvas = json_decode($this->curvas, true);
        return $this;
    }

    private function cantidadesToDB($values) {
        for ($i = 1; $i <= 10; $i++) {
            $values['cant_' . $i] = Datos::objectToDB($this->cantidades[$i]);
        }
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
}

?>