<?php

/**
 * @property HtmlTableRow[]		$rows
 */

class HtmlTableFoot extends Html {
 	public	$id;
 	public	$class;
 	public	$style;
 	public	$rows;
	public	$tdBaseClass;
	public	$tdBaseClassLast;
	public	$tdBaseClassFirst;

	public function __construct($config = array()) {
		$this->rows = array();
		$this->style = new HtmlStyle();
		$this->constructFromArray($config);
	}

	public function create(){
		$string = '';
		if (!$this->isEmpty()) {
			$string = '<tfoot id="' . $this->id . '" class="' . $this->class . '" style="' . $this->style->toString() . '">';
			foreach($this->rows as $row){
				$row->class = '';
				$string .= $row->create($this);
			}
			$string .= '</tfoot>';
		}
		return $string;
	}

	public function addFootRow(HtmlTableRow $row){
		$this->rows[] = $row;
	}
	public function removeFootRow($index){
		unset($this->rows[$index]);
		$this->rows = Funciones::reconstruirArray($this->rows);
	}

	public function isEmpty(){
		return (count($this->rows) == 0);
	}
}

?>