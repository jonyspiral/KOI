<?php
/**
 * TEST COMPLETO DEL FLUJO DE LOGIN
 * Simula el flujo de master.php paso a paso
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// NO iniciar sesión aquí, dejar que premaster.php lo haga
// session_start(); ← COMENTAR ESTA LÍNEA

echo "<pre>";
echo "=== DIAGNÓSTICO DEL FLUJO DE LOGIN ===\n\n";

// PASO 1: Verificar que premaster.php existe
echo "1. Verificando premaster.php... ";
$premasterPath = __DIR__ . '/premaster.php';
if (!file_exists($premasterPath)) {
    die("❌ ERROR: premaster.php no existe en " . __DIR__ . "\n");
}
echo "✅ OK\n";

// PASO 2: Cargar premaster.php con captura de errores
echo "2. Cargando premaster.php... ";
ob_start();
try {
    require_once($premasterPath);
    $output = ob_get_clean();
    if (trim($output)) {
        echo "\n⚠️ ADVERTENCIA: premaster.php generó output:\n$output\n";
    } else {
        echo "✅ OK\n";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    die();
}

// PASO 3: Verificar que Config está cargado
echo "3. Verificando Config... ";
if (!class_exists('Config')) {
    die("❌ ERROR: Config no se cargó\n");
}
echo "✅ OK (DB: " . Config::mysql_db . ")\n";

// PASO 4: Verificar Factory
echo "4. Verificando Factory... ";
if (!class_exists('Factory')) {
    die("❌ ERROR: Factory no se cargó\n");
}
try {
    $factory = Factory::getInstance();
    echo "✅ OK\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    die();
}

// PASO 5: Verificar DbMysql
echo "5. Verificando DbMysql... ";
try {
    $db = $factory->db();
    echo "✅ OK (clase: " . get_class($db) . ")\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    die();
}

// PASO 6: Test de conexión MySQL
echo "6. Test de conexión MySQL... ";
try {
    $result = $db->query("SELECT VERSION() AS v");
    echo "✅ OK (v" . $result[0]['v'] . ")\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    die();
}

// PASO 7: Verificar Usuario
echo "7. Verificando clase Usuario... ";
if (!class_exists('Usuario')) {
    die("❌ ERROR: Usuario no se cargó\n");
}
echo "✅ OK\n";

// PASO 8: Simular login (la sesión ya fue iniciada por premaster.php)
echo "8. Test de Usuario::logueado()... ";
try {
    $usuario = Usuario::logueado(true);
    if ($usuario && isset($usuario->id)) {
        echo "✅ OK (logueado: " . $usuario->id . ")\n";
    } else {
        echo "✅ OK (no hay sesión activa)\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    die();
}

// PASO 9: Verificar que login.php existe
echo "9. Verificando login.php... ";
$loginPath = __DIR__ . '/login.php';
if (!file_exists($loginPath)) {
    echo "❌ ERROR: login.php no existe\n";
} else {
    echo "✅ OK\n";
}

// PASO 10: Verificar que main.php existe
echo "10. Verificando main.php... ";
$mainPath = __DIR__ . '/main.php';
if (!file_exists($mainPath)) {
    echo "❌ ERROR: main.php no existe\n";
} else {
    echo "✅ OK\n";
}

echo "\n✅ FLUJO DE LOGIN VALIDADO CORRECTAMENTE\n";
echo "\nPróximos pasos:\n";
echo "1. Navegar a http://koi.spiralshoes.com/ (debe mostrar login.php)\n";
echo "2. Ingresar credenciales válidas\n";
echo "3. Si falla, revisar logs en tmp/logs/\n";
echo "</pre>";