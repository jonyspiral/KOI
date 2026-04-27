<?php

/**
 * @property Cliente				$cliente
 * @property Sucursal				$sucursal
 * @property PedidoClienteItem[]	$detalle
 * @property Pedido					$pedido
 * @property Usuario				$usuario
 * @property Usuario				$usuarioBaja
 * @property Usuario				$usuarioUltimaMod
 */

class PedidoCliente extends Base {
    const		ESTADO_PENDIENTE = 'P';
    const		ESTADO_EN_CURSO = 'C';

    protected	$__table = 'pedidos_cliente_c';
    protected	$__primaryKey = array('id');

    public		$id;
    public		$idCliente;
    protected	$_cliente;
    public		$idSucursal;
    protected	$_sucursal;
    // public		$descuento;
    // public		$recargo;
    public		$importeTotal;
    protected	$_detalle;
    public		$estado;
    public		$idPedido;
    protected	$_pedido;
    public		$observaciones;
    public		$anulado;
    public		$idUsuario;
    public		$idUsuarioBaja;
    public		$idUsuarioUltimaMod;
    public		$fechaAlta;
    public		$fechaBaja;
    public		$fechaUltimaMod;

    public		$formulario;

    protected $__dbMappings = array(
        'id',
        'idCliente',
        'idSucursal',
        'importeTotal',
        'estado',
        'idPedido',
        'observaciones',
        'anulado',
        'idUsuario',
        'idUsuarioBaja',
        'idUsuarioUltimaMod',
        'fechaAlta',
        'fechaBaja',
        'fechaUltimaMod'
    );

    protected $__relations = array(
        'detalle' => array(
            'cascadeDelete' => true
        )
    );

    public static function find($id = -1) {
        $obj = new PedidoCliente();
        return $obj->baseFind(func_get_args());
    }

    protected function save() {
        parent::save();
        if ($this->modo == Modos::insert) {
            foreach ($this->detalle as $item) {
                $item->marcarParaInsertar();
                $item->idPedidoCliente = $this->id;
                $item->guardar();
            }
        }
        return $this;
    }

	public function calcularTotal() {
		$aux = 0;
		foreach ($this->getDetalle() as $item) {
			$aux += Funciones::toFloat($item->precioUnitario) * Funciones::sumaArray($item->cantidades);
		}
		$this->importeTotal = $aux;
		return $this->importeTotal;
	}

	public function calcularTotalPares() {
		$aux = 0;
		foreach ($this->getDetalle() as $item) {
			$aux += Funciones::sumaArray($item->cantidades);
		}
		return $aux;
	}

	public function addItem(PedidoClienteItem $item) {
		$this->getDetalle(); //En caso de pedido nuevo, esto me va a traer un array vacío
		$this->_detalle[] = $item;
	}

	// Formulario
    public function abrir() {
        $this->formulario = new FormularioPedidoCliente();
        $this->formulario->pedido = $this;
        $this->formulario->abrir();
    }

	//GETS y SETS
	protected function getCliente() {
		if (!isset($this->_cliente)){
			//Hago clienteTodos porque sino no funciona en el HtmlAutoSuggestBox
			$this->_cliente = Factory::getInstance()->getClienteTodos($this->idCliente);
		}
		return $this->_cliente;
	}
	protected function setCliente($cliente) {
		$this->_cliente = $cliente;
        $this->idCliente = $cliente->id;
		return $this;
	}
	protected function getDetalle() {
		if (!isset($this->_detalle) && isset($this->id)){
			$this->_detalle = Base::getListObject('PedidoClienteItem', 'cod_pedido_cliente = ' . Datos::objectToDB($this->id) . ' ');
		}
		return $this->_detalle;
	}
	protected function setDetalle($detalle) {
		$this->_detalle = $detalle;
		return $this;
	}
    protected function getPedido() {
        if (!isset($this->_pedido)){
            $this->_pedido = Factory::getInstance()->getPedido($this->idPedido);
        }
        return $this->_pedido;
    }
    protected function setPedido(Pedido $pedido) {
        $this->_pedido = $pedido;
        $this->idPedido = $pedido->numero;
        return $this;
    }
    protected function getSucursal() {
        if (!isset($this->_sucursal)){
            $this->_sucursal = Factory::getInstance()->getSucursal($this->idCliente, $this->idSucursal);
        }
        return $this->_sucursal;
    }
    protected function setSucursal($sucursal) {
        $this->_sucursal = $sucursal;
        $this->idCliente = $sucursal->idCliente;
        $this->idSucursal = $sucursal->id;
        return $this;
    }
}

?>