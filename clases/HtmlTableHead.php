<?php

class HtmlTableHead extends Html {
	public	$id;
	public	$class;
	public	$colspan;
	public	$title;
	public	$style;
	public	$dataType;
	public	$content;

	public function __construct($config = array()) {
		$this->colspan = isset($config['colspan']) ? $config['colspan'] : '1';
		$this->dataType = isset($config['dataType']) ? $config['dataType'] : '';
		$this->style = new HtmlStyle();
		$this->constructFromArray($config);
	}

	public function create(){
		$string = '<th id="' . $this->id . '" class="' . $this->class . '" colspan="' . $this->colspan . '" title="' . $this->title . '" style="' . $this->style->toString() . '">';
		$string .= $this->content;
		$string .= '</th>';
		return $string;
	}
}

?>