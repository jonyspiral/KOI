<?php

class Ecommerce_Configuration extends Base {
	const		_primaryKey = '["id"]';

	//TODO: Hacer estas configuraciones VIA TABLA y PANEL DE CONFIGURACIONES DE E-COMMERCE
	const		ECOMMERCE_ID_STATUS_INICIAL = Ecommerce_OrderStatus_Cobrado::STATUS_ID; //Es el ID del STATUS en el cual intento empezar
	const		ECOMMERCE_ID_STATUS_HASTA_OBLIGATORIO = Ecommerce_OrderStatus_Pedido::STATUS_ID; //Es el ID del status hasta el cual TENGO QUE llegar sí o sí al momento de crear un pedido nuevo. Si no se puede llegar, vuelvo atrás
	const		ECOMMERCE_ID_STATUS_HASTA_INTENTAR = Ecommerce_OrderStatus_Predespachado::STATUS_ID; //Es el ID del status hasta el cual INTENTO llegar al momento de crear un pedido. Si no se puede llegar, me quedo hasta donde llegué

	const		ECOMMERCE_ID_ALMACEN = 14;
	const		ECOMMERCE_ID_CLIENTE = 663;
	const		ECOMMERCE_ID_SUCURSAL = 1;
	const		ECOMMERCE_ID_CAJA_COBRANZA = 114;
	const		ECOMMERCE_ID_IMPUTACION = '1131100';

	public		$id;
	public		$valor;
	public		$descripcion;
	public		$idUsuarioUltimaMod;
	protected	$_usuarioUltimaMod;
	public		$fechaUltimaMod;

	//GETS y SETS
}

?>