<?php

class Array2Csv {
	public	$fileName;
	private $array = array();
	private	$created;
	private	$localTmpFolder;

	public function __construct(){
		$this->created = false;
		$this->localTmpFolder = Config::pathBase . 'tmp/array2csv/';
	}

	public function create(){
		try {
			$this->createCsv();
			$this->created = true;
		} catch (Exception $ex) {
			$this->deleteFiles();
			throw $ex;
		}
	}

	public function download(){
		if (!$this->created)
			$this->create();
		$csvPath = $this->localTmpFolder . $this->fileName . '.csv';
		header("Content-Length: " . filesize ($csvPath));
		header("Content-type: text/csv");
		header("Content-disposition: attachment; filename=" . basename($csvPath));
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		ob_clean();
		flush();
		readfile($csvPath);
	}

	public function open(){
		if (!$this->created)
			$this->create();
		header('Content-type: text/csv');
		readfile($this->localTmpFolder . $this->fileName . '.csv');
	}

	public function loadArray($array) {
		$this->array = $array;
	}

	private function deleteCsv(){
		$fileName = $this->localTmpFolder . $this->fileName . '.csv';
		$this->delete($fileName);
	}

	public function deleteFiles(){
		$this->deleteCsv();
	}

	private function createCsv() {
		if (!count($this->array)) {
			throw new FactoryExceptionRegistroNoExistente('No se puede crear un CSV vacío');
		}
		if (empty($this->fileName) || !isset($this->fileName))
			$this->fileName = $this->getRandomName();
		$csvPath = $this->localTmpFolder . $this->fileName . '.csv';
		$fp = fopen($csvPath, 'w');
		foreach ($this->array as $values) {
			fputcsv($fp, $values);
		}
		fclose($fp);
		return true;
	}

	private function delete($fileName) {
		if (file_exists($fileName))
			unlink($fileName);
	}

	private function getRandomName(){
		return uniqid();
	}
}


?>