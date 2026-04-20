<?php

class Ecommerce_Document {
	public		$docid;
	public		$doctype;
	public		$docnum;
	public		$url;

	public function __construct($config = array()) {
		foreach($config as $attr => $value){
			if (property_exists($this, $attr)) {
				$this->$attr = $value;
			}
		}
	}

	public function getImgName() {
		$imgName = 'tilde';
		switch ($this->doctype) {
			case 'Recibo': $imgName = 'rec'; break;
			case 'Predespacho': $imgName = 'predespachado'; break;
			case 'Despacho': $imgName = 'despachado'; break;
			case 'Remito': $imgName = 'remitido'; break;
			case 'Factura': $imgName = 'facturado'; break;
			case 'CSV Andreani': $imgName = 'csv'; break;
		}
		return $imgName;
	}
}

?>