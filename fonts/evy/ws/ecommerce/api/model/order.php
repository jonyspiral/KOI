<?php

/**
 * @property int					$id
 * @property int					$idEcommerce
 * @property Ecommerce_Order		$_object
 */

class Model_Order extends Model_Base {
	protected $_koi_class_name = 'Ecommerce_Order';
	protected $_always_where = 'anulado = \'N\'';
	protected $_always_order = 'cod_order ASC';

	protected $_properties = array(
		'id'			=> 'idEcommerce',
		'datetime'		=> 'fechaPedido',
		'status'		=> 'status',
		'totaldiscount'	=> 'totalDiscount',
		'totalcoupon'	=> 'totalCoupon',
		'grandtotal'	=> 'grandTotal',
		'customer'		=> 'customer',
		'details'		=> 'details',
		'coupons'		=> 'coupons',
		'payments'		=> 'payments',
		'delivery'		=> 'delivery',
		'documents'		=> 'documents'
	);

	protected function get_by_id($id) {
		//Este $id vendría a ser el código de ecommerce, por eso es distinto
		//Si esto se empieza a usar para muchos modelos, se puede hacer una propiedad protected que se llame tipo $_GET_BY_ID_FIELD y que si tiene valor (tipo "cod_order_ecommerce") se busque con GetListObject
		$class_name = get_class($this);
		$orders = Factory::getInstance()->getListObject($this->_koi_class_name, 'anulado = ' . Datos::objectToDB('N') . ' AND cod_order_ecommerce = ' . $id);
		if (!count($orders)) {
			throw new Model_Exception_RecordExists('La "order" buscada no existe (N ' . $id . ')');
		}

		$return = new $class_name($orders[0]);
		return $return;
	}

	//Getters & Setters
	protected function get_datetime() {
		return Funciones::formatearFecha($this->_object->fechaPedido, 'Y-m-d H:i');
	}
	protected function get_status() {
		return $this->_object->status->nombre;
	}

	protected function set_datetime($value) {
		$this->_object->fechaPedido = empty($value) ? null : Funciones::formatearFecha($value, 'd/m/Y H:i');
		return $this;
	}

	protected function set_customer($value) {
		$customer = Ecommerce_Customer::getFromIdEcommerce($value['id']);
		$customer->idEcommerce = $value['id'];
		$customer->title = $value['title'];
		$customer->firstname = $value['firstname'];
		$customer->lastname = $value['lastname'];
		$customer->email = $value['email'];
		$customer->birthday = empty($value['birthday']) ? null : Funciones::formatearFecha($value['birthday'], 'd/m/Y');
		$customer->newsletters = $value['newsletters'] == 'yes' ? 'S' : 'N';
		$customer->offers = $value['offers'] == 'yes' ? 'S' : 'N';

		$usergroups = Factory::getInstance()->getListObject('Ecommerce_Usergroup', 'anulado = ' . Datos::objectToDB('N') . ' AND nombre = ' . Datos::objectToDB($value['usergroup']));
		if (count($usergroups) != 1) {
			throw new Model_Exception_AppException('No se pudo encontrar el USERGROUP "' . $value['usergroup'] . '"');
		}
		$customer->usergroup = $usergroups[0];

		$this->_object->customer = $customer;
		return $this;
	}

	protected function set_details($value) {
		$details = array();
		foreach ($value as $val) {
			/** @var Ecommerce_OrderDetail $detail */
			$detail = Factory::getInstance()->getEcommerce_OrderDetail();
			$detail->reference = $val['reference'];
			$detail->description = $val['description'];
			$detail->size = $val['size'];
			$detail->quantity = $val['quantity'];
			$detail->price = $val['price'];
			$detail->subtotal = $val['subtotal'];

			$details[] = $detail;
		}
		$this->_object->details = $details;
		return $this;
	}

	protected function set_coupons($value) {
		$coupons = array();
		foreach ($value as $val) {
			/** @var Ecommerce_Coupon $coupon */
			$coupon = Factory::getInstance()->getEcommerce_Coupon();
			$coupon->idEcommerce = $val['couponid'];
			$coupon->code = $val['code'];
			$coupon->amount = $val['amount'];
			$coupon->percentage = $val['percentage'];
			$coupon->maxAmount = $val['maxamount'];
			$coupon->appliedAmount = $val['appliedamount'];

			$coupons[] = $coupon;
		}
		$this->_object->coupons = $coupons;
		return $this;
	}

	protected function set_payments($value) {
		$payments = array();
		foreach ($value as $val) {
			/** @var Ecommerce_PaymentMethod $method */
			$method = Ecommerce_PaymentMethod::getFromIdEcommerce($val['method']);
			$method->nombre = $val['method'];

			/** @var Ecommerce_Payment $payment */
			$payment = Factory::getInstance()->getEcommerce_Payment();
			$payment->idEcommerce = $val['paymentid'];
			$payment->method = $method;
			$payment->instrumentId = $val['instrumentid'];
			$payment->amount = $val['amount'];
			$payment->authId = $val['authid'];
			$payment->info = $val['info'];

			$payments[] = $payment;
		}
		$this->_object->payments = $payments;
		return $this;
	}

	protected function set_delivery($value) {
		$delivery = Factory::getInstance()->getEcommerce_Delivery();
		$delivery->street = $value['street'];
		$delivery->city = $value['city'];
		$delivery->province = $value['province'];
		$delivery->pbox = $value['pbox'];
		$delivery->country = $value['country'];
		$delivery->phone = $value['phone'];
		$delivery->receptorName = $value['receptorname'];
		$delivery->expectedDate = empty($value['expecteddate']) ? null : Funciones::formatearFecha($value['expecteddate'], 'd/m/Y');
		$delivery->timeFrame = $value['timeframe'];

		$this->_object->delivery = $delivery;
		return $this;
	}
}

?>