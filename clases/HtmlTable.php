<?php

class HtmlTable extends Html {
	public	$id;
	public	$class;
	public	$cantCols;
	public	$cantRows;
	public	$cellPadding;
	public	$cellSpacing;
	public	$width;
	public	$border;
	public	$style;
	public	$caption;
	public	$captionClass;
	public	$header;
	public	$body;
	public	$foot;

	public function __construct($config = array()) {
		$this->cantCols = 1;
		$this->cantRows = 1;
		$this->border = Funciones::keyIsSet($config, 'border', '0');
		$this->header = new HtmlTableHeader();
		$this->body = new HtmlTableBody();
		$this->foot = new HtmlTableFoot();
		$this->style = new HtmlStyle();
		$this->body->tdBaseClass = Funciones::keyIsSet($config, 'tdBaseClass', '0');
		$this->body->tdBaseClassLast = Funciones::keyIsSet($config, 'tdBaseClassLast', '0');
		$this->body->tdBaseClassFirst = Funciones::keyIsSet($config, 'tdBaseClassFirst', '0');
		$this->createHeaderFromArray(Funciones::keyIsSet($config, 'header', array()));
		$this->constructFromArray($config);
	}

	public function toString(){
		return $this->create(true);
	}

	/** @noinspection PhpInconsistentReturnPointsInspection */
	public function create($return = false){
		$echo = '';
		$id = 'id="' . $this->id . '" ';
		$class = 'class="' . $this->class . '" ';
		$cellpadding = 'cellpadding="' . $this->cellPadding . '" ';
		$cellspacing = 'cellspacing="' . $this->cellSpacing . '" ';
		$width = 'width="' . $this->width . '" ';
		$border = 'border="' . $this->border . '" ';
		$style = 'style="' . $this->style->toString() . '" ';
		$echo .= '<table ' . $id . $class . $cellpadding . $cellspacing . $width . $border . $style . '>';
		if (isset($this->caption))
			$echo .= '<caption class="caption' . ($this->captionClass ? ' ' . $this->captionClass : '') . '">' . $this->caption . '</caption>';
		$echo .= $this->header->create();
		$echo .= $this->body->create($this->header->rows); //Para que el create de CELL pueda formatear el contenido seg�n el dataType del header
		$echo .= $this->foot->create();
		$echo .= '</table>';
		if ($return)
			return $echo;
		echo $echo;
	}

	// Add y remove
	public function addHeadRow(HtmlTableRow $headRow){
		$this->header->addHeadRow($headRow);
	}
	public function removeHeadRow($index){
		$this->header->removeHeadRow($index);
	}
	public function addRow(HtmlTableRow $row){
		$this->body->addRow($row);
	}
	public function removeRow($index){
		$this->body->removeRow($index);
	}
	public function addFootRow(HtmlTableRow $headRow){
		$this->foot->addFootRow($headRow);
	}
	public function removeFootRow($index){
		$this->foot->removeFootRow($index);
	}

	//Classes
	public function headerClass($class){
		$this->header->class = $class;
	}
	public function bodyClass($class){
		$this->body->class = $class;
	}
	public function headClass($class){
		$this->header->headClass($class);
	}
	public function footClass($class){
		$this->foot->class = $class;
	}
	public function rowClass($class){
		$this->body->rowClass($class);
	}
	public function cellClass($class){
		$this->body->cellClass($class);
	}


	//############### getRowCellArray, getHeadArray y getFootArray ###############//
	public function getRowCellArray(&$rows, &$cells) {
		$rows = array();
		$cells = array();
		for($i = 0; $i < $this->cantRows; $i++) {
			$this->body->rows[$i] = new HtmlTableRow();
			for($j = 0; $j < $this->cantCols; $j++) {
				$this->body->rows[$i]->cells[$j] = new HtmlTableCell();
				$cells[$i][$j] = &$this->body->rows[$i]->cells[$j];
			}
			$rows[$i] = &$this->body->rows[$i];
		}
		//Al volver de esta funci�n, en $rows y en $cells hay punteros
		//directos a las filas y celdas de la tabla
	}
	public function getHeadArray(&$ths) {
		$ths = array();
		$this->header->rows[0] = new HtmlTableRow();
		for($i = 0; $i < $this->cantCols; $i++) {
			$this->header->rows[0]->cells[$i] = new HtmlTableHead();
			$ths[$i] = &$this->header->rows[0]->cells[$i];
		}
	}
	public function getFootArray(&$foots) {
		$foots = array();
		$this->foot->rows[0] = new HtmlTableRow();
		for($i = 0; $i < $this->cantCols; $i++) {
			$this->foot->rows[0]->cells[$i] = new HtmlTableCell();
			$foots[$i] = &$this->foot->rows[0]->cells[$i];
		}
	}
	//############### ###############//


	//############### Crea el contenido a partir de un array sin formato ###############//
	public function createBodyFromArray($array){
		$this->body = new HtmlTableBody();
		//Tiene que aplicar formatos seg�n los head
		foreach($array as $row){
			$tr = new HtmlTableRow();
			foreach($row as $attr => $value){
				if (count($this->header->rows[count($this->header->rows) - 1]) < count($row)){ //Si no existen tantos headers como columnas, los pongo
					$th = new HtmlTableHead();
					$th->content = $attr;
					$this->header->addHead($th);
				}
				$tc = new HtmlTableCell();
				$tc->content = $value;
				$tr->addCell($tc);
			}
			$this->body->addRow($tr);
		}
	}
	//############### ###############//


	//############### Crea el header a partir de un array ###############//
	public function createHeaderFromArray($array = null){
		if (!is_null($array) && count($array)) {
			$this->headerClass('tableHeader');
			$ths = array();
			$this->getHeadArray($ths);

			for ($i = 0; $i < $this->cantCols; $i++) {
				$conf = Funciones::keyIsSet($array, $i, array());
				if ($i == 0) {
					$ths[$i]->class = Funciones::keyIsSet($conf, 'class', 'cornerL5');
				} elseif ($i == ($this->cantCols - 1)) {
					$ths[$i]->class = Funciones::keyIsSet($conf, 'class', 'cornerR5 bLeftWhite');
				} else {
					$ths[$i]->class = Funciones::keyIsSet($conf, 'class', 'bLeftWhite');
				}
				if (array_key_exists('width', $conf)) {
					$ths[$i]->style->width = $conf['width'] . (is_int($conf['width']) ? '%' : '');
				}
				if (array_key_exists('dataType', $conf)) {
					$ths[$i]->dataType = $conf['dataType'];
				}
				if (array_key_exists('title', $conf)) {
					$ths[$i]->title = $conf['title'];
				}
				$ths[$i]->content = Funciones::keyIsSet($conf, 'content');
			}
		}
	}
	//############### ###############//
}

?>