<?php

class HtmlStyle extends Html {
	public	$background_color;
	public	$color;
	public	$display;
	public	$font_size;
	public	$font_weight;
	public	$height;
	public	$text_align;
	public	$width;

	public function __construct($config = array()) {
		$this->constructFromArray($config);
	}

	public function toString(){
		$string = '';
		foreach($this as $attr => $value){
			$attr = str_replace('_', '-', $attr); //No puedo crear attributes con nombres con guiones medios
			if (!empty($value))
				$string .= $attr . ': ' . $value . '; ';
		}
		return $string;
	}

	public static function init($varInit){
		$style = new HtmlStyle();
		if (Funciones::getType($varInit) == 'string')
			$style->initFromString($varInit);
		elseif (Funciones::getType($varInit) == 'HtmlStyle')
			foreach($varInit as $attr => $value)
				$style->$attr = $value;
		return $style;	
	}

	private function initFromString($string){
		$string = str_replace('; ', ';', str_replace(' ; ', ';', $string)); //Primero le saco los ' ; ' y dsp los '; '
		$arrayStyles = explode(';', $string);
		foreach($arrayStyles as $style){
			$style = str_replace(': ', ':', str_replace(' : ', ':', $style)); //Primero le saco los ' : ' y dsp los ': '
			$propVal = explode(':', $style);
			if (count($propVal) == 2){
				$attr = str_replace('-', '_', $propVal[0]); //No puedo crear attributes con nombres con guiones medios
				if (property_exists($this, $attr)){
					$value = $propVal[1];
					$this->$attr = $value;
				}
			}
		}
	}
}

?>