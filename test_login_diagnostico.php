<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar premaster
require_once __DIR__ . '/premaster.php';

echo "<pre>";
echo "=== DIAGNÓSTICO COMPLETO DE LOGIN ===\n\n";

// 1. Verificar conexión a BD
echo "1. Conexión a Base de Datos:\n";
try {
    $db = Factory::getInstance()->db();
    echo "   ✅ Conexión exitosa\n";
    echo "   Host: " . Config::mysql_host . "\n";
    echo "   BD: " . Config::mysql_db . "\n\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    exit;
}

// 2. Verificar tabla users
echo "2. Estructura de tabla 'users':\n";
try {
    $columns = Datos::EjecutarSQL("SHOW COLUMNS FROM users");
    echo "   Columnas encontradas:\n";
    foreach ($columns as $col) {
        echo "   - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// 3. Listar usuarios existentes (primeros 5)
echo "3. Usuarios en la base de datos:\n";
try {
    $usuarios = Datos::EjecutarSQL("SELECT cod_usuario, nombre, anulado FROM users LIMIT 5");
    if (count($usuarios) > 0) {
        foreach ($usuarios as $u) {
            echo "   - Usuario: '" . $u['cod_usuario'] . "' | Nombre: " . $u['nombre'] . " | Anulado: " . $u['anulado'] . "\n";
        }
    } else {
        echo "   ⚠️ No hay usuarios en la tabla\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// 4. Probar buscar un usuario específico (usa uno de los que aparecieron arriba)
echo "4. Prueba de búsqueda de usuario:\n";
$usuarioPrueba = 'jony'; // ← CAMBIA ESTO por un usuario real de tu lista
echo "   Buscando usuario: '$usuarioPrueba'\n";

try {
    // Simular lo que hace Factory->getUsuarioLogin()
    $usuarioLogin = new UsuarioLogin();
    $usuarioLogin->modo = Modos::insert;
    $usuarioLogin->id = $usuarioPrueba;
    
    // Ver qué SQL se genera
    $mapper = new Mapper();
    $sqlGenerado = $mapper->getQueryInstancia($usuarioLogin, Modos::select);
    echo "   SQL generado:\n   $sqlGenerado\n\n";
    
    // Ejecutar el SQL
    echo "   Ejecutando SQL...\n";
    $resultado = Datos::EjecutarSQL($sqlGenerado);
    
    if ($resultado && count($resultado) > 0) {
        echo "   ✅ Usuario encontrado:\n";
        print_r($resultado[0]);
    } else {
        echo "   ❌ Usuario NO encontrado (0 resultados)\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Excepción: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FIN DIAGNÓSTICO ===\n";
echo "</pre>";
?>
