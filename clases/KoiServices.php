<?php

abstract class KoiServices {
    const SOCKET_IP = 'localhost';
    const SOCKET_PORT = '8000';
    const EOF_MARK = '<EOF>';
    const READ_BUFFER_SIZE = 512;

    protected $service;
    private $socket = false;
    private $stream = null;
    private $connected = false;
    private $transport = null;

    protected function connect() {
        if ($this->socket === false && $this->stream === null) {
            $this->createSocket();
        }
        if (!$this->connected) {
            $this->connectSocket();
        }
    }

    private function canUseSocketsExtension() {
        return function_exists('socket_create')
            && function_exists('socket_connect')
            && function_exists('socket_write')
            && function_exists('socket_read')
            && function_exists('socket_close')
            && function_exists('socket_last_error')
            && function_exists('socket_strerror');
    }

    private function createSocket() {
        if ($this->canUseSocketsExtension()) {
            $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($this->socket === false) {
                throw new Exception($this->getExceptionMsg('Ocurrio un error al intentar crear el socket'));
            }
            $this->transport = 'socket';
            return $this->socket;
        }

        $errno = 0;
        $errstr = '';
        $this->stream = @stream_socket_client(
            'tcp://' . self::SOCKET_IP . ':' . self::SOCKET_PORT,
            $errno,
            $errstr,
            15,
            STREAM_CLIENT_CONNECT
        );
        if ($this->stream === false) {
            throw new Exception($this->getExceptionMsg('Ocurrio un error al intentar crear la conexion con KoiServices', $errno, $errstr));
        }
        stream_set_timeout($this->stream, 30);
        $this->transport = 'stream';
        return $this->stream;
    }

    private function connectSocket() {
        if ($this->transport === 'socket') {
            $result = socket_connect($this->socket, self::SOCKET_IP, self::SOCKET_PORT);
            if ($result === false) {
                throw new Exception($this->getExceptionMsg('Ocurrio un error al intentar conectar el socket'));
            }
            $this->connected = true;
            return true;
        }

        if ($this->transport === 'stream' && is_resource($this->stream)) {
            $this->connected = true;
            return true;
        }

        throw new Exception($this->getExceptionMsg('Ocurrio un error al intentar conectar con KoiServices'));
    }

    private function closeSocket() {
        if ($this->transport === 'socket' && $this->socket !== false) {
            socket_close($this->socket);
            $this->socket = false;
        }
        if ($this->transport === 'stream' && is_resource($this->stream)) {
            fclose($this->stream);
            $this->stream = null;
        }
        $this->connected = false;
        $this->transport = null;
    }

    private function sendRequest($args) {
        $request = $this->service . ' ' . $args . self::EOF_MARK;
        if ($this->transport === 'socket') {
            return socket_write($this->socket, $request, strlen($request));
        }
        if ($this->transport === 'stream' && is_resource($this->stream)) {
            return fwrite($this->stream, $request);
        }
        return false;
    }

    private function getResponse() {
        $response = '';
        if ($this->transport === 'socket') {
            do {
                $recv = socket_read($this->socket, self::READ_BUFFER_SIZE);
                if ($recv === false) {
                    return false;
                } elseif ($recv !== '') {
                    $response .= $recv;
                }
            } while ($recv !== '');
            return $response;
        }

        if ($this->transport === 'stream' && is_resource($this->stream)) {
            while (!feof($this->stream)) {
                $recv = fread($this->stream, self::READ_BUFFER_SIZE);
                if ($recv === false) {
                    return false;
                }
                if ($recv !== '') {
                    $response .= $recv;
                }
                $meta = stream_get_meta_data($this->stream);
                if (!empty($meta['timed_out'])) {
                    return false;
                }
            }
            return $response;
        }

        return false;
    }

    protected function getExceptionMsg($msg = '', $errorcode = null, $errormsg = null) {
        if ($errorcode === null || $errormsg === null) {
            if ($this->transport === 'socket' && function_exists('socket_last_error') && function_exists('socket_strerror')) {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
            } else {
                $lastError = error_get_last();
                $errorcode = $errorcode === null ? 0 : $errorcode;
                if ($errormsg === null) {
                    $errormsg = $lastError && isset($lastError['message']) ? $lastError['message'] : 'Servicio no disponible';
                }
            }
        }

        return (!$msg ? 'Ocurrio un error en la conexion con KoiServices: ' : $msg) . '[' . $errorcode . '] ' . $errormsg;
    }

    protected function execute($args = '') {
        $error = false;
        $this->connect();
        $this->sendRequest($args);
        $response = $this->getResponse();
        if ($response === false) {
            $error = $this->getExceptionMsg('Ocurrio un error al recibir una respuesta de KoiServices');
        }
        $this->closeSocket();
        if ($error) {
            throw new Exception($error);
        }
        return $response;
    }
}