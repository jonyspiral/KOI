#!/usr/bin/env php
<?php
declare(strict_types=1);

const DEFAULT_FLOW = 'abm_clientes';
const DEFAULT_CLIENT_ID = 204;
const DEFAULT_EXPECTED_VENDEDOR = 'V00358';
const DEFAULT_EXPECTED_PERSONAL = '358';

main($argv);

function main(array $argv): void
{
    $options = parseArgs($argv);
    if (!empty($options['help'])) {
        printUsage();
        return;
    }

    $flowId = (string) ($options['flow'] ?? DEFAULT_FLOW);
    $flows = getFlows();
    if (!isset($flows[$flowId])) {
        fwrite(STDERR, "Unknown flow: {$flowId}\n");
        exit(2);
    }

    $stageEngine = requireValue($options, 'stage-engine');
    $stageDsn = requireValue($options, 'stage-dsn');
    $stageUser = (string) ($options['stage-user'] ?? getenv('KOI_STAGE_USER') ?: '');
    $stagePass = (string) ($options['stage-pass'] ?? getenv('KOI_STAGE_PASS') ?: '');

    $referenceEngine = requireValue($options, 'reference-engine');
    $referenceDsn = requireValue($options, 'reference-dsn');
    $referenceUser = (string) ($options['reference-user'] ?? getenv('KOI_REFERENCE_USER') ?: '');
    $referencePass = (string) ($options['reference-pass'] ?? getenv('KOI_REFERENCE_PASS') ?: '');

    $clientId = (string) ($options['client-id'] ?? DEFAULT_CLIENT_ID);
    $expectedVendedor = (string) ($options['expected-vendedor'] ?? DEFAULT_EXPECTED_VENDEDOR);
    $expectedPersonal = (string) ($options['expected-personal'] ?? DEFAULT_EXPECTED_PERSONAL);
    $format = strtolower((string) ($options['format'] ?? 'human'));

    $stage = connectDb($stageEngine, $stageDsn, $stageUser, $stagePass);
    $reference = connectDb($referenceEngine, $referenceDsn, $referenceUser, $referencePass);

    $context = array(
        'client_id' => $clientId,
        'expected_vendedor' => $expectedVendedor,
        'expected_personal' => $expectedPersonal,
    );

    $result = runAudit(
        $flows[$flowId],
        $stage,
        $reference,
        $stageEngine,
        $referenceEngine,
        $context
    );

    if ($format === 'human' || $format === 'all') {
        echo renderHuman($result);
    }
    if ($format === 'json' || $format === 'all') {
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    }
    if ($format === 'csv') {
        echo renderCsv($result);
    }

    if (!empty($options['json-out'])) {
        file_put_contents((string) $options['json-out'], json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }
    if (!empty($options['csv-out'])) {
        file_put_contents((string) $options['csv-out'], renderCsv($result));
    }

    exit($result['summary']['errors'] > 0 ? 1 : 0);
}

function parseArgs(array $argv): array
{
    $options = array();
    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            $options['help'] = true;
            continue;
        }
        if (strpos($arg, '--') !== 0) {
            continue;
        }
        $pair = explode('=', substr($arg, 2), 2);
        $options[$pair[0]] = $pair[1] ?? true;
    }
    return $options;
}

function printUsage(): void
{
    echo <<<TXT
Usage:
  php scripts/koi-parity-audit.php --flow=abm_clientes \\
    --stage-engine=mysql --stage-dsn='mysql:host=127.0.0.1;dbname=koi1_stage;charset=utf8mb4' \\
    --reference-engine=odbc --reference-dsn='odbc:Driver=FreeTDS;Server=sqlserver;Port=1433;Database=encinitas_test;TDS_Version=8.0'

Required:
  --stage-engine=mysql|odbc|dblib|sqlsrv
  --stage-dsn=PDO_DSN
  --reference-engine=mysql|odbc|dblib|sqlsrv
  --reference-dsn=PDO_DSN

Optional:
  --stage-user=USER
  --stage-pass=PASS
  --reference-user=USER
  --reference-pass=PASS
  --flow=abm_clientes
  --client-id=204
  --expected-vendedor=V00358
  --expected-personal=358
  --format=human|json|csv|all
  --json-out=/tmp/audit.json
  --csv-out=/tmp/audit.csv

Environment fallbacks:
  KOI_STAGE_USER, KOI_STAGE_PASS, KOI_REFERENCE_USER, KOI_REFERENCE_PASS

TXT;
}

function requireValue(array $options, string $key): string
{
    if (!isset($options[$key]) || $options[$key] === '') {
        fwrite(STDERR, "Missing required option --{$key}\n");
        exit(2);
    }
    return (string) $options[$key];
}

function connectDb(string $engine, string $dsn, string $user, string $pass): PDO
{
    $pdo = new PDO($dsn, $user, $pass, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ));

    if ($engine === 'mysql') {
        $pdo->exec("SET NAMES utf8mb4");
    }

    return $pdo;
}

function runAudit(array $flow, PDO $stage, PDO $reference, string $stageEngine, string $referenceEngine, array $context): array
{
    $checks = array();

    foreach ($flow['objects'] as $object) {
        $stageExists = objectExists($stage, $stageEngine, $object['name'], $object['type']);
        $referenceExists = objectExists($reference, $referenceEngine, $object['name'], $object['type']);
        $checks[] = buildCheck(
            'object',
            $object['severity'],
            $object['name'],
            $object['type'],
            ($stageExists && $referenceExists) ? 'ok' : 'error',
            array('exists' => $stageExists),
            array('exists' => $referenceExists),
            (!$stageExists || !$referenceExists)
                ? 'Object missing on one side.'
                : 'Object exists on both sides.'
        );

        if ($object['type'] === 'view' && $stageExists && $referenceExists) {
            $stageDefinition = normalizeDefinition(getViewDefinition($stage, $stageEngine, $object['name']));
            $referenceDefinition = normalizeDefinition(getViewDefinition($reference, $referenceEngine, $object['name']));
            $checks[] = buildCheck(
                'view_definition',
                $object['severity'],
                $object['name'],
                'view',
                ($stageDefinition === $referenceDefinition) ? 'ok' : 'error',
                array('definition_hash' => sha1($stageDefinition)),
                array('definition_hash' => sha1($referenceDefinition)),
                ($stageDefinition === $referenceDefinition)
                    ? 'View definition matches after normalization.'
                    : 'View definition differs after normalization.'
            );
        }
    }

    foreach ($flow['counts'] as $countCheck) {
        $stageCount = safeCount($stage, $stageEngine, $countCheck['object']);
        $referenceCount = safeCount($reference, $referenceEngine, $countCheck['object']);
        $checks[] = buildCheck(
            'count',
            $countCheck['severity'],
            $countCheck['object'],
            $countCheck['type'],
            ((string) $stageCount === (string) $referenceCount) ? 'ok' : 'warning',
            array('count' => $stageCount),
            array('count' => $referenceCount),
            ((string) $stageCount === (string) $referenceCount)
                ? 'Row count matches.'
                : 'Row count differs.'
        );
    }

    foreach ($flow['keys'] as $keyCheck) {
        $stageRows = safeQuery($stage, $keyCheck['sql'], $context);
        $referenceRows = safeQuery($reference, $keyCheck['sql'], $context);
        $stageKeys = rowsToKeys($stageRows, $keyCheck['columns']);
        $referenceKeys = rowsToKeys($referenceRows, $keyCheck['columns']);
        $missingInStage = array_values(array_diff($referenceKeys, $stageKeys));
        $missingInReference = array_values(array_diff($stageKeys, $referenceKeys));
        $status = (count($missingInStage) === 0 && count($missingInReference) === 0) ? 'ok' : 'warning';
        $checks[] = buildCheck(
            'functional_key',
            $keyCheck['severity'],
            $keyCheck['object'],
            $keyCheck['type'],
            $status,
            array('keys' => count($stageKeys), 'missing' => array_slice($missingInStage, 0, 20)),
            array('keys' => count($referenceKeys), 'missing' => array_slice($missingInReference, 0, 20)),
            ($status === 'ok')
                ? 'Functional keys match.'
                : 'Functional keys differ.'
        );
    }

    foreach ($flow['joins'] as $joinCheck) {
        $stageRows = safeQuery($stage, $joinCheck['sql'], $context);
        $referenceRows = safeQuery($reference, $joinCheck['sql'], $context);
        $checks[] = buildCheck(
            'join_break',
            $joinCheck['severity'],
            $joinCheck['object'],
            $joinCheck['type'],
            (count($stageRows) === 0 && count($referenceRows) === 0) ? 'ok' : 'warning',
            array('rows' => count($stageRows), 'sample' => array_slice($stageRows, 0, 5)),
            array('rows' => count($referenceRows), 'sample' => array_slice($referenceRows, 0, 5)),
            (count($stageRows) === 0 && count($referenceRows) === 0)
                ? 'No broken joins detected.'
                : 'Broken joins or orphan rows detected.'
        );
    }

    foreach ($flow['cases'] as $caseCheck) {
        $stageRows = safeQuery($stage, $caseCheck['sql'], $context);
        $referenceRows = safeQuery($reference, $caseCheck['sql'], $context);
        $status = compareCaseRows($stageRows, $referenceRows, $caseCheck['required_pairs']) ? 'ok' : 'error';
        $checks[] = buildCheck(
            'case',
            $caseCheck['severity'],
            $caseCheck['object'],
            $caseCheck['type'],
            $status,
            array('rows' => $stageRows),
            array('rows' => $referenceRows),
            ($status === 'ok')
                ? 'Mandatory case matches.'
                : 'Mandatory case differs or is incomplete.'
        );
    }

    $summary = summarizeChecks($checks);

    return array(
        'generated_at' => gmdate('c'),
        'flow' => array(
            'id' => $flow['id'],
            'label' => $flow['label'],
            'client_id' => $context['client_id'],
            'expected_vendedor' => $context['expected_vendedor'],
            'expected_personal' => $context['expected_personal'],
        ),
        'summary' => $summary,
        'checks' => $checks,
    );
}

function getFlows(): array
{
    return array(
        'abm_clientes' => array(
            'id' => 'abm_clientes',
            'label' => 'Base de sesion y ABM Clientes',
            'objects' => array(
                array('name' => 'users', 'type' => 'table', 'severity' => 'critical'),
                array('name' => 'roles', 'type' => 'table', 'severity' => 'critical'),
                array('name' => 'roles_por_usuario', 'type' => 'table', 'severity' => 'critical'),
                array('name' => 'roles_por_usuario_v', 'type' => 'view', 'severity' => 'critical'),
                array('name' => 'funcionalidades_por_rol', 'type' => 'table', 'severity' => 'critical'),
                array('name' => 'personal', 'type' => 'table', 'severity' => 'critical'),
                array('name' => 'Operadores', 'type' => 'table', 'severity' => 'critical'),
                array('name' => 'operadores_v', 'type' => 'view', 'severity' => 'critical'),
                array('name' => 'Clientes', 'type' => 'table', 'severity' => 'critical'),
                array('name' => 'sucursales_clientes', 'type' => 'table', 'severity' => 'critical'),
                array('name' => 'sucursales_v', 'type' => 'view', 'severity' => 'critical'),
                array('name' => 'Contactos', 'type' => 'table', 'severity' => 'high'),
                array('name' => 'areas_empresa', 'type' => 'table', 'severity' => 'high'),
                array('name' => 'condiciones_iva', 'type' => 'table', 'severity' => 'high'),
                array('name' => 'Formas_pago', 'type' => 'table', 'severity' => 'high'),
                array('name' => 'grupo_empresa', 'type' => 'table', 'severity' => 'high'),
                array('name' => 'Grupos_clientes', 'type' => 'table', 'severity' => 'high'),
                array('name' => 'Paises', 'type' => 'table', 'severity' => 'high'),
                array('name' => 'Provincias', 'type' => 'table', 'severity' => 'high'),
                array('name' => 'localidades', 'type' => 'table', 'severity' => 'high'),
            ),
            'counts' => array(
                array('object' => 'users', 'type' => 'table', 'severity' => 'medium'),
                array('object' => 'roles', 'type' => 'table', 'severity' => 'medium'),
                array('object' => 'roles_por_usuario', 'type' => 'table', 'severity' => 'medium'),
                array('object' => 'personal', 'type' => 'table', 'severity' => 'medium'),
                array('object' => 'Operadores', 'type' => 'table', 'severity' => 'medium'),
                array('object' => 'Clientes', 'type' => 'table', 'severity' => 'medium'),
                array('object' => 'sucursales_clientes', 'type' => 'table', 'severity' => 'medium'),
                array('object' => 'Contactos', 'type' => 'table', 'severity' => 'medium'),
            ),
            'keys' => array(
                array('object' => 'users', 'type' => 'table', 'severity' => 'high', 'columns' => array('cod_usuario'), 'sql' => 'SELECT cod_usuario FROM users'),
                array('object' => 'roles', 'type' => 'table', 'severity' => 'high', 'columns' => array('cod_rol'), 'sql' => 'SELECT cod_rol FROM roles'),
                array('object' => 'roles_por_usuario', 'type' => 'table', 'severity' => 'critical', 'columns' => array('cod_usuario', 'cod_rol'), 'sql' => 'SELECT cod_usuario, cod_rol FROM roles_por_usuario'),
                array('object' => 'personal', 'type' => 'table', 'severity' => 'high', 'columns' => array('cod_personal'), 'sql' => 'SELECT cod_personal FROM personal'),
                array('object' => 'Operadores', 'type' => 'table', 'severity' => 'critical', 'columns' => array('cod_operador', 'cod_personal'), 'sql' => 'SELECT cod_operador, cod_personal FROM Operadores'),
                array('object' => 'Clientes', 'type' => 'table', 'severity' => 'critical', 'columns' => array('cod_cli', 'cod_vendedor'), 'sql' => 'SELECT cod_cli, cod_vendedor FROM Clientes'),
                array('object' => 'sucursales_clientes', 'type' => 'table', 'severity' => 'high', 'columns' => array('cod_cli', 'cod_suc'), 'sql' => 'SELECT cod_cli, cod_suc FROM sucursales_clientes'),
            ),
            'joins' => array(
                array(
                    'object' => 'Clientes/Operadores',
                    'type' => 'join',
                    'severity' => 'critical',
                    'sql' => "SELECT c.cod_cli, c.cod_vendedor FROM Clientes c LEFT JOIN Operadores o ON o.cod_operador = c.cod_vendedor WHERE c.anulado = 'N' AND c.cod_vendedor IS NOT NULL AND o.cod_operador IS NULL"
                ),
                array(
                    'object' => 'Operadores/personal',
                    'type' => 'join',
                    'severity' => 'critical',
                    'sql' => "SELECT o.cod_operador, o.cod_personal FROM Operadores o LEFT JOIN personal p ON p.cod_personal = o.cod_personal WHERE o.cod_personal IS NOT NULL AND p.cod_personal IS NULL"
                ),
                array(
                    'object' => 'users/roles_por_usuario',
                    'type' => 'join',
                    'severity' => 'high',
                    'sql' => "SELECT u.cod_usuario FROM users u LEFT JOIN roles_por_usuario rpu ON rpu.cod_usuario = u.cod_usuario WHERE u.anulado = 'N' AND rpu.cod_usuario IS NULL"
                ),
                array(
                    'object' => 'roles/funcionalidades_por_rol',
                    'type' => 'join',
                    'severity' => 'high',
                    'sql' => "SELECT r.cod_rol FROM roles r LEFT JOIN funcionalidades_por_rol fpr ON fpr.cod_rol = r.cod_rol WHERE r.anulado = 'N' AND fpr.cod_rol IS NULL"
                ),
            ),
            'cases' => array(
                array(
                    'object' => 'cliente_204',
                    'type' => 'chain',
                    'severity' => 'critical',
                    'required_pairs' => array(
                        array('cod_cli', 'cod_cli'),
                        array('cod_vendedor', 'cod_vendedor'),
                        array('operador_cod_personal', 'operador_cod_personal'),
                        array('personal_cod_personal', 'personal_cod_personal'),
                    ),
                    'sql' => "SELECT c.cod_cli, c.cod_vendedor, o.cod_operador, o.cod_personal AS operador_cod_personal, p.cod_personal AS personal_cod_personal FROM Clientes c LEFT JOIN Operadores o ON o.cod_operador = c.cod_vendedor LEFT JOIN personal p ON p.cod_personal = o.cod_personal WHERE c.cod_cli = {client_id}"
                ),
                array(
                    'object' => 'operadores_v_case',
                    'type' => 'chain',
                    'severity' => 'critical',
                    'required_pairs' => array(
                        array('cod_operador', 'cod_operador'),
                        array('cod_personal', 'cod_personal'),
                    ),
                    'sql' => "SELECT cod_operador, cod_personal, tipo_operador FROM operadores_v WHERE cod_operador = '{expected_vendedor}' OR cod_personal = {expected_personal}"
                ),
            ),
        ),
    );
}

function buildCheck(string $category, string $severity, string $object, string $type, string $status, array $stage, array $reference, string $detail): array
{
    return array(
        'category' => $category,
        'severity' => $severity,
        'object' => $object,
        'type' => $type,
        'status' => $status,
        'stage' => $stage,
        'reference' => $reference,
        'detail' => $detail,
    );
}

function summarizeChecks(array $checks): array
{
    $summary = array('total' => count($checks), 'ok' => 0, 'warnings' => 0, 'errors' => 0);
    foreach ($checks as $check) {
        if ($check['status'] === 'ok') {
            $summary['ok']++;
        } elseif ($check['status'] === 'warning') {
            $summary['warnings']++;
        } else {
            $summary['errors']++;
        }
    }
    return $summary;
}

function objectExists(PDO $pdo, string $engine, string $name, string $type): bool
{
    if ($engine === 'mysql') {
        if ($type === 'procedure') {
            $sql = 'SELECT 1 FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = ? AND ROUTINE_TYPE = ?';
            return (bool) fetchOne($pdo, $sql, array($name, 'PROCEDURE'));
        }
        $tableType = ($type === 'view') ? 'VIEW' : 'BASE TABLE';
        $sql = 'SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND TABLE_TYPE = ?';
        return (bool) fetchOne($pdo, $sql, array($name, $tableType));
    }

    $map = array('table' => 'U', 'view' => 'V', 'procedure' => 'P', 'function' => 'FN');
    $sql = 'SELECT 1 FROM sysobjects WHERE name = ? AND type = ?';
    return (bool) fetchOne($pdo, $sql, array($name, $map[$type] ?? 'U'));
}

function getViewDefinition(PDO $pdo, string $engine, string $name): string
{
    if ($engine === 'mysql') {
        $identifier = '`' . str_replace('`', '``', $name) . '`';
        $row = $pdo->query("SHOW CREATE VIEW {$identifier}")->fetch(PDO::FETCH_ASSOC);
        return (string) ($row['Create View'] ?? '');
    }

    $rows = fetchAll($pdo, 'SELECT c.text FROM sysobjects o JOIN syscomments c ON o.id = c.id WHERE o.name = ? AND o.type = ? ORDER BY c.colid', array($name, 'V'));
    $definition = '';
    foreach ($rows as $row) {
        $definition .= (string) ($row['text'] ?? reset($row));
    }
    return $definition;
}

function normalizeDefinition(string $definition): string
{
    $definition = strtolower($definition);
    $definition = str_replace(array("[", "]", "`", '"', "\r", "\n", "\t"), ' ', $definition);
    $definition = preg_replace('/\s+/', ' ', $definition);
    return trim((string) $definition);
}

function safeCount(PDO $pdo, string $engine, string $object): int
{
    $sql = 'SELECT COUNT(*) AS total FROM ' . quoteIdentifier($engine, $object);
    $row = fetchOne($pdo, $sql, array());
    return (int) ($row['total'] ?? reset($row) ?? 0);
}

function safeQuery(PDO $pdo, string $sqlTemplate, array $context): array
{
    $sql = interpolateSql($sqlTemplate, $context);
    return fetchAll($pdo, $sql, array());
}

function fetchAll(PDO $pdo, string $sql, array $params): array
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($params));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchOne(PDO $pdo, string $sql, array $params): ?array
{
    $rows = fetchAll($pdo, $sql, $params);
    return $rows[0] ?? null;
}

function rowsToKeys(array $rows, array $columns): array
{
    $keys = array();
    foreach ($rows as $row) {
        $parts = array();
        foreach ($columns as $column) {
            $parts[] = (string) ($row[$column] ?? '');
        }
        $keys[] = implode('|', $parts);
    }
    sort($keys);
    return array_values(array_unique($keys));
}

function compareCaseRows(array $stageRows, array $referenceRows, array $requiredPairs): bool
{
    if (count($stageRows) === 0 || count($referenceRows) === 0) {
        return false;
    }
    $stage = $stageRows[0];
    $reference = $referenceRows[0];
    foreach ($requiredPairs as $pair) {
        if ((string) ($stage[$pair[0]] ?? '') !== (string) ($reference[$pair[1]] ?? '')) {
            return false;
        }
    }
    return true;
}

function interpolateSql(string $sql, array $context): string
{
    $replacements = array();
    foreach ($context as $key => $value) {
        if (is_numeric($value)) {
            $replacements['{' . $key . '}'] = (string) $value;
        } else {
            $replacements['{' . $key . '}'] = "'" . str_replace("'", "''", (string) $value) . "'";
        }
    }
    return strtr($sql, $replacements);
}

function quoteIdentifier(string $engine, string $identifier): string
{
    if ($engine === 'mysql') {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
    return '[' . str_replace(']', ']]', $identifier) . ']';
}

function renderHuman(array $result): string
{
    $lines = array();
    $lines[] = 'Parity audit: ' . $result['flow']['label'];
    $lines[] = 'Client case: ' . $result['flow']['client_id']
        . ' -> ' . $result['flow']['expected_vendedor']
        . ' -> ' . $result['flow']['expected_personal'];
    $lines[] = 'Summary: '
        . $result['summary']['ok'] . ' ok, '
        . $result['summary']['warnings'] . ' warnings, '
        . $result['summary']['errors'] . ' errors';
    $lines[] = '';

    foreach ($result['checks'] as $check) {
        $prefix = '[OK]';
        if ($check['status'] === 'warning') {
            $prefix = '[WARN]';
        } elseif ($check['status'] === 'error') {
            $prefix = '[ERROR]';
        }
        $lines[] = $prefix . ' '
            . $check['category'] . ' '
            . $check['object'] . ' '
            . '(' . $check['type'] . '): '
            . $check['detail'];
    }

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

function renderCsv(array $result): string
{
    $fh = fopen('php://temp', 'r+');
    fputcsv($fh, array('category', 'severity', 'object', 'type', 'status', 'stage', 'reference', 'detail'));
    foreach ($result['checks'] as $check) {
        fputcsv($fh, array(
            $check['category'],
            $check['severity'],
            $check['object'],
            $check['type'],
            $check['status'],
            json_encode($check['stage'], JSON_UNESCAPED_SLASHES),
            json_encode($check['reference'], JSON_UNESCAPED_SLASHES),
            $check['detail'],
        ));
    }
    rewind($fh);
    return stream_get_contents($fh) ?: '';
}
