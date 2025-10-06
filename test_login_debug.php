<?php
/**
 * DEBUG DEL PROCESO DE LOGIN
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('premaster.php');

echo "<pre>";
echo "=== DEBUG DE LOGIN ===\n\n";

// Simular POST del formulario de login
$_POST['user'] = 'jony';  // ← CAMBIA ESTO
$_POST['pass'] = 'Route667'; // ← CAMBIA ESTO
$_POST['empresa'] = '1';

echo "1. Datos POST recibidos:\n";
echo "   Usuario: " . $_POST['user'] . "\n";
echo "   Empresa: " . $_POST['empresa'] . "\n\n";

echo "2. Intentando login...\n";

try {
    // Verificar si existe la clase UsuarioLogin
    if (!class_exists('UsuarioLogin')) {
        die("❌ ERROR: Clase UsuarioLogin no existe\n");
    }
    
    echo "   ✅ Clase UsuarioLogin cargada\n";
    
    // Intentar el login
    $resultado = UsuarioLogin::login($_POST['user'], $_POST['pass'], $_POST['empresa']);
    
    echo "3. Resultado del login:\n";
    var_dump($resultado);
    
    // Verificar sesión
    echo "\n4. Verificar sesión después del login:\n";
    echo "   usuarioLogueadoUser: " . (isset($_SESSION['usuarioLogueadoUser']) ? $_SESSION['usuarioLogueadoUser'] : 'NO SETEADO') . "\n";
    echo "   empresa: " . (isset($_SESSION['empresa']) ? $_SESSION['empresa'] : 'NO SETEADO') . "\n";
    
    // Verificar Usuario::logueado()
    echo "\n5. Usuario::logueado():\n";
    $usuario = Usuario::logueado();
    if ($usuario) {
        echo "   ✅ Usuario logueado: ID=" . $usuario->id . "\n";
    } else {
        echo "   ❌ Usuario NO logueado\n";
    }
    
} catch (Exception $e) {
    echo "❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";
