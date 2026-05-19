<?php

class Mutex {
    private $mutex_folder;
    private $id;
    private $locked = false;
    private $fileName;
    private $filePointer;

    function __construct($mutexId) {
        if (empty($mutexId))
            throw new Exception('Error al crear mutex: ID vacio');
        $this->id = $mutexId;
        $this->mutex_folder = Config::pathBase . 'tmp/mutex/';
        $this->initializeMutex();
    }

    public function getId() {
        return $this->id;
    }

    public function initializeMutex() {
        if (!is_dir($this->mutex_folder)) {
            if (!@mkdir($this->mutex_folder, 0777, true) && !is_dir($this->mutex_folder)) {
                throw new Exception('Error al crear carpeta de mutex ' . $this->id);
            }
        }

        $this->fileName = preg_replace('/[^A-Za-z0-9_-]/', '_', $this->id) . '.lock';
        return true;
    }

    public function lock() {
        $this->filePointer = @fopen($this->mutex_folder . $this->fileName, 'c+');
        if(!$this->filePointer)
            throw new Exception('Error al intentar abrir archivo del mutex ' . $this->id);
        if(!flock($this->filePointer, LOCK_EX))
            throw new Exception('Error al intentar bloquear el mutex ' . $this->id);
        $this->locked = true;
        return true;
    }

    public function unlock() {
        if(!$this->locked)
            return true;
        if(!is_resource($this->filePointer))
            return true;
        if(!flock($this->filePointer, LOCK_UN))
            throw new Exception('Error al intentar desbloquear el mutex ' . $this->id);
        fclose($this->filePointer);
        $this->filePointer = null;
        $this->locked = false;
        return true;
    }
}
