<?php
// NO iniciar sesión aquí porque premaster.php ya lo hace
// @session_start(); // ← COMENTAR ESTA LÍNEA
require_once('premaster.php'); // ← Aquí se inicia la sesión
error_reporting(E_ALL & ~E_STRICT);

/* --- DEBUG HOOK: quitar luego --- */
if (isset($_GET['debug']) && $_GET['debug']==='sid') {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'sid'        => session_id(),
        'pagename'   => isset($_GET['pagename']) ? $_GET['pagename'] : null,
        'request_uri'=> $_SERVER['REQUEST_URI'],
        'session'    => array_intersect_key($_SESSION, array_flip([
            'usuarioLogueadoUser','usuarioLogueadoPass','empresa','user_type'
        ])),
    ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    exit;
}
/* --- /DEBUG HOOK --- */

// ===================================
// PROCESAR LOGIN SI HAY POST
// ===================================
if (isset($_POST['user']) && isset($_POST['pass'])) {
    try {
        // Intentar login
        UsuarioLogin::login();

        // Si llegamos aquí sin excepción, el login fue exitoso
        // Redirigir para evitar resubmit del formulario
        header('Location: ' . Config::siteRoot);
        exit;

    } catch (LoginFailException $e) {
        // Login falló, guardar mensaje de error en variable
        $loginError = $e->getMessage();
    } catch (Exception $e) {
        // Error inesperado
        $loginError = 'Error del sistema. Por favor contacte al administrador.';
        error_log('[MASTER] Login error: ' . $e->getMessage());
    }
}

function puedeSinLoguear($pagename) {
    //Esto es para las funcionalidades que no necesitan login
    $arrayExcepciones = array(
        'api',
        'fichaje'
    );
    if (in_array($pagename, $arrayExcepciones)) {
        return true;
    }
    return false;
}

function findRealPath($filename) {
    if (realpath($filename) == $filename) {
        return $filename;
    }
    $paths = explode(PATH_SEPARATOR, get_include_path());
    foreach ($paths as $path) {
        if (substr($path, -1) == DIRECTORY_SEPARATOR) {
            $fullpath = $path . $filename;
        } else {
            $fullpath = $path . DIRECTORY_SEPARATOR . $filename;
        }
        if (file_exists($fullpath)) {
            return $fullpath;
        }
    }
    return false;
}

$prefix = Usuario::logueado(true)->esCliente() ? 'cliente/' : '';
$pagename = $prefix . 'index'; //Default
$auxPagename = Funciones::get('pagename');
$idBuscar = Funciones::get('buscar');
if (Usuario::logueado() || puedeSinLoguear($auxPagename)) {
    $tit = explode('/', $auxPagename);
    $tit = ucfirst(implode(' ', explode('_', $tit[count($tit) - 1])));
    if (isset($auxPagename)) {
        $pagename = $prefix . Funciones::get('pagename');
    }
    if (file_exists($pagename . '.php')) {
        require_once($pagename . '.php');
    } else {
        if (!findRealPath('content/' . $pagename . '.php')) {
            if (findRealPath('content/' . $pagename . '/index.php')) {
                $pagename .= '/index';
            } else {
                $pagename = $prefix . 'index';
            }
        }
        if (!Usuario::logueado(true)->puede(substr($pagename, 0, -5))){
            $pagename = $prefix . 'index';
        }

        if (Usuario::logueado(true)->esCliente()) {
            include_once('content/cliente/main.php');
        } else {
            include_once('main.php');
        }
    }
} else { //Login
    // Mostrar formulario de login
    // La variable $loginError estará disponible si hubo error
    include_once('login.php');
}
?>