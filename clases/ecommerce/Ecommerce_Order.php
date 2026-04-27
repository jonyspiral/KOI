<?php

/**
 * @property Ecommerce_OrderStatus		$status
 * @property Ecommerce_ServicioAndreani	$servicioAndreani
 * @property Ecommerce_Customer			$customer
 * @property Ecommerce_Delivery			$delivery
 * @property Ecommerce_OrderDetail[]	$details
 * @property Ecommerce_Coupon[]			$coupons
 * @property Ecommerce_Payment[]		$payments
 * @property Ecommerce_Document[]		$documents
 * @property Ecommerce_OrderStatus[]	$dependenciasCumplidas
 * @property Ecommerce_Coupon			$cuponDeCambio
 * @property Recibo						$recibo
 * @property Pedido						$pedido
 * @property Despacho					$despacho
 * @property Remito						$remito
 * @property Factura					$factura
 * @property Usuario					$usuario
 * @property Usuario					$usuarioBaja
 * @property Usuario					$usuarioUltimaMod
 */
class Ecommerce_Order extends Base {
	const	_primaryKey = '["id"]';

	public		$id;
	public		$idEcommerce;
	public		$idStatus;
	protected	$_status;
	public		$idServicioAndreani;
	protected	$_servicioAndreani;
	public		$idCustomer;
	protected	$_customer;
	public		$idDelivery;
	protected	$_delivery;
	public		$totalDiscount;
	public		$totalCoupon;
	public		$grandTotal;
	public		$fechaPedido;
	protected	$_details;
	protected	$_cantidadPares;
	protected	$_coupons;
	protected	$_payments;
	protected	$_documents;
	public		$idDependenciasCumplidas;
	protected	$_dependenciasCumplidas;
	public		$idCuponDeCambio;
	protected	$_cuponDeCambio;
	public		$cuponDeCambioUtilizado;
	public		$cuponDeCambioImporte;
	protected	$_recibo;
	protected	$_pedido;
	protected	$_despacho;
	protected	$_remito;
	protected	$_factura;
	public		$anulado;
	public		$idUsuario;
	protected	$_usuario;
	public		$idUsuarioBaja;
	protected	$_usuarioBaja;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	public function calcularDescuentoEnPorcentaje() {
		if ($this->totalDiscount > 0) {
			return ($this->totalDiscount / ($this->totalDiscount + $this->grandTotal)) * 100;
		}
		return 0;
	}

	public function correspondeRecibo() {
		return $this->grandTotal > 0;
	}

	public function avanzarStatus(Ecommerce_OrderStatus $proximoStatus = null) {
		//El parï¿½metro es para cuando quiero forzarlo a llegar hasta un determinado status o para cuando puede tener dos proximos status posibles
		//IMPORTANTE: Tener en cuenta que si no estï¿½n cumplidas las dependencias esto intentarï¿½ cumplirlas
		try {
			Factory::getInstance()->beginTransaction();

			if (is_null($proximoStatus)) {
				//Tengo que preguntar acï¿½ si tieneProximoStatus y no antes de procesar ya que a veces necesito procesar uno que no tiene proximoStatus (por dependencias)
				if ($this->status->tieneProximoStatus()) {
					$proximoStatus = $this->status->proximoStatus;
				}
			}
			$proximoStatus->procesar($this);

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}
	}

	public function retrocederStatus() {
		try {
			Factory::getInstance()->beginTransaction();

			//El comentario que estï¿½ en avanzarStatus vale para esto tambiï¿½n
			if ($this->status->esReversible()) {
				$this->status->desprocesar($this);
			}

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex) {
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}
	}

	public function registrarStatusProcesado($status) {
		if (!in_array($status->id, explode(',', $this->idDependenciasCumplidas))) {
			$this->idDependenciasCumplidas .= (!empty($this->idDependenciasCumplidas) ? ',' : '') . $status->id;
		}
		$this->status = $status;
		$this->guardar();
	}

	public function registrarStatusDesprocesado(Ecommerce_OrderStatus $status) {
		$dependencias = explode(',', $this->idDependenciasCumplidas);
		if (in_array($status->id, $dependencias)) {
			$newArray = array();
			foreach ($dependencias as $dep) {
				if ($dep != $status->id) {
					$newArray[] = $dep;
				}
			}
			$this->idDependenciasCumplidas = implode(',', $newArray);
		}
		$this->status = $status->statusAnterior;
		$this->guardar();
	}

	public function tieneDependenciaCumplida($idDependencia) {
		return in_array($idDependencia, explode(',', $this->idDependenciasCumplidas));
	}

	public function guardar() {
		//ï¿½CUIDADO! Si vas a usar esta funciï¿½n, verificï¿½ que efectivamente se estï¿½ llegando en modo UPDATE
		if ($this->modo == Modos::insert) {
			$this->agregar();
		} elseif ($this->modo == Modos::update) {
			$this->editar();
		}

		return $this;
	}

	protected function validarGuardar() {
		if (!isset($this->cuponDeCambioUtilizado)) {
			$this->cuponDeCambioUtilizado = 'N';
		}
		if (!isset($this->cuponDeCambioImporte)) {
			$this->cuponDeCambioImporte = 0;
		}
	}

	private function agregar() {
		try {
			Factory::getInstance()->beginTransaction();

			//Guardo los objetos necesarios para guardar el ORDER
			$this->customer->guardar();

			//Le asigno al ORDER el tipo de servicio de Andreani que le corresponde
			$this->asignarServicioAndreani();

			//Guardo el ORDER
			parent::guardar();

			//Guardo los objetos que necesitan al ORDER
			$this->delivery->order = $this;
			$this->delivery->guardar();

			foreach ($this->coupons as $coupon) {
				$coupon->order = $this;
				$coupon->guardar();
			}
			foreach ($this->payments as $payment) {
				$payment->order = $this;
				$payment->guardar();
			}
			foreach ($this->details as $detail) {
				$detail->order = $this;
				$detail->guardar();
			}

			//Comienzo el circuito de STATUS
			$statusInicial = Ecommerce_OrderStatus::forge(Ecommerce_Configuration::ECOMMERCE_ID_STATUS_INICIAL);
			$statusInicial->procesar($this);

			//Una vez logrado el status inicial, comienzo a avanzar de estados hasta llegar al obligatorio
			$this->avanzarHasta(Ecommerce_Configuration::ECOMMERCE_ID_STATUS_HASTA_OBLIGATORIO);

			Factory::getInstance()->commitTransaction();
		} catch (Exception $ex){
			Factory::getInstance()->rollbackTransaction();
			throw $ex;
		}

		try {
			//Una vez generados los status obligatorios, paso a generar los automï¿½ticos pero opcionales
			$this->avanzarHasta(Ecommerce_Configuration::ECOMMERCE_ID_STATUS_HASTA_INTENTAR);
		} catch (Exception $ex) {
			//No hago nada, no me importa si tira error acï¿½, se verï¿½ despuï¿½s
		}

		return $this;
	}

	private function editar() {
		return parent::guardar();
	}

	private function avanzarHasta($idStatusHasta) {
		while ($this->status->id != $idStatusHasta) {
			$this->avanzarStatus();
			$this->guardar();
		}
	}

	private function asignarServicioAndreani() {
		//Reviso si vino algï¿½n cupï¿½n de cambio, entonces seteo este pedido como de cambio.
		foreach ($this->coupons as $coupon) {
			$orders = Factory::getInstance()->getListObject('Ecommerce_Order', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_cupon_cambio = ' . Datos::objectToDB($coupon->code));
			if (count($orders)) {
				$this->idServicioAndreani = Ecommerce_ServicioAndreani::CAMBIO;
				/** @var Ecommerce_Order $order */
				$order = $orders[0];
				$order->cuponDeCambioUtilizado = 'S';
				//$order->retrocederStatus(); //Debo dejar el pedido en CAMBIO para que puedan ingresar las garantï¿½as
				$order->guardar(); //Esto ademï¿½s hace el GUARDAR
			}
			break;
		}

		if (!isset($this->idServicioAndreani)) {
			$this->idServicioAndreani = Ecommerce_ServicioAndreani::ENVIO_URGENTE;
		}
	}

	//GETS y SETS
	public function getCantidadPares() {
		if (!isset($this->_cantidadPares)){
			$this->_cantidadPares = 0;
			foreach ($this->details as $detail) {
				$this->_cantidadPares += $detail->quantity;
			}
		}
		return $this->_cantidadPares;
	}
	protected function getCoupons() {
		if (!isset($this->_coupons) && isset($this->id)){
			$this->_coupons = Factory::getInstance()->getListObject('Ecommerce_Coupon', 'cod_order = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N'));
		}
		return $this->_coupons;
	}
	protected function setCoupons($coupons) {
		$this->_coupons = $coupons;
		return $this;
	}
	protected function getCuponDeCambio() {
		if ($this->cuponDeCambioUtilizado == 'S' && !isset($this->_cuponDeCambio)){
            $cupones = Factory::getInstance()->getListObject('Ecommerce_Coupon', 'code = ' . Datos::objectToDB($this->idCuponDeCambio) . ' AND anulado = ' . Datos::objectToDB('N'));
            if (count($cupones) == 1) {
                $this->_cuponDeCambio = $cupones[0];
            }
		}
		return $this->_cuponDeCambio;
	}
	protected function setCuponDeCambio($cuponDeCambio) {
		$this->_cuponDeCambio = $cuponDeCambio;
		return $this;
	}
	protected function getCustomer() {
		if (!isset($this->_customer)){
			$this->_customer = Factory::getInstance()->getEcommerce_Customer($this->idCustomer);
		}
		return $this->_customer;
	}
	protected function setCustomer($customer) {
		$this->_customer = $customer;
		return $this;
	}
	protected function getDelivery() {
		if (!isset($this->_delivery) && isset($this->id)){
			$list = Factory::getInstance()->getListObject('Ecommerce_Delivery', 'cod_order = ' . Datos::objectToDB($this->id));
			$this->_delivery = count($list) ? $list[0] : null;
		}
		return $this->_delivery;
	}
	protected function setDelivery($delivery) {
		$this->_delivery = $delivery;
		return $this;
	}
	protected function getDetails() {
		if (!isset($this->_details) && isset($this->id)){
			$this->_details = Factory::getInstance()->getListObject('Ecommerce_OrderDetail', 'cod_order = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N'));
		}
		return $this->_details;
	}
	protected function setDetails($details) {
		$this->_details = $details;
		return $this;
	}
	protected function getDocuments() {
		if (!isset($this->_documents)){
			$documents = array();
			foreach (explode(',', $this->idDependenciasCumplidas) as $idDep) {
				$status = Ecommerce_OrderStatus::forge($idDep);
				$documentLinkObject = $status->getDocumentLinkObject($this);
				if ($documentLinkObject) {
					$documents[] = $documentLinkObject;
				}
			}
			$this->_documents = $documents;
		}
		return $this->_documents;
	}
	protected function setDocuments($documents) {
		$this->_documents = $documents;
		return $this;
	}
	protected function getDependenciasCumplidas() {
		if (!isset($this->_dependenciasCumplidas)){
			$this->_dependenciasCumplidas = array();
			foreach (explode(',', $this->idDependenciasCumplidas) as $idDependencia) {
				$this->_dependenciasCumplidas[] = Factory::getInstance()->getEcommerce_OrderStatus($idDependencia);
			}
		}
		return $this->_dependenciasCumplidas;
	}
	protected function setDependenciasCumplidas($dependenciasCumplidas) {
		$this->_dependenciasCumplidas = $dependenciasCumplidas;

		//Serializo las dependencias cumplidas en el campo $idDependenciasCumplidas
		$deps = array();
		foreach ($deps as $dep) {
			$deps[] = $dep->id;
		}
		$this->idDependenciasCumplidas = implode(',', $deps);

		return $this;
	}
	protected function getDespacho() {
		if (!isset($this->_despacho) && isset($this->id)){
			$despachos = Factory::getInstance()->getListObject('Despacho', 'cod_ecommerce_order = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N'));
			if (count($despachos) != 1) {
				throw new FactoryExceptionCustomException('No se pudo encontrar un despacho único para el "Order" buscado');
			}
			$this->_despacho = $despachos[0];
		}
		return $this->_despacho;
	}
	protected function setDespacho($despacho) {
		$this->_despacho = $despacho;
		return $this;
	}
	protected function getFactura() {
		if (!isset($this->_factura) && isset($this->id)){
			$facturas = Factory::getInstance()->getListObject('Factura', 'cod_ecommerce_order = ' . Datos::objectToDB($this->id) . ' AND tipo_docum = ' . Datos::objectToDB('FAC') . ' AND anulado = ' . Datos::objectToDB('N'));
			if (count($facturas) != 1) {
				throw new FactoryExceptionCustomException('No se pudo encontrar una factura única para el "Order" buscado');
			}
			$this->_factura = $facturas[0];
		}
		return $this->_factura;
	}
	protected function setFactura($factura) {
		$this->_factura = $factura;
		return $this;
	}
	protected function getPayments() {
		if (!isset($this->_payments) && isset($this->id)){
			$this->_payments = Factory::getInstance()->getListObject('Ecommerce_Payment', 'cod_order = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N'));
		}
		return $this->_payments;
	}
	protected function setPayments($payments) {
		$this->_payments = $payments;
		return $this;
	}
	protected function getPedido() {
		if (!isset($this->_pedido) && isset($this->id)){
			$pedidos = Factory::getInstance()->getListObject('Pedido', 'cod_ecommerce_order = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N'));
			if (count($pedidos) != 1) {
				throw new FactoryExceptionCustomException('No se pudo encontrar un pedido único para el "Order" buscado');
			}
			$this->_pedido = $pedidos[0];
		}
		return $this->_pedido;
	}
	protected function setPedido($pedido) {
		$this->_pedido = $pedido;
		return $this;
	}
	protected function getRecibo() {
		if (!isset($this->_recibo) && isset($this->id)){
			$recibos = Factory::getInstance()->getListObject('Recibo', 'cod_ecommerce_order = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N'));
			if (count($recibos) > 1) {
				throw new FactoryExceptionCustomException('No se pudo encontrar un recibo único para el "Order" buscado');
			}
			if (count($recibos) == 1) {
				$this->_recibo = $recibos[0];
			}
		}
		return $this->_recibo;
	}
	protected function setRecibo($recibo) {
		$this->_recibo = $recibo;
		return $this;
	}
	protected function getRemito() {
		if (!isset($this->_remito) && isset($this->id)){
			$remitos = Factory::getInstance()->getListObject('Remito', 'cod_ecommerce_order = ' . Datos::objectToDB($this->id) . ' AND anulado = ' . Datos::objectToDB('N'));
			if (count($remitos) != 1) {
				throw new FactoryExceptionCustomException('No se pudo encontrar un remito único para el "Order" buscado');
			}
			$this->_remito = $remitos[0];
		}
		return $this->_remito;
	}
	protected function setRemito($remito) {
		$this->_remito = $remito;
		return $this;
	}
	protected function getServicioAndreani() {
		if (!isset($this->_servicioAndreani)){
			$this->_servicioAndreani = Factory::getInstance()->getEcommerce_ServicioAndreani($this->idServicioAndreani);
		}
		return $this->_servicioAndreani;
	}
	protected function setServicioAndreani($servicioAndreani) {
		$this->_servicioAndreani = $servicioAndreani;
		return $this;
	}
	protected function getStatus() {
		if (!isset($this->_status)){
			$this->_status = Ecommerce_OrderStatus::forge($this->idStatus);
		}
		return $this->_status;
	}
	protected function setStatus($status) {
		$this->_status = $status;
		$this->idStatus = $status->id;
		return $this;
	}
}

?>