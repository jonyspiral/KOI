<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simular POST desde formulario de login
$_POST['user'] = 'jony';
$_POST['pass'] = 'Route667'; // ← CAMBIA ESTO por la contraseña real sin encriptar
$_POST['empresa'] = '1';

// Cargar premaster
require_once __DIR__ . '/premaster.php';

echo "<pre>";
echo "=== TEST DE LOGIN COMPLETO ===\n\n";

// Mostrar qué datos recibimos
echo "1. Datos POST:\n";
echo "   Usuario: " . $_POST['user'] . "\n";
echo "   Empresa: " . $_POST['empresa'] . "\n\n";

// Intentar login
echo "2. Intentando login...\n";
try {
    // El login usa SHA1 para encriptar la contraseña
    $passwordEncriptado = Funciones::toSHA1($_POST['pass']);
    echo "   Password SHA1: $passwordEncriptado\n\n";
    
    // Verificar que el password en BD coincida
    $userDB = Datos::EjecutarSQLItem("SELECT cod_usuario, password, anulado FROM users WHERE cod_usuario = 'jony'");
    echo "   Password en BD: " . $userDB['password'] . "\n";
    echo "   ¿Coinciden? " . ($userDB['password'] === $passwordEncriptado ? "✅ SÍ" : "❌ NO") . "\n\n";
    
    if ($userDB['password'] !== $passwordEncriptado) {
        echo "   ⚠️ ADVERTENCIA: La contraseña no coincide.\n";
        echo "   Prueba con la contraseña original del usuario 'jony'\n\n";
    }
    
    // Intentar el login real
    echo "3. Ejecutando UsuarioLogin::login()...\n";
    UsuarioLogin::login();
    
    // Si llegamos aquí, el login fue exitoso
    $usuarioLogueado = Usuario::logueado();
    
    if ($usuarioLogueado) {
        echo "   ✅ LOGIN EXITOSO!\n\n";
        echo "4. Datos del usuario logueado:\n";
        echo "   ID: " . $usuarioLogueado->id . "\n";
        echo "   Tipo: " . $usuarioLogueado->tipoPersona . "\n";
        echo "   Anulado: " . $usuarioLogueado->anulado . "\n";
        echo "   Empresa en sesión: " . $_SESSION['empresa'] . "\n";
        
        // Verificar sesión
        echo "\n5. Verificación de sesión:\n";
        echo "   Session ID: " . session_id() . "\n";
        echo "   Usuario en sesión: " . (isset($_SESSION['usuarioLogueadoUser']) ? $_SESSION['usuarioLogueadoUser'] : 'NO SET') . "\n";
        echo "   Password en sesión: " . (isset($_SESSION['usuarioLogueadoPass']) ? 'SET (oculto)' : 'NO SET') . "\n";
        
    } else {
        echo "   ❌ Login falló pero no lanzó excepción\n";
    }
    
} catch (LoginFailException $e) {
    echo "   ❌ LOGIN FALLÓ\n";
    echo "   Razón: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "   ❌ ERROR INESPERADO\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN TEST ===\n";
echo "</pre>";
?>
