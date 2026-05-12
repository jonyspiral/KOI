<?php

require_once(Config::pathBase . 'includes/myPHPExcel/PHPExcel/PHPExcel.php');

class myPHPExcel extends PHPExcel {
	public function __construct(){
		parent::__construct();
		$this->getDefaultStyle()->getFont()->setName('Calibri');
		$this->getDefaultStyle()->getFont()->setSize(10);
	}

	public function crearHoja($numero = -1){
		if ($numero >= 0){
			$this->createSheet($numero);
			$this->hojaActiva($numero);
		} else
			$this->createSheet();
	}

	public function hojaActiva($numero = 0){
		$this->setActiveSheetIndex($numero);
	}

	public function setOrientacionHorizontal() {
		$this->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	}

	public function setOrientacionVertical() {
		$this->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	}

	public function setMargins($top, $right, $bottom, $left) {
		$this->getActiveSheet()->getPageMargins()->setTop($top)->setRight($right)->setBottom($bottom)->setLeft($left);
	}

	public function tituloHoja($titulo = ''){
		$this->getActiveSheet()->setTitle($titulo);
	}

	public function texto($celda = 'A1', $valor = ''){
		//PHPExcel_Style_NumberFormat::
		//PHPExcel_Cell_DataType::
		$this->getActiveSheet()->getCell($celda)->setValue($valor);
	}

	public function combinarCeldas($rango = 'A1:A2'){
		$this->getActiveSheet()->mergeCells($rango);
	}

	public function centrarHorizontalVertical($celdaRango = 'A1'){
		$this->getActiveSheet()->getStyle($celdaRango)->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle($celdaRango)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->getActiveSheet()->getStyle($celdaRango)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	}

	public function alignLeft($celdaRango = 'A1'){
		$this->getActiveSheet()->getStyle($celdaRango)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	}

	public function alignCenter($celdaRango = 'A1'){
		$this->getActiveSheet()->getStyle($celdaRango)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}

	public function alignRight($celdaRango = 'A1'){
		$this->getActiveSheet()->getStyle($celdaRango)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	}

	public function bold($celdaRango = 'A1') {
		$this->getActiveSheet()->getStyle($celdaRango)->getFont()->setBold(true);
	}

	public function currency($celdaRango = 'A1') {
		//Además hay que formatear correctamente el valor del campo (Ej: $ -10.000,00 || $ 1.235.123,21)
		$currency = '$ #,##0.00;$ -#,##0.00';
		$this->getActiveSheet()->getStyle($celdaRango)->getNumberFormat()->setFormatCode($currency);
	}

	public function numericFloat($celdaRango = 'A1') {
		$this->getActiveSheet()->getStyle($celdaRango)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
	}

	public function numeric($celdaRango = 'A1') {
		$this->getActiveSheet()->getStyle($celdaRango)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
	}

	public function font($celdaRango = 'A1', $font = 'Calibri', $size = 10, $color = 'FF000000', $bold = false, $subrayado = false){
		if ($font != '') $this->getActiveSheet()->getStyle($celdaRango)->getFont()->setName($font);
		if ($size != 0) $this->getActiveSheet()->getStyle($celdaRango)->getFont()->setSize($size);
		$this->getActiveSheet()->getStyle($celdaRango)->getFont()->setBold($bold);
		if ($color != '') $this->getActiveSheet()->getStyle($celdaRango)->getFont()->getColor()->setARGB($color);
		if ($subrayado) $this->getActiveSheet()->getStyle($celdaRango)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
	}

	public function colorRelleno($celdaRango = 'A1:A2', $color = 'FF000000'){
		$this->getActiveSheet()->getStyle($celdaRango)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$this->getActiveSheet()->getStyle($celdaRango)->getFill()->getStartColor()->setARGB($color);
	}

	public function color($celdaRango = 'A1:A2', $color = 'FF000000'){
		$this->getActiveSheet()->getStyle($celdaRango)->getFont()->getColor()->setARGB($color);
	}

	public function rowHeight($row = 1, $height = 4){
		$this->getActiveSheet()->getRowDimension($row)->setRowHeight($height);
	}

	public function columnWidth($column = 'A', $width = 4){
		$this->getActiveSheet()->getColumnDimension($column)->setWidth($width);
	}

	public function link($celda = 'A1', $link = '#'){
		$this->getActiveSheet()->getCell($celda)->getHyperlink()->setUrl($link);
	}

	public function image($path = '', $celda = 'A1', $height = '', $width = ''){
		$dibujante = new PHPExcel_Worksheet_Drawing();
		$dibujante->setPath($path);
		$dibujante->setCoordinates($celda);
		if ($height != '')
			$dibujante->setHeight($height);
		if ($width != '')
			$dibujante->setWidth($width);
		$dibujante->setWorksheet($this->getActiveSheet());
	}

	public function estiloDeArray($celdaRango = 'A1', $array){
		$this->getActiveSheet()->getStyle($celdaRango)->applyFromArray($array);
	}

	public function guardar($path = 'Excel.xls'){
		$this->hojaActiva(0);
		$writer = PHPExcel_IOFactory::createWriter($this, 'Excel5');
		$writer->save($path);
	}
}

?>