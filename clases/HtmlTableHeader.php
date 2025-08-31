<?php

class HtmlTableHeader extends Html {
 	public	$id;
 	public	$class;
 	public	$style;
 	public	$rows;

	public function __construct($config = array()) {
		$this->rows = array();
		$this->style = new HtmlStyle();
		$this->constructFromArray($config);
	}

	public function create(){
		$string = '';
		if (!$this->isEmpty()) {
			$string = '<thead id="' . $this->id . '" class="' . $this->class . '" style="' . $this->style->toString() . '">';
			foreach($this->rows as $row){
				/** @var HtmlTableRow $row */
				$string .= $row->create($this);
			}
			$string .= '</thead>';
		}
		return $string;
	}

	public function addHeadRow(HtmlTableRow $row){
		$this->rows[] = $row;
	}
	public function removeHeadRow($index){
		unset($this->rows[$index]);
		$this->rows = Funciones::reconstruirArray($this->rows);
	}

	public function headClass($class){
		foreach($this->rows as $row){
			foreach($row->cells as $head){
				$head->class = $class;
			}
		}
	}

	public function isEmpty(){
		return (count($this->rows) == 0);
	}
}

?>