<?php
/**
 * editar.php — Gestión de Cobranza (con logging y JSON garantizado)
 * Ruta: content/administracion/cobranzas/gestion_cobranza/editar.php
 * NOTA: Dejar estos logs solo durante diagnóstico.
 */

//// ——— Config de logs ———
$__log_dir  = '/var/www/encinitas/tmp';
$__log_file = $__log_dir . '/editar_guardar.log';
if (!is_dir($__log_dir)) { @mkdir($__log_dir, 0777, true); }
@touch($__log_file); @chmod($__log_file, 0777);
$__t0 = microtime(true);
function _elog($msg) {
    global $__log_file;
    @file_put_contents($__log_file, '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL, FILE_APPEND);
}

//// ——— Hardening PHP/headers ———
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', $__log_file);

// capturo cualquier echo/HTML legacy para que no rompa el JSON
ob_start();
if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

// si hay fatal, devolvemos JSON (así el loader no queda colgado)
register_shutdown_function(function () {
    global $__t0;
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        _elog("[FATAL] {$e['message']} @ {$e['file']}:{$e['line']}");
        if (!headers_sent()) header('Content-Type: application/json; charset=utf-8', true, 500);
        if (ob_get_length()) { ob_clean(); }
        echo json_encode([
            'ok'   => false,
            'fatal'=> true,
            'msg'  => $e['message'],
            'file' => $e['file'],
            'line' => $e['line'],
            'ms'   => (int)((microtime(true)-$__t0)*1000),
        ]);
        exit;
    }
});

require_once('../../../../premaster.php');

try {
    if (!Usuario::logueado()->puede('administracion/cobranzas/gestion_cobranza/editar/')) {
        throw new Exception('No tiene permisos para editar gestión de cobranza.');
    }

    // —— Entrada POST ——
    $idCliente            = Funciones::post('idCliente');
    $calificacion         = Funciones::post('calificacion');
    $observaciones        = Funciones::post('observaciones');              // textarea principal
    $observacionesVendedor= Funciones::post('observacionesVendedor');      // textarea vendedor

    _elog('POST '
        . 'idCliente='.(isset($idCliente)?$idCliente:'<null>')
        . ' len(obs)='.strlen((string)$observaciones)
        . ' calif='.(string)$calificacion
        . ' len(obsVend)='.strlen((string)$observacionesVendedor)
    );

    if (!isset($idCliente) || $idCliente === '' || $idCliente === null) {
        throw new FactoryExceptionRegistroNoExistente();
    }

    $cliente = Factory::getInstance()->getCliente($idCliente);
    if (!$cliente) {
        throw new FactoryExceptionRegistroNoExistente();
    }

    // —— Reglas por rol ——
    if (Usuario::logueado()->esVendedor()) {
        if (!$cliente->suVendedorEs(Usuario::logueado()->personal)) {
            throw new FactoryExceptionCustomException(
                'El cliente que intenta editar no corresponde a su cartera de clientes.'
            );
        }
        // Vendedor: normalmente NO toca obs de cobranza ni calificación
        // (dejar sólo observaciones del vendedor)
    } elseif (Usuario::logueado()->esPersonal()) {
        // Administrativo: puede tocar cobranza + calificación
        $cliente->observacionesGestionCobranza = $observaciones;
        $cliente->calificacion = $calificacion;
    }

    // Observaciones del vendedor (ambos roles pueden enviar)
    $cliente->observacionesVendedor = $observacionesVendedor;

    // —— Persistencia ——
    // Si tu Factory maneja transacciones, podrías envolver en begin/commit
    $cliente->guardar()->notificar('administracion/cobranzas/gestion_cobranza/editar/');

    // limpiar echos perdidos, si los hubo, para no romper JSON
    $stray = ob_get_clean();
    if ($stray !== '') { _elog('[WARN echo] '.$stray); }

    $ms = (int)((microtime(true)-$__t0)*1000);
    _elog("OK ({$ms} ms) clienteId=".$cliente->id);

    // Respuesta estándar
    Html::jsonSuccess('El cliente fue editado correctamente', $cliente);
    exit;

} catch (FactoryExceptionCustomException $ex) {
    if (ob_get_length()) { $stray = ob_get_clean(); if ($stray!=='') _elog('[WARN echo/EX] '.$stray); }
    _elog('[EX Custom] '.$ex->getMessage());
    Html::jsonError($ex->getMessage());
    exit;

} catch (FactoryExceptionRegistroNoExistente $ex) {
    if (ob_get_length()) { $stray = ob_get_clean(); if ($stray!=='') _elog('[WARN echo/EX] '.$stray); }
    _elog('[EX NoExiste] '.$ex->getMessage());
    Html::jsonError('El cliente que intentó editar no existe');
    exit;

} catch (Exception $ex) {
    if (ob_get_length()) { $stray = ob_get_clean(); if ($stray!=='') _elog('[WARN echo/EX] '.$stray); }
    _elog('[EX Gen] '.$ex->getMessage());
    Html::jsonError('Ocurrió un error al intentar editar el cliente: '.$ex->getMessage());
    exit;
}
