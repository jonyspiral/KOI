<?php

class Controller_Orders extends Controller_Base {
	protected	$model_name = 'Model_Order';
	protected	$permissions = array(
		'get' => 'ws/ecommerce/orders/buscar/',
		'create' => 'ws/ecommerce/orders/agregar/',
		'update' => 'ws/ecommerce/orders/editar/',
		'delete' => 'ws/ecommerce/orders/borrar/',
		//'autocomplete' => true
	);
	protected   $fields = array(
		'get' => array('id', 'datetime', 'status', 'totaldiscount', 'totalcoupon', 'grandtotal', 'documents'),
		'create' => array('id', 'datetime', 'totaldiscount', 'totalcoupon', 'grandtotal', 'customer', 'details', 'coupons', 'payments', 'delivery'),
		'update' => false //Por ahora no se pueden modificar
	);

	protected function before_create(&$data) {
		/** @var Model_Order $data */
		$orders = Factory::getInstance()->getListObject('Ecommerce_Order', 'anulado = ' . Datos::objectToDB('N') . ' AND cod_order_ecommerce = ' . $data->id);
		if (count($orders)) {
			throw new Model_Exception_RecordExists('La "order" que se intenta ingresar ya existe (N ' . $data->id . ')');
		}
	}
}

?>