<?php
// Importante: bootstrap del framework ANTES de cualquier salida
require_once __DIR__ . '/../premaster.php';

// Opcionalmente pedimos al framework que repueble el usuario desde $_SESSION
try { UsuarioLogin::login(); } catch (Exception $e) { /* no hacemos nada */ }

header('Content-Type: text/plain; charset=iso-8859-1');

$u = Usuario::logueado();
if ($u) {
    echo "USUARIO={$u->id}\n";
    if (isset($_SESSION['empresa'])) echo "EMPRESA={$_SESSION['empresa']}\n";
    if (property_exists($u, 'tipo')) echo "TIPO={$u->tipo}\n";
} else {
    echo "SIN_USUARIO\n";
}




