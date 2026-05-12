<?php

/**
 * @property HtmlTableCell[]		$cells
 */

class HtmlTableRow extends Html {
	public	$id;
	public	$class;
	public	$rowspan;
	public	$style;
	public	$cells;

	public function __construct($config = array()) {
		$this->rowspan = isset($config['rowspan']) ? $config['rowspan'] : '1';
		$this->cells = array();
		$this->class = 'tableRow';
		$this->style = new HtmlStyle();
		$this->constructFromArray($config);
	}

	public function create(Html $parent, $arrayHeads = null){
		$string = '';
		if (count($this->cells) > 0) {
			$string = '<tr id="' . $this->id . '" class="' . $this->class . '" rowspan="' . $this->rowspan . '" style="' . $this->style->toString() . '">';
			$nroCol = 0;
			$span = 0;
			$i = 1;
			foreach($this->cells as $cell){
				if ($span == 0) {
					$string .= $cell->create($this->getDataType($nroCol, $arrayHeads), $parent, $i == 1, (count($this->cells) == $i));
					$cellColspan = Funciones::toInt($cell->colspan);
					if ($cellColspan > 1) {
						$span = $cellColspan - 1;
						$nroCol += $cellColspan;
					} else
						$nroCol++;
				} else
					$span--;
				$i++;
			}
			$string .= '</tr>';
		}
		return $string;
	}

	public function addCell($cell){
		$this->cells[] = $cell;
	}

	public function removeCell($index){
		unset($this->cells[$index]);
		$this->cells = Funciones::reconstruirArray($this->cells);
	}

	public function cellClass($class){
		foreach($this->cells as $cell){
			$cell->class = $class;
		}
	}

	private function getDataType($nroCol, $arrayHeads = null){
		if ($arrayHeads == null)
			return false;
		$arrayHeads = $arrayHeads[count($arrayHeads) - 1]->cells; //La última fila de los headers es la que tiene que tener los datatypes
		$i = 0;
		while($i <= $nroCol) {
			if (isset($arrayHeads[$i])){
				if ($nroCol == $i)
					return $arrayHeads[$i]->dataType;
				$headColspan = Funciones::toInt($arrayHeads[$i]->colspan);
				if ($headColspan > 1)
					$i += $headColspan;
				else
					$i++;
			} else {
				break;
			}
		}
		return '';
	}
}

?>