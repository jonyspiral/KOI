<?php

/**
 * @property PedidoCliente		$pedidoCliente
 * @property Articulo			$articulo
 * @property ColorPorArticulo	$colorPorArticulo
 */

class PedidoClienteItem extends Base {
    protected	$__table = 'pedidos_cliente_d';
    protected	$__primaryKey = array('idPedidoCliente', 'numeroDeItem');
    protected	$__autoIncrement = false;
    protected	$__softDelete = false;

    public		$idPedidoCliente;
    protected	$_pedidoCliente;
    public		$numeroDeItem;
    public		$idArticulo;
    protected	$_articulo;
    public		$idColorPorArticulo;
    protected	$_colorPorArticulo;
    public		$precioUnitario;
    public		$cantidades = array();	//Array de 1 a 10
    public		$idUsuario;
    public		$fechaAlta;

    protected $__dbMappings = array(
        'idPedidoCliente',
        'numeroDeItem' => array('db' => 'nro_item'),
        'idArticulo',
        'idColorPorArticulo' => array('db' => 'cod_color_articulo'),
        'precioUnitario',
        // 'cant_N', // Esto lo manejo extendiendo algunos métodos (fill, getQueryX)
        'idUsuario',
        'fechaAlta'
    );

    public static function find($idPedidoCliente = -1, $numeroDeItem = -1) {
        $obj = new PedidoClienteItem();
        return $obj->baseFind(func_get_args());
    }

    protected function fill($dr) {
        parent::fill($dr);
        for ($i = 1; $i <= 10; $i++) {
            $this->cantidades[$i] = $dr['cant_' . $i];
        }
        return $this;
    }

    protected function getQueryInsertValues() {
        $values = parent::getQueryInsertValues();
        for ($i = 1; $i <= 10; $i++) {
            $values['cant_' . $i] = Datos::objectToDB($this->cantidades[$i]);
        }
        return $values;
    }

    public function calcularImporteTotal() {
        return Funciones::toFloat($this->precioUnitario) * $this->calcularTotalPares();
    }

    public function calcularTotalPares() {
        return Funciones::sumaArray($this->cantidades);
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
	protected function getPedidoCliente() {
		if (!isset($this->_pedidoCliente)){
			$this->_pedidoCliente = Factory::getInstance()->getPedidoCliente($this->id);
		}
		return $this->_pedidoCliente;
	}
	protected function setPedidoCliente($pedidoCliente) {
		$this->_pedidoCliente = $pedidoCliente;
        $this->idPedidoCliente = $pedidoCliente->id;
		return $this;
	}
}

?>