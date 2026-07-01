#!/usr/bin/env php
<?php

/**
 * KOI Functional Parity Auditor (read-only)
 * - Baseline MySQL: koi1_stage
 * - Target MySQL: encinitas_test
 * - Formal SQL Server sources: sqlsrv_encinitas / sqlsrv_spiral (metadata only, no writes)
 */

date_default_timezone_set(getenv('TZ') ?: 'America/Buenos_Aires');

main($argv);

function main(array $argv)
{
    $options = parseArgs($argv);
    if (!empty($options['help'])) {
        printUsage();
        exit(0);
    }

    $root = dirname(__DIR__);
    $manifestPath = isset($options['manifest'])
        ? resolvePath($root, $options['manifest'])
        : $root . '/resources/migration-manifests/encinitas_funcional_dependencias.tsv';

    if (!is_file($manifestPath)) {
        fwrite(STDERR, "Manifest not found: {$manifestPath}\n");
        exit(2);
    }

    $requestedFlows = isset($options['flows'])
        ? array_filter(array_map('trim', explode(',', $options['flows'])))
        : array('ET01', 'ET05');

    $outDir = isset($options['out-dir'])
        ? resolvePath($root, $options['out-dir'])
        : $root . '/storage/app/parity-runs';

    if (!is_dir($outDir) && !mkdir($outDir, 0777, true)) {
        fwrite(STDERR, "Cannot create output directory: {$outDir}\n");
        exit(2);
    }

    $baseline = connectMysqlRole('KOI_BASELINE_MYSQL_', 'baseline');
    $target = connectMysqlRole('KOI_TARGET_MYSQL_', 'target');

    $manifestRows = loadManifestTsv($manifestPath, $requestedFlows);
    if (count($manifestRows) === 0) {
        fwrite(STDERR, "No manifest rows for flows: " . implode(',', $requestedFlows) . "\n");
        exit(2);
    }

    $baselineMeta = loadObjectMetadata($baseline['pdo']);
    $targetMeta = loadObjectMetadata($target['pdo']);

    $requiredObjects = array_values(array_unique(array_map(function ($r) {
        return $r['logical_object'];
    }, $manifestRows)));

    $phpUsageMap = scanPhpUsage($root, $requiredObjects);
    $mapperJoins = scanMapperJoins($root . '/factory/Mapper.php');

    $results = array();
    $resolvedBaseline = array();
    $resolvedTarget = array();

    foreach ($manifestRows as $row) {
        $logical = $row['logical_object'];
        $expectedType = normalizeExpectedType($row['expected_type']);

        $baseRes = resolveObject($baselineMeta, $logical, $expectedType);
        $tgtRes = resolveObject($targetMeta, $logical, $expectedType);
        $resolvedBaseline[$logical] = $baseRes;
        $resolvedTarget[$logical] = $tgtRes;

        $baseCount = null;
        $targetCount = null;

        if ($baseRes['exists'] && ($baseRes['resolved_kind'] === 'BASE TABLE' || $baseRes['resolved_kind'] === 'VIEW')) {
            $baseCount = countRows($baseline['pdo'], $baseRes['resolved_name']);
        }
        if ($tgtRes['exists'] && ($tgtRes['resolved_kind'] === 'BASE TABLE' || $tgtRes['resolved_kind'] === 'VIEW')) {
            $targetCount = countRows($target['pdo'], $tgtRes['resolved_name']);
        }

        $severity = 'OK';
        $status = 'paridad_objeto_ok';

        if (!$tgtRes['exists']) {
            $severity = 'BLOCKER';
            $status = 'objeto_faltante_target';
        } elseif (!$baseRes['exists']) {
            $severity = 'WARNING';
            $status = 'objeto_no_encontrado_en_baseline';
        } elseif ($tgtRes['type_status'] === 'TYPE_MISMATCH') {
            $severity = 'ERROR';
            $status = 'tipo_incompatible';
        } elseif (is_int($baseCount) && is_int($targetCount) && $baseCount > 0 && $targetCount === 0) {
            $severity = ($logical === 'lineas_productos') ? 'BLOCKER' : 'ERROR';
            $status = 'filas_faltantes_en_target';
        } elseif (is_int($baseCount) && is_int($targetCount) && $targetCount < $baseCount) {
            $severity = 'WARNING';
            $status = 'diferencia_de_conteo';
        }

        $requiredBy = usageEvidenceForObject($phpUsageMap, $logical);

        $results[] = array(
            'kind' => 'dependency',
            'flow_id' => $row['flow_id'],
            'flow_name' => $row['flow_name'],
            'dependency_id' => $row['dependency_id'],
            'logical_object' => $logical,
            'expected_type' => $row['expected_type'],
            'baseline_object' => $baseRes,
            'target_object' => $tgtRes,
            'baseline_count' => $baseCount,
            'target_count' => $targetCount,
            'missing_rows' => (is_int($baseCount) && is_int($targetCount) && $baseCount > $targetCount) ? ($baseCount - $targetCount) : 0,
            'orphan_count' => null,
            'required_by_files' => $requiredBy,
            'dependency_sql_declared' => $row['relation_rule'],
            'source_sqlserver' => $row['source_sqlserver'],
            'severity' => $severity,
            'status' => $status,
        );
    }

    $relBaseline = runRelationalChecksET01ET05($baseline['pdo'], $resolvedBaseline, 'baseline');
    $relTarget = runRelationalChecksET01ET05($target['pdo'], $resolvedTarget, 'target');

    $results = array_merge($results, compareRelationalChecks($relBaseline, $relTarget));

    $categoryCompare = compareEt05CategorySets($baseline['pdo'], $target['pdo'], $resolvedBaseline, $resolvedTarget);
    $results[] = $categoryCompare;

    $summary = summarizeResults($results);
    $suggestedLoads = buildSuggestedLoads($results);

    $runId = date('Ymd_His') . '_' . substr(sha1(uniqid('', true)), 0, 8);
    $jsonPath = $outDir . '/parity_' . $runId . '.json';
    $tsvPath = $outDir . '/parity_' . $runId . '.tsv';

    $report = array(
        'run_id' => $runId,
        'timestamp' => date('c'),
        'flows' => $requestedFlows,
        'sources' => array(
            'baseline_mysql' => maskDsn($baseline['dsn']),
            'target_mysql' => maskDsn($target['dsn']),
            'sqlsrv_encinitas' => envValue('KOI_SQLSRV_ENCINITAS_DSN', ''),
            'sqlsrv_spiral' => envValue('KOI_SQLSRV_SPIRAL_DSN', ''),
        ),
        'manifest' => str_replace('\\', '/', $manifestPath),
        'summary' => $summary,
        'executive_blockers' => array_values(array_filter($results, function ($r) {
            return in_array($r['severity'], array('BLOCKER', 'ERROR'), true);
        })),
        'mapper_joins_detected' => $mapperJoins,
        'results' => $results,
        'suggested_loads' => $suggestedLoads,
    );

    file_put_contents($jsonPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    writeTsvReport($tsvPath, $results);

    file_put_contents($outDir . '/latest.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    writeTsvReport($outDir . '/latest.tsv', $results);

    echo "Run ID: {$runId}\n";
    echo "JSON: {$jsonPath}\n";
    echo "TSV : {$tsvPath}\n";
    echo "Summary => BLOCKER: {$summary['BLOCKER']}, ERROR: {$summary['ERROR']}, WARNING: {$summary['WARNING']}, OK: {$summary['OK']}\n";

    $exitCode = ($summary['BLOCKER'] > 0 || $summary['ERROR'] > 0) ? 1 : 0;
    exit($exitCode);
}

function parseArgs(array $argv)
{
    $out = array();
    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            $out['help'] = true;
            continue;
        }
        if (strpos($arg, '--') !== 0) {
            continue;
        }
        $pair = explode('=', substr($arg, 2), 2);
        $out[$pair[0]] = isset($pair[1]) ? $pair[1] : true;
    }
    return $out;
}

function printUsage()
{
    echo "Usage:\n";
    echo "  php scripts/koi-functional-parity.php [--flows=ET01,ET05] [--manifest=resources/migration-manifests/encinitas_funcional_dependencias.tsv] [--out-dir=storage/app/parity-runs]\n\n";
    echo "Required env (MySQL baseline):\n";
    echo "  KOI_BASELINE_MYSQL_DSN or KOI_BASELINE_MYSQL_HOST/PORT/DB/USER/PASS\n";
    echo "Required env (MySQL target):\n";
    echo "  KOI_TARGET_MYSQL_DSN or KOI_TARGET_MYSQL_HOST/PORT/DB/USER/PASS\n\n";
    echo "Optional formal source metadata:\n";
    echo "  KOI_SQLSRV_ENCINITAS_DSN\n";
    echo "  KOI_SQLSRV_SPIRAL_DSN\n";
}

function resolvePath($root, $path)
{
    if (preg_match('/^[A-Za-z]:\\\\/', $path) || strpos($path, '/') === 0) {
        return $path;
    }
    return rtrim($root, '/\\') . '/' . ltrim($path, '/\\');
}

function envValue($key, $default = null)
{
    $v = getenv($key);
    return ($v === false || $v === '') ? $default : $v;
}

function connectMysqlRole($prefix, $label)
{
    $dsn = envValue($prefix . 'DSN', '');
    $user = envValue($prefix . 'USER', '');
    $pass = envValue($prefix . 'PASS', '');

    if ($dsn === '') {
        $host = envValue($prefix . 'HOST', '127.0.0.1');
        $port = envValue($prefix . 'PORT', '3306');
        $db = envValue($prefix . 'DB', '');
        $charset = envValue($prefix . 'CHARSET', 'utf8mb4');
        if ($db === '') {
            fwrite(STDERR, "Missing env {$prefix}DB or {$prefix}DSN for {$label}\n");
            exit(2);
        }
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
    }

    try {
        $pdo = new PDO($dsn, $user, $pass, array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ));
    } catch (Exception $e) {
        fwrite(STDERR, "Connection failed for {$label}: {$e->getMessage()}\n");
        exit(2);
    }

    return array('pdo' => $pdo, 'dsn' => $dsn, 'label' => $label);
}

function loadManifestTsv($path, array $flows)
{
    $rows = array();
    $fh = fopen($path, 'r');
    if (!$fh) {
        return $rows;
    }
    $header = fgetcsv($fh, 0, "\t");
    if (!$header) {
        fclose($fh);
        return $rows;
    }

    while (($data = fgetcsv($fh, 0, "\t")) !== false) {
        if (count($data) === 0) {
            continue;
        }
        $row = array();
        foreach ($header as $i => $col) {
            $row[$col] = isset($data[$i]) ? trim($data[$i]) : '';
        }
        if (!in_array($row['flow_id'], $flows, true)) {
            continue;
        }
        $rows[] = $row;
    }
    fclose($fh);
    return $rows;
}

function loadObjectMetadata(PDO $pdo)
{
    $objects = array();

    $stmt = $pdo->query("SELECT table_name AS object_name, table_type AS object_type FROM information_schema.tables WHERE table_schema = DATABASE()");
    foreach ($stmt as $r) {
        $objects[] = array(
            'name' => $r['object_name'],
            'kind' => strtoupper($r['object_type']),
        );
    }

    $stmt = $pdo->query("SELECT routine_name AS object_name, routine_type AS object_type FROM information_schema.routines WHERE routine_schema = DATABASE()");
    foreach ($stmt as $r) {
        $objects[] = array(
            'name' => $r['object_name'],
            'kind' => strtoupper($r['object_type']),
        );
    }

    return $objects;
}

function normalizeExpectedType($t)
{
    $x = strtoupper(trim($t));
    if ($x === 'TABLE') return 'BASE TABLE';
    if ($x === 'VIEW') return 'VIEW';
    if ($x === 'PROCEDURE') return 'PROCEDURE';
    if ($x === 'FUNCTION') return 'FUNCTION';
    if ($x === 'BASE TABLE') return 'BASE TABLE';
    return $x;
}

function resolveObject(array $meta, $logical, $expectedType)
{
    $exact = array();
    $insensitive = array();

    foreach ($meta as $o) {
        if ($o['name'] === $logical) {
            $exact[] = $o;
        }
        if (strcasecmp($o['name'], $logical) === 0) {
            $insensitive[] = $o;
        }
    }

    $candidates = count($exact) > 0 ? $exact : $insensitive;
    if (count($candidates) === 0) {
        return array(
            'exists' => false,
            'resolved_name' => null,
            'resolved_kind' => null,
            'type_status' => 'MISSING',
        );
    }

    $best = chooseBestCandidate($candidates, $expectedType);

    $typeStatus = 'TYPE_OK';
    if ($expectedType === 'BASE TABLE' || $expectedType === 'VIEW') {
        if ($best['kind'] !== $expectedType) {
            if ($best['kind'] === 'BASE TABLE' || $best['kind'] === 'VIEW') {
                $typeStatus = 'TYPE_COMPAT_TABLE_VIEW';
            } else {
                $typeStatus = 'TYPE_MISMATCH';
            }
        }
    } elseif ($expectedType !== '' && $best['kind'] !== $expectedType) {
        $typeStatus = 'TYPE_MISMATCH';
    }

    return array(
        'exists' => true,
        'resolved_name' => $best['name'],
        'resolved_kind' => $best['kind'],
        'type_status' => $typeStatus,
    );
}

function chooseBestCandidate(array $candidates, $expectedType)
{
    foreach ($candidates as $c) {
        if ($expectedType !== '' && $c['kind'] === $expectedType) {
            return $c;
        }
    }
    return $candidates[0];
}

function countRows(PDO $pdo, $tableOrView)
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $tableOrView)) {
        return null;
    }
    $sql = 'SELECT COUNT(*) AS c FROM `' . str_replace('`', '``', $tableOrView) . '`';
    try {
        $row = $pdo->query($sql)->fetch();
        return isset($row['c']) ? (int) $row['c'] : null;
    } catch (Exception $e) {
        return null;
    }
}

function scanPhpUsage($root, array $objects)
{
    $files = array(
        'content/cliente/menu.php',
        'content/cliente/catalogo/index.php',
        'factory/Factory.php',
        'factory/Mapper.php',
        'clases/Catalogo.php',
        'clases/CatalogoSeccion.php',
        'clases/CatalogoSeccionFamilia.php',
        'clases/CatalogoSeccionFamiliaArticulo.php',
        'clases/FamiliaProducto.php',
        'clases/LineaProducto.php',
        'clases/Articulo.php',
        'clases/ColorPorArticulo.php',
        'clases/TipoProductoStock.php',
        'clases/Usuario.php',
        'clases/Rol.php',
        'clases/RolPorUsuario.php',
        'clases/FuncionalidadPorRol.php',
        'clases/Cliente.php',
        'clases/Contacto.php',
        'clases/Sucursal.php',
        'clases/Operador.php',
        'clases/Personal.php',
    );

    $out = array();
    foreach ($objects as $obj) {
        $out[$obj] = array();
    }

    foreach ($files as $rf) {
        $fp = $root . '/' . $rf;
        if (!is_file($fp)) {
            continue;
        }
        $lines = file($fp, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            continue;
        }
        foreach ($lines as $idx => $line) {
            foreach ($objects as $obj) {
                if (stripos($line, $obj) !== false) {
                    $out[$obj][] = array('file' => $rf, 'line' => $idx + 1);
                }
            }
        }
    }

    return $out;
}

function usageEvidenceForObject(array $usageMap, $object)
{
    $items = isset($usageMap[$object]) ? $usageMap[$object] : array();
    $unique = array();
    foreach ($items as $it) {
        $k = $it['file'] . ':' . $it['line'];
        $unique[$k] = $k;
    }
    $vals = array_values($unique);
    sort($vals);
    return array_slice($vals, 0, 12);
}

function scanMapperJoins($mapperPath)
{
    $joins = array();
    if (!is_file($mapperPath)) {
        return $joins;
    }
    $lines = file($mapperPath, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $i => $line) {
        if (stripos($line, 'JOIN ') !== false) {
            $joins[] = array('file' => 'factory/Mapper.php', 'line' => $i + 1, 'sql' => trim($line));
        }
    }
    return $joins;
}

function runRelationalChecksET01ET05(PDO $pdo, array $resolvedMap, $dbLabel)
{
    $checks = array();

    $names = array();
    foreach ($resolvedMap as $logical => $res) {
        if (!empty($res['exists']) && !empty($res['resolved_name'])) {
            $names[strtolower($logical)] = $res['resolved_name'];
        }
    }

    // ET05 explicit checks
    $checks[] = runFkCheck(
        $pdo,
        $dbLabel,
        'ET05',
        'ET05_LINEA_PRODUCTO_RESUELVE',
        $names,
        array('catalogo_secciones', 'lineas_productos'),
        function ($n) {
            return "SELECT COUNT(*) AS c\n"
                . "FROM `{$n['catalogo_secciones']}` cs\n"
                . "LEFT JOIN `{$n['lineas_productos']}` lp ON lp.cod_linea_nro = cs.cod_linea_producto\n"
                . "WHERE lp.cod_linea_nro IS NULL";
        },
        'Cada catalogo_secciones.cod_linea_producto debe resolver en lineas_productos.cod_linea_nro'
    );

    $checks[] = runFkCheck(
        $pdo,
        $dbLabel,
        'ET05',
        'ET05_FAMILIA_RESUELVE',
        $names,
        array('catalogo_seccion_familias', 'familias_producto'),
        function ($n) {
            return "SELECT COUNT(*) AS c\n"
                . "FROM `{$n['catalogo_seccion_familias']}` csf\n"
                . "LEFT JOIN `{$n['familias_producto']}` fp ON fp.id = csf.cod_familia_producto\n"
                . "WHERE fp.id IS NULL";
        },
        'Cada familia referenciada debe existir en familias_producto'
    );

    $checks[] = runFkCheck(
        $pdo,
        $dbLabel,
        'ET05',
        'ET05_ARTICULO_COLOR_RESUELVE',
        $names,
        array('catalogo_seccion_familia_articulos', 'articulos', 'colores_por_articulo'),
        function ($n) {
            return "SELECT COUNT(*) AS c\n"
                . "FROM `{$n['catalogo_seccion_familia_articulos']}` cfa\n"
                . "LEFT JOIN `{$n['articulos']}` a ON a.cod_articulo = cfa.cod_articulo\n"
                . "LEFT JOIN `{$n['colores_por_articulo']}` c ON c.cod_articulo = cfa.cod_articulo AND c.cod_color_articulo = cfa.cod_color_articulo\n"
                . "WHERE a.cod_articulo IS NULL OR c.cod_articulo IS NULL";
        },
        'Cada articulo/color del catalogo debe existir y ser enlazable'
    );

    $checks[] = runFkCheck(
        $pdo,
        $dbLabel,
        'ET05',
        'ET05_TITULO_CATALOGO_NO_VACIO',
        $names,
        array('catalogo_secciones', 'lineas_productos'),
        function ($n) {
            return "SELECT COUNT(*) AS c\n"
                . "FROM `{$n['catalogo_secciones']}` cs\n"
                . "LEFT JOIN `{$n['lineas_productos']}` lp ON lp.cod_linea_nro = cs.cod_linea_producto\n"
                . "WHERE lp.cod_linea_nro IS NOT NULL AND (lp.titulo_catalogo IS NULL OR TRIM(lp.titulo_catalogo) = '')";
        },
        'Cada seccion debe renderizar titulo_catalogo no vacio'
    );

    // ET01 explicit checks
    $checks[] = runFkCheck(
        $pdo,
        $dbLabel,
        'ET01',
        'ET01_USERS_ROLES_ENLACE',
        $names,
        array('users', 'roles_por_usuario', 'roles'),
        function ($n) {
            return "SELECT COUNT(*) AS c\n"
                . "FROM `{$n['roles_por_usuario']}` rpu\n"
                . "LEFT JOIN `{$n['users']}` u ON u.cod_usuario = rpu.cod_usuario\n"
                . "LEFT JOIN `{$n['roles']}` r ON r.cod_rol = rpu.cod_rol\n"
                . "WHERE u.cod_usuario IS NULL OR r.cod_rol IS NULL";
        },
        'roles_por_usuario debe resolver usuarios y roles'
    );

    $checks[] = runFkCheck(
        $pdo,
        $dbLabel,
        'ET01',
        'ET01_FUNCIONALIDADES_ROL_ENLACE',
        $names,
        array('funcionalidades_por_rol', 'roles'),
        function ($n) {
            return "SELECT COUNT(*) AS c\n"
                . "FROM `{$n['funcionalidades_por_rol']}` fpr\n"
                . "LEFT JOIN `{$n['roles']}` r ON r.cod_rol = fpr.cod_rol\n"
                . "WHERE r.cod_rol IS NULL";
        },
        'funcionalidades_por_rol debe resolver roles'
    );

    $checks[] = runFkCheck(
        $pdo,
        $dbLabel,
        'ET01',
        'ET01_USERS_CONTACTOS_ENLACE',
        $names,
        array('users', 'contactos'),
        function ($n) {
            return "SELECT COUNT(*) AS c\n"
                . "FROM `{$n['users']}` u\n"
                . "LEFT JOIN `{$n['contactos']}` c ON c.cod_contacto = u.cod_contacto\n"
                . "WHERE u.cod_contacto IS NOT NULL AND u.cod_contacto <> 0 AND c.cod_contacto IS NULL";
        },
        'users.cod_contacto debe resolver contactos'
    );

    return $checks;
}

function runFkCheck(PDO $pdo, $dbLabel, $flow, $checkId, array $resolvedNames, array $requiredLogicals, callable $queryBuilder, $description)
{
    $actual = array();
    foreach ($requiredLogicals as $logical) {
        $k = strtolower($logical);
        if (!isset($resolvedNames[$k]) || $resolvedNames[$k] === '') {
            return array(
                'kind' => 'relational',
                'flow_id' => $flow,
                'dependency_id' => $checkId,
                'logical_object' => $description,
                'db' => $dbLabel,
                'orphan_count' => null,
                'severity' => 'BLOCKER',
                'status' => 'dependencia_fisica_faltante_para_check',
                'details' => 'Missing required objects: ' . implode(', ', $requiredLogicals),
            );
        }
        $actual[$logical] = $resolvedNames[$k];
    }

    try {
        $sql = $queryBuilder($actual);
        $row = $pdo->query($sql)->fetch();
        $orphans = isset($row['c']) ? (int) $row['c'] : 0;
        $severity = $orphans > 0 ? 'ERROR' : 'OK';
        $status = $orphans > 0 ? 'claves_huerfanas_detectadas' : 'integridad_ok';
        return array(
            'kind' => 'relational',
            'flow_id' => $flow,
            'dependency_id' => $checkId,
            'logical_object' => $description,
            'db' => $dbLabel,
            'orphan_count' => $orphans,
            'severity' => $severity,
            'status' => $status,
            'details' => $sql,
        );
    } catch (Exception $e) {
        return array(
            'kind' => 'relational',
            'flow_id' => $flow,
            'dependency_id' => $checkId,
            'logical_object' => $description,
            'db' => $dbLabel,
            'orphan_count' => null,
            'severity' => 'ERROR',
            'status' => 'error_en_check_relacional',
            'details' => $e->getMessage(),
        );
    }
}

function compareRelationalChecks(array $baselineChecks, array $targetChecks)
{
    $out = array();
    $indexedBaseline = array();
    foreach ($baselineChecks as $b) {
        $indexedBaseline[$b['dependency_id']] = $b;
    }

    foreach ($targetChecks as $t) {
        $b = isset($indexedBaseline[$t['dependency_id']]) ? $indexedBaseline[$t['dependency_id']] : null;

        $severity = $t['severity'];
        $status = $t['status'];
        $missingRows = 0;

        if ($b && is_int($b['orphan_count']) && is_int($t['orphan_count'])) {
            if ($b['orphan_count'] === 0 && $t['orphan_count'] > 0) {
                $severity = 'ERROR';
                $status = 'presente_no_enlazable_en_target';
                $missingRows = $t['orphan_count'];
            }
        }

        $out[] = array(
            'kind' => 'relational_compare',
            'flow_id' => $t['flow_id'],
            'flow_name' => $t['flow_id'] === 'ET01' ? 'Sesion y ABM Clientes' : 'Catalogo Cliente',
            'dependency_id' => $t['dependency_id'],
            'logical_object' => $t['logical_object'],
            'expected_type' => 'RELATION',
            'baseline_object' => null,
            'target_object' => null,
            'baseline_count' => $b ? $b['orphan_count'] : null,
            'target_count' => $t['orphan_count'],
            'missing_rows' => $missingRows,
            'orphan_count' => $t['orphan_count'],
            'required_by_files' => array(),
            'dependency_sql_declared' => $t['details'],
            'source_sqlserver' => ($t['flow_id'] === 'ET05') ? 'spiral' : 'encinitas',
            'severity' => $severity,
            'status' => $status,
        );
    }

    return $out;
}

function compareEt05CategorySets(PDO $baselinePdo, PDO $targetPdo, array $resolvedBaseline, array $resolvedTarget)
{
    $q1 = buildCategorySetQuery($resolvedBaseline);
    $q2 = buildCategorySetQuery($resolvedTarget);

    if (!$q1 || !$q2) {
        return array(
            'kind' => 'category_compare',
            'flow_id' => 'ET05',
            'flow_name' => 'Catalogo Cliente',
            'dependency_id' => 'ET05_CATEGORIAS_RESULTANTES',
            'logical_object' => 'Conjunto funcional de categorias/menu',
            'expected_type' => 'RESULT_SET',
            'baseline_object' => null,
            'target_object' => null,
            'baseline_count' => null,
            'target_count' => null,
            'missing_rows' => null,
            'orphan_count' => null,
            'required_by_files' => array('content/cliente/menu.php', 'content/cliente/catalogo/index.php'),
            'dependency_sql_declared' => 'Comparacion de conjunto de categorias renderizables',
            'source_sqlserver' => 'spiral',
            'severity' => 'BLOCKER',
            'status' => 'no_se_pudo_construir_consulta_de_categorias',
        );
    }

    try {
        $baseRows = $baselinePdo->query($q1)->fetchAll();
        $targetRows = $targetPdo->query($q2)->fetchAll();

        $setA = array();
        foreach ($baseRows as $r) {
            $setA[$r['k']] = true;
        }
        $setB = array();
        foreach ($targetRows as $r) {
            $setB[$r['k']] = true;
        }

        $missingInTarget = array_diff_key($setA, $setB);
        $extraInTarget = array_diff_key($setB, $setA);

        $severity = 'OK';
        $status = 'categorias_equivalentes';
        if (count($missingInTarget) > 0) {
            $severity = 'ERROR';
            $status = 'categorias_faltantes_o_no_renderizables_en_target';
        } elseif (count($extraInTarget) > 0) {
            $severity = 'WARNING';
            $status = 'categorias_extra_en_target';
        }

        return array(
            'kind' => 'category_compare',
            'flow_id' => 'ET05',
            'flow_name' => 'Catalogo Cliente',
            'dependency_id' => 'ET05_CATEGORIAS_RESULTANTES',
            'logical_object' => 'Conjunto funcional de categorias/menu',
            'expected_type' => 'RESULT_SET',
            'baseline_object' => null,
            'target_object' => null,
            'baseline_count' => count($setA),
            'target_count' => count($setB),
            'missing_rows' => count($missingInTarget),
            'orphan_count' => 0,
            'required_by_files' => array('content/cliente/menu.php', 'content/cliente/catalogo/index.php'),
            'dependency_sql_declared' => 'SELECT de categorias con joins catalogo->linea->familia',
            'source_sqlserver' => 'spiral',
            'severity' => $severity,
            'status' => $status,
        );
    } catch (Exception $e) {
        return array(
            'kind' => 'category_compare',
            'flow_id' => 'ET05',
            'flow_name' => 'Catalogo Cliente',
            'dependency_id' => 'ET05_CATEGORIAS_RESULTANTES',
            'logical_object' => 'Conjunto funcional de categorias/menu',
            'expected_type' => 'RESULT_SET',
            'baseline_object' => null,
            'target_object' => null,
            'baseline_count' => null,
            'target_count' => null,
            'missing_rows' => null,
            'orphan_count' => null,
            'required_by_files' => array('content/cliente/menu.php', 'content/cliente/catalogo/index.php'),
            'dependency_sql_declared' => 'Comparacion de conjunto de categorias renderizables',
            'source_sqlserver' => 'spiral',
            'severity' => 'ERROR',
            'status' => 'error_en_comparacion_de_categorias: ' . $e->getMessage(),
        );
    }
}

function buildCategorySetQuery(array $resolved)
{
    $need = array('catalogos', 'catalogo_secciones', 'lineas_productos', 'catalogo_seccion_familias', 'familias_producto');
    $actual = array();
    foreach ($need as $logical) {
        if (empty($resolved[$logical]['exists']) || empty($resolved[$logical]['resolved_name'])) {
            return null;
        }
        $actual[$logical] = $resolved[$logical]['resolved_name'];
    }

    return "SELECT DISTINCT CONCAT(cs.cod_linea_producto, '|', COALESCE(lp.titulo_catalogo,''), '|', COALESCE(csf.cod_familia_producto,''), '|', COALESCE(fp.nombre,'')) AS k\n"
        . "FROM `{$actual['catalogos']}` c\n"
        . "JOIN `{$actual['catalogo_secciones']}` cs ON cs.cod_catalogo = c.id\n"
        . "LEFT JOIN `{$actual['lineas_productos']}` lp ON lp.cod_linea_nro = cs.cod_linea_producto\n"
        . "LEFT JOIN `{$actual['catalogo_seccion_familias']}` csf ON csf.cod_catalogo = cs.cod_catalogo AND csf.cod_linea_producto = cs.cod_linea_producto\n"
        . "LEFT JOIN `{$actual['familias_producto']}` fp ON fp.id = csf.cod_familia_producto\n"
        . "WHERE c.id = (SELECT MAX(id) FROM `{$actual['catalogos']}` WHERE anulado = 'N')";
}

function summarizeResults(array $results)
{
    $sum = array('BLOCKER' => 0, 'ERROR' => 0, 'WARNING' => 0, 'OK' => 0);
    foreach ($results as $r) {
        $sev = isset($r['severity']) ? $r['severity'] : 'OK';
        if (!isset($sum[$sev])) {
            $sum[$sev] = 0;
        }
        $sum[$sev]++;
    }
    return $sum;
}

function buildSuggestedLoads(array $results)
{
    $loads = array();
    foreach ($results as $r) {
        if (!in_array($r['severity'], array('BLOCKER', 'ERROR'), true)) {
            continue;
        }
        if (empty($r['logical_object']) || strpos($r['logical_object'], 'Conjunto funcional') === 0) {
            continue;
        }
        if (strpos($r['logical_object'], 'REVISAR_MANUALMENTE') === 0) {
            continue;
        }

        $object = preg_replace('/[^A-Za-z0-9_]/', '', $r['logical_object']);
        if ($object === '') {
            continue;
        }

        $source = isset($r['source_sqlserver']) ? $r['source_sqlserver'] : 'encinitas';
        $sourceConn = ($source === 'spiral') ? 'sqlsrv_spiral' : 'sqlsrv_encinitas';

        $k = $sourceConn . '|' . strtolower($object);
        $loads[$k] = array(
            'source' => $sourceConn,
            'object' => $object,
            'reason' => $r['status'],
            'suggested_command' => "php artisan koi:import-legacy-object --source={$sourceConn} --object={$object} --target=mysql_encinitas_test --dry-run",
        );
    }

    return array_values($loads);
}

function writeTsvReport($path, array $results)
{
    $fh = fopen($path, 'w');
    if (!$fh) {
        return;
    }

    $header = array(
        'severity','kind','flow_id','dependency_id','logical_object','expected_type',
        'baseline_exists','baseline_type','baseline_count',
        'target_exists','target_type','target_count',
        'missing_rows','orphan_count','status','source_sqlserver','required_by_files','dependency_sql_declared'
    );
    fputcsv($fh, $header, "\t");

    foreach ($results as $r) {
        $row = array(
            val($r,'severity'),
            val($r,'kind'),
            val($r,'flow_id'),
            val($r,'dependency_id'),
            val($r,'logical_object'),
            val($r,'expected_type'),
            boolStr(path($r,'baseline_object.exists')),
            path($r,'baseline_object.resolved_kind'),
            val($r,'baseline_count'),
            boolStr(path($r,'target_object.exists')),
            path($r,'target_object.resolved_kind'),
            val($r,'target_count'),
            val($r,'missing_rows'),
            val($r,'orphan_count'),
            val($r,'status'),
            val($r,'source_sqlserver'),
            is_array(val($r,'required_by_files')) ? implode(';', val($r,'required_by_files')) : '',
            val($r,'dependency_sql_declared'),
        );
        fputcsv($fh, $row, "\t");
    }

    fclose($fh);
}

function val($arr, $key, $default = '')
{
    return isset($arr[$key]) ? $arr[$key] : $default;
}

function path($arr, $path, $default = '')
{
    $parts = explode('.', $path);
    $cur = $arr;
    foreach ($parts as $p) {
        if (!is_array($cur) || !array_key_exists($p, $cur)) {
            return $default;
        }
        $cur = $cur[$p];
    }
    return $cur;
}

function boolStr($v)
{
    if ($v === true) return '1';
    if ($v === false) return '0';
    return '';
}

function maskDsn($dsn)
{
    return preg_replace('/password=[^;]+/i', 'password=***', (string)$dsn);
}
