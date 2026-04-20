<?php

class CondicionIva extends Base {
	const		_primaryKey = '["id"]';

	public		$id;
	public		$nombre;
	public		$anulado;
	public		$letraFactura;
	public		$letraFacturaProveedor;
	public		$porcentajes;			//Array del 1 al 5 con distintos porcentajes que puede tener esa condición de iva (ver RubroIva)
	public		$tratamiento;			//Es cómo debe mostrarse el IVA. Incluido ('I') o Discriminado ('D')

	//GETS y SETS
}

?>