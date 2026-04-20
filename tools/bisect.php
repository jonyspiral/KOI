<?php
// /var/www/encinitas/tools/bisect.php
// MODO SAFE: no enviamos nada al cliente antes de incluir premaster.php

$step = isset($_GET['step']) ? $_GET['step'] : '';

if ($step === 'static') {
    header('Content-Type: text/plain; charset=UTF-8');
    echo "[BISECT] step=static\nOK: PHP ejecuta (sin includes)\n";
    exit;
}

if ($step === 'includes') {
    ob_start();
    echo "[BISECT] step=includes\nA: antes de includes.php\n";
    require_once __DIR__ . '/../includes.php';
    echo "B: después de includes.php\n";
    $out = ob_get_clean();
    header('Content-Type: text/plain; charset=UTF-8');
    echo $out;
    exit;
}

if ($step === 'premaster' || $step === 'master_head' || $step === 'master_full') {
    // Nada de headers ni echo ANTES de premaster.php
    ob_start();
    echo "[BISECT] step={$step}\nA: antes de premaster.php\n";
    require_once __DIR__ . '/../premaster.php';
    echo "B: después de premaster.php\n";
    if ($step === 'master_head') {
        echo "B2: cortamos ANTES de la lógica de master\n";
    } elseif ($step === 'master_full') {
        echo "C: ahora incluimos master.php completo…\n";
        require_once __DIR__ . '/../master.php';
        echo "D: (si ves esto) master.php terminó\n";
    }
    $out = ob_get_clean();
    header('Content-Type: text/plain; charset=UTF-8');
    echo $out;
    exit;
}

header('Content-Type: text/plain; charset=UTF-8');
echo "Uso: ?step=static | includes | premaster | master_head | master_full\n";
