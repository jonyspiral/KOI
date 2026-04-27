<?php

class Mutex {
	private	$mutex_folder;
	private	$id;
	//private	$mutexId;
	private	$locked = false;
	private	$onWindows;
	private	$fileName;
	private	$filePointer;

	function __construct($mutexId) {
		if (empty($mutexId))
			throw new Exception('Error al crear mutex: ID vacío');
		$this->id = $mutexId;
		$this->mutex_folder = Config::pathBase . 'tmp/mutex/';
		$this->onWindows = (substr(PHP_OS, 0, 3) == 'WIN' ? true : false);
		$this->initializeMutex();
	}

	public function getId() {
		return $this->id;
	}

	public function initializeMutex() {
		if($this->onWindows) {
			$this->fileName = $this->id . '.txt';
		} else {
			//$this->mutexId = sem_get($this->id, 1); //La comento porque sem_get tira warning en windows (no existe)
			//if(!($this->mutexId))
				throw new Exception('Error al iniciar mutex ' . $this->id);
		}
		return true;
	}

	public function lock() {
		if($this->onWindows) {
			$this->filePointer = @fopen($this->mutex_folder . $this->fileName, 'w+');
			if(!$this->filePointer)
				throw new Exception('Error al intentar abrir archivo del mutex ' . $this->id);
			if(!flock($this->filePointer, LOCK_EX))
				throw new Exception('Error al intentar bloquear el mutex ' . $this->id);
		} else {
			//if (!sem_acquire($this->mutexId)) //La comento porque sem_acquire tira warning en windows (no existe)
				throw new Exception('Error al intentar bloquear el mutex ' . $this->id);
		}
		$this->locked = true;
		return true;
	}

	public function unlock() {
		if(!$this->locked)
			return true;
		if($this->onWindows) {
			if(!flock($this->filePointer, LOCK_UN))
				throw new Exception('Error al intentar desbloquear el mutex ' . $this->id);
			fclose($this->filePointer);
		} else {
			//if (!sem_release($this->mutexId)) //La comento porque sem_release tira warning en windows (no existe)
				throw new Exception('Error al intentar desbloquear el mutex ' . $this->id);
		}
		$this->locked = false;
		return true;
	}
}

?>