<?php

/**
 * @property HtmlTableRow[]		$rows
 */

class HtmlTableBody extends Html {
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

	public function create($arrayHeads = null){
		$string = '';
		if (count($this->rows) > 0) {
			$string = '<tbody id="' . $this->id . '" class="' . $this->class . '" style="' . $this->style->toString() . '">';
			foreach($this->rows as $row){
				/** @var HtmlTableRow $row */
				$string .= $row->create($this, $arrayHeads);
			}
			$string .= '</tbody>';
		}
		return $string;
	}

	public function addRow(HtmlTableRow $row){
		$this->rows[] = $row;
	}
	public function removeRow($index){
		unset($this->rows[$index]);
		$this->rows = Funciones::reconstruirArray($this->rows);
	}
	
	public function rowClass($class){
		foreach($this->rows as $row){
			$row->class = $class;
		}
	}

	public function cellClass($class){
		foreach($this->rows as $row){
			$row->cellClass($class);
		}
	}
}

?>