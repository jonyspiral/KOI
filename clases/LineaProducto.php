<?php

class LineaProducto extends Base {
	const		_primaryKey = '["id"]';

	public		$id;				//También tiene un id varchar(2)
	public		$anulado;
	public		$nombre;
	public		$tituloCatalogo;
	public		$tituloEcommerce;
	public		$origen;			//"N"acional
	public		$fechaLanzamiento;
	public		$fechaAlta;
	public		$fechaBaja;
	public		$fechaUltimaMod;

	//GETS y SETS
}

?>