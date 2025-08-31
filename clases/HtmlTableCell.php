<?php

class HtmlTableCell extends Html {
	public	$id;
	public	$class;
	public	$rel;
	public	$colspan;
	public	$title;
	public	$style;
	public	$content;

	public function __construct($config = array()) {
		$this->colspan = isset($config['colspan']) ? $config['colspan'] : '1';
		$this->style = new HtmlStyle();
		$this->baseclass = 'pRight10 pLeft10 bLeftDarkGray ';
		$this->baseclasslast = 'pRight10 pLeft10 bLeftDarkGray bRightDarkGray ';
		$this->constructFromArray($config);
	}

	public function create($dataType, Html $parent, $first = false, $last = false){
		$this->format($dataType);
		$string = '<td id="' . $this->id . '" class="' . $this->baseclass($parent, $first, $last) . $this->class . '" rel="' . $this->rel . '" colspan="' . $this->colspan . '" title="' . $this->title . '" style="' . $this->style->toString() . '">';
		if (strstr($this->content, '</') || strstr($this->content, '/>'))
			$string .= $this->content;
		else
			$string .= '<label>' . $this->content . '</label>';
		$string .= '</td>';
		return $string;
	}

	private function baseClass(Html $parent, $first = false, $last = false) {
		$return = '';
		if ($parent instanceof HtmlTableBody || $parent instanceof HtmlTableFoot) {
			/** @var HtmlTableBody|HtmlTableFoot $parent */
			if ($first && $parent->tdBaseClassFirst) {
				$return = $parent->tdBaseClassFirst;
			} elseif ($last && $parent->tdBaseClassLast) {
				$return = $parent->tdBaseClassLast;
			} else {
				$return = $parent->tdBaseClass;
			};
		}
		return $return . ' ';
	}

	private function format($dataType){
		if ($dataType !== false) {
			switch($dataType) {
				case 'Fecha':
					$this->content = Funciones::formatearFecha($this->content);
					$this->class .= ' aCenter';
					break;
				case 'Moneda':
					$this->content = Funciones::formatearMoneda($this->content);
					$this->class .= ' aRight';
					break;
				case 'Entero':
					$this->content = Funciones::toInt($this->content);
					$this->class .= ' aRight';
					break;
				case 'DosDecimales':
					$this->content = Funciones::formatearDecimales($this->content, 2);
					$this->class .= ' aRight';
					break;
				case 'CuatroDecimales':
					$this->content = Funciones::formatearDecimales($this->content, 4);
					$this->class .= ' aRight';
					break;
				case 'Center':
					$this->class .= ' aCenter';
					break;
				case 'Right':
					$this->class .= ' aRight';
					break;
				case 'Texto':
				default:
					$this->content = Funciones::toString($this->content);
					$this->class .= ' aLeft';
					break;
			}
		}
	}
}

?>