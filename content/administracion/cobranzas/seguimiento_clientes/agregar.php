<?php
/**
 * agregar.php — Seguimiento de Clientes (Gestión de Cobranzas)
 * PHP 5.6 compatible — esquema sin AUTO_INCREMENT (MAX(id)+1 con Mutex)
 */

//// ——— Logs ———
$__log_dir  = '/var/www/encinitas/tmp';
$__log_file = $__log_dir . '/seguimiento_agregar.log';
if (!is_dir($__log_dir)) { @mkdir($__log_dir, 0777, true); }
@touch($__log_file); @chmod($__log_file, 0777);
$__t0 = microtime(true);
function _elog_seg($msg) {
    global $__log_file;
    @file_put_contents($__log_file, '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL, FILE_APPEND);
}

//// ——— PHP/headers ———
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', $__log_file);

ob_start();
if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

// Fatal handler
register_shutdown_function(function () {
    global $__t0;
    $e = error_get_last();
    if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        _elog_seg("[FATAL] ".$e['message']." @ ".$e['file'].":".$e['line']);
        if (!headers_sent()) header('Content-Type: application/json; charset=utf-8', true, 500);
        if (ob_get_length()) { ob_clean(); }
        echo json_encode(array(
            'ok'   => false,
            'fatal'=> true,
            'msg'  => $e['message'],
            'file' => $e['file'],
            'line' => $e['line'],
            'ms'   => (int)((microtime(true)-$__t0)*1000),
        ));
        exit;
    }
});

// require robusto
require_once(realpath(dirname(__FILE__) . '/../../../../premaster.php'));

try {
    // ACL
    if (!Usuario::logueado()->puede('administracion/cobranzas/seguimiento_clientes/agregar/')) {
        throw new Exception('No tiene permisos para agregar gestiones de seguimiento.');
    }

    // Solo POST
    $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    if (strtoupper($method) !== 'POST') {
        throw new Exception('Método no permitido (se esperaba POST).');
    }

    // Entrada
    $idCliente     = Funciones::post('idCliente');
    $fechaForm     = Funciones::post('fecha');          // opcional
    $observaciones = Funciones::post('observaciones');

    _elog_seg('POST idCliente='. (isset($idCliente)?$idCliente:'<null>')
      .' len(obs)='. strlen((string)$observaciones)
      .' fechaForm='. (string)$fechaForm
    );

    // Validaciones
    if (empty($idCliente) || empty($observaciones)) {
        throw new FactoryExceptionCustomException('Todos los campos son obligatorios');
    }

    // Normalización fecha
    $fechaGestion = $fechaForm ? $fechaForm : Funciones::hoy();

    // Modelo + cliente
    $seguimiento = Factory::getInstance()->getSeguimientoCliente();
    if (!$seguimiento) {
        throw new Exception('No se pudo instanciar el modelo de SeguimientoCliente.');
    }

    $cliente = Factory::getInstance()->getCliente($idCliente);
    if (!$cliente) {
        throw new FactoryExceptionRegistroNoExistente();
    }

    // Transacción (si existe API)
    $factory = Factory::getInstance();
    $txBegan = false;
    if (method_exists($factory, 'begin') || method_exists($factory, 'beginTransaction')) {
        if (method_exists($factory, 'begin')) { $factory->begin(); }
        else { $factory->beginTransaction(); }
        $txBegan = true;
        _elog_seg('TX BEGIN');
    }

    // === Reservar ID con Mutex (no cambiamos el esquema) ===
    $db = Factory::getInstance()->db();
    $mtx = null;
    $nextId = null;

    // Usamos el Mutex del proyecto (Linux flock)
    if (class_exists('Mutex')) {
        $mtx = new Mutex('gcc_pk_mutex');
        $mtx->lock();
        _elog_seg('MUTEX LOCK gcc_pk_mutex');
    }

    try {
        // Calculamos MAX(id)+1 de forma atómica bajo el mutex
        $row = $db->queryOne("SELECT IFNULL(MAX(id),0)+1 AS next FROM gestiones_clientes_cobranza");
        $nextId = ($row && isset($row['next'])) ? (int)$row['next'] : 1;
        _elog_seg('NEXT ID (MAX+1) = '.$nextId);

        // Seteo campos — OBLIGATORIO: id explícito porque la tabla no es AUTO_INCREMENT
        $seguimiento->id            = $nextId;
        $seguimiento->cliente       = $cliente;              // mapea a cod_cli
        $seguimiento->fechaGestion  = $fechaGestion;         // mapea a fecha_gestion
        $seguimiento->observaciones = $observaciones;
        $seguimiento->estado        = 0;                     // tinyint/int
        // Si anulado es NOT NULL sin default, seteamos:
        if (property_exists($seguimiento, 'anulado')) {
            $seguimiento->anulado = 'N';
        }

        // Persistir (el mapper INSERT debe incluir `id` y, si aplica, `anulado`)
        $seguimiento->guardar();

    } finally {
        if ($mtx) {
            $mtx->unlock();
            _elog_seg('MUTEX UNLOCK gcc_pk_mutex');
        }
    }

    // Commit
    if ($txBegan) {
        if (method_exists($factory, 'commit')) { $factory->commit(); }
        else { $factory->commitTransaction(); }
        _elog_seg('TX COMMIT');
    }

    // Limpiar echos perdidos
    $stray = ob_get_clean();
    if ($stray !== '') { _elog_seg('[WARN echo] '.$stray); }

    $ms = (int)((microtime(true)-$__t0)*1000);
    _elog_seg("OK ({$ms} ms) idCliente=".$idCliente." idGenerado=".$nextId);

    Html::jsonSuccess('La gestión se agregó correctamente', $seguimiento->expand());
    exit;

} catch (FactoryExceptionCustomException $ex) {
    if (ob_get_length()) { $stray = ob_get_clean(); if ($stray!=='') _elog_seg('[WARN echo/EX] '.$stray); }
    _elog_seg('[EX Custom] '.$ex->getMessage());
    Html::jsonError($ex->getMessage());
    exit;

} catch (FactoryExceptionRegistroNoExistente $ex) {
    if (ob_get_length()) { $stray = ob_get_clean(); if ($stray!=='') _elog_seg('[WARN echo/EX] '.$stray); }
    _elog_seg('[EX NoExiste] '.$ex->getMessage());
    Html::jsonError('El cliente que intentó gestionar no existe');
    exit;

} catch (Exception $ex) {
    if (ob_get_length()) { $stray = ob_get_clean(); if ($stray!=='') _elog_seg('[WARN echo/EX] '.$stray); }
    _elog_seg('[EX Gen] '.$ex->getMessage());
    Html::jsonError('Ocurrió un error al intentar agregar la gestión');
    exit;
}
