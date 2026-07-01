#!/usr/bin/env php
<?php
declare(strict_types=1);

const DEFAULT_FLOW = 'abm_clientes';
const DEFAULT_MODE = 'parity';
const DEFAULT_CLIENT_ID = 204;
const DEFAULT_EXPECTED_VENDEDOR = 'V00358';
const DEFAULT_EXPECTED_PERSONAL = '358';
const DEFAULT_MANIFEST = 'resources/migration-manifests/encinitas_funcional.tsv';

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
        fail("Unknown flow: {$flowId}");
    }

    $mode = strtolower((string) ($options['mode'] ?? DEFAULT_MODE));
    if (!in_array($mode, array('parity', 'provenance'), true)) {
        fail('Invalid --mode. Use parity or provenance.');
    }
    $checkManifestOnly = !empty($options['check-manifest-only']);

    $manifestPath = (string) ($options['manifest'] ?? DEFAULT_MANIFEST);
    $manifestRows = loadManifest($manifestPath);
    $flow = $flows[$flowId];
    $flowManifestRows = filterManifestRows($manifestRows, $flow['manifest_stage']);

    $context = array(
        'client_id' => (string) ($options['client-id'] ?? DEFAULT_CLIENT_ID),
        'expected_vendedor' => (string) ($options['expected-vendedor'] ?? DEFAULT_EXPECTED_VENDEDOR),
        'expected_personal' => (string) ($options['expected-personal'] ?? DEFAULT_EXPECTED_PERSONAL),
    );

    $format = strtolower((string) ($options['format'] ?? 'human'));
    $roles = buildRoles($mode, $options, $checkManifestOnly);
    validateRoleSeparation($mode, $roles, $checkManifestOnly);

    $connections = array();
    foreach ($roles as $role => $config) {
        $connections[$role] = connectDb(
            $config['engine'],
            $config['dsn'],
            $config['user'],
            $config['pass']
        );
    }

    $result = runAudit(
        $mode,
        $flow,
        $flowManifestRows,
        $connections,
        $roles,
        $context,
        $manifestPath,
        $checkManifestOnly
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
  php scripts/koi-parity-audit.php --check-manifest-only --flow=abm_clientes \\
    --baseline-engine=mysql --baseline-dsn='mysql:host=127.0.0.1;dbname=koi1_stage;charset=utf8mb4' \\
    --target-engine=mysql --target-dsn='mysql:host=127.0.0.1;dbname=encinitas_test;charset=utf8mb4'

  php scripts/koi-parity-audit.php --mode=parity --flow=abm_clientes \\
    --baseline-engine=mysql --baseline-dsn='mysql:host=127.0.0.1;dbname=koi1_stage;charset=utf8mb4' \\
    --target-engine=mysql --target-dsn='mysql:host=127.0.0.1;dbname=encinitas_test;charset=utf8mb4'

  php scripts/koi-parity-audit.php --mode=provenance --flow=abm_clientes \\
    --target-engine=mysql --target-dsn='mysql:host=127.0.0.1;dbname=encinitas_test;charset=utf8mb4' \\
    --source-role=encinitas \\
    --source-engine=odbc --source-dsn='odbc:Driver=FreeTDS;Server=sqlserver;Port=1433;Database=encinitas;TDS_Version=8.0'

Required in parity mode:
  --baseline-engine=mysql
  --baseline-dsn=PDO_DSN
  --target-engine=mysql
  --target-dsn=PDO_DSN

Required in provenance mode:
  --target-engine=mysql
  --target-dsn=PDO_DSN
  --source-role=encinitas|spiral
  --source-engine=odbc|dblib|sqlsrv
  --source-dsn=PDO_DSN

Optional:
  --check-manifest-only
  --baseline-user=USER
  --baseline-pass=PASS
  --target-user=USER
  --target-pass=PASS
  --source-user=USER
  --source-pass=PASS
  --manifest=resources/migration-manifests/encinitas_funcional.tsv
  --flow=abm_clientes
  --client-id=204
  --expected-vendedor=V00358
  --expected-personal=358
  --format=human|json|csv|all
  --json-out=/tmp/audit.json
  --csv-out=/tmp/audit.csv

Environment fallbacks:
  KOI_BASELINE_USER, KOI_BASELINE_PASS
  KOI_TARGET_USER, KOI_TARGET_PASS
  KOI_SOURCE_USER, KOI_SOURCE_PASS

Behavior:
  check-manifest-only  Validate runtime/target exact names and exact types using MySQL information_schema only.
  parity      Compare baseline MySQL koi1_stage vs target MySQL encinitas_test.
  provenance  Compare target MySQL encinitas_test vs formal SQL Server source.
  The audit blocks if manifest names/types do not match target information_schema.

TXT;
}

function buildRoles(string $mode, array $options, bool $checkManifestOnly): array
{
    $roles = array();
    if ($mode === 'parity' || $checkManifestOnly) {
        $roles['baseline'] = array(
            'engine' => requireValue($options, 'baseline-engine'),
            'dsn' => requireValue($options, 'baseline-dsn'),
            'user' => (string) ($options['baseline-user'] ?? getenv('KOI_BASELINE_USER') ?: ''),
            'pass' => (string) ($options['baseline-pass'] ?? getenv('KOI_BASELINE_PASS') ?: ''),
            'label' => 'baseline:koi1_stage',
        );
    }

    $roles['target'] = array(
        'engine' => requireValue($options, 'target-engine'),
        'dsn' => requireValue($options, 'target-dsn'),
        'user' => (string) ($options['target-user'] ?? getenv('KOI_TARGET_USER') ?: ''),
        'pass' => (string) ($options['target-pass'] ?? getenv('KOI_TARGET_PASS') ?: ''),
        'label' => 'target:encinitas_test',
    );

    if ($mode === 'provenance' && !$checkManifestOnly) {
        $roles['source'] = array(
            'engine' => requireValue($options, 'source-engine'),
            'dsn' => requireValue($options, 'source-dsn'),
            'user' => (string) ($options['source-user'] ?? getenv('KOI_SOURCE_USER') ?: ''),
            'pass' => (string) ($options['source-pass'] ?? getenv('KOI_SOURCE_PASS') ?: ''),
            'label' => 'source:' . requireValue($options, 'source-role'),
            'source_role' => strtolower(requireValue($options, 'source-role')),
        );
    }

    return $roles;
}

function validateRoleSeparation(string $mode, array $roles, bool $checkManifestOnly): void
{
    if ($mode === 'parity' || $checkManifestOnly) {
        if ($roles['baseline']['engine'] !== 'mysql' || $roles['target']['engine'] !== 'mysql') {
            fail('Parity mode and --check-manifest-only require MySQL for baseline and target.');
        }
    }

    if ($roles['target']['engine'] !== 'mysql') {
        fail('Target must always be MySQL encinitas_test.');
    }

    if ($mode === 'provenance' && !$checkManifestOnly) {
        $sourceRole = $roles['source']['source_role'] ?? '';
        if (!in_array($sourceRole, array('encinitas', 'spiral'), true)) {
            fail('Provenance mode requires --source-role=encinitas|spiral.');
        }
        if (!in_array($roles['source']['engine'], array('odbc', 'dblib', 'sqlsrv'), true)) {
            fail('Provenance mode requires SQL Server access via odbc, dblib or sqlsrv.');
        }
    }

    $usedDsns = array();
    foreach ($roles as $role => $config) {
        if (isset($usedDsns[$config['dsn']])) {
            fail("Roles {$usedDsns[$config['dsn']]} and {$role} cannot share the same DSN.");
        }
        $usedDsns[$config['dsn']] = $role;
    }
}

function requireValue(array $options, string $key): string
{
    if (!isset($options[$key]) || $options[$key] === '') {
        fail("Missing required option --{$key}");
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
        $pdo->exec('SET NAMES utf8mb4');
    }

    return $pdo;
}

function runAudit(
    string $mode,
    array $flow,
    array $manifestRows,
    array $connections,
    array $roles,
    array $context,
    string $manifestPath,
    bool $checkManifestOnly
): array {
    $checks = array();

    $baselineCatalog = isset($connections['baseline'])
        ? getObjectCatalog($connections['baseline'], $roles['baseline']['engine'])
        : array();
    $targetCatalog = getObjectCatalog($connections['target'], $roles['target']['engine']);
    $manifestChecks = buildManifestChecks(
        $flow,
        $manifestRows,
        $baselineCatalog,
        $targetCatalog,
        $connections['baseline'],
        $roles['baseline']['engine'],
        $connections['target'],
        $roles['target']['engine']
    );
    $checks = array_merge($checks, $manifestChecks);

    $blocked = hasBlockingManifestMismatch($manifestChecks);
    if ($blocked || $checkManifestOnly) {
        $summary = summarizeChecks($checks);
        return array(
            'generated_at' => gmdate('c'),
            'mode' => $mode,
            'blocked' => $blocked,
            'check_manifest_only' => $checkManifestOnly,
            'manifest' => $manifestPath,
            'flow' => buildFlowMetadata($flow, $context),
            'roles' => buildRoleMetadata($roles),
            'summary' => $summary,
            'checks' => $checks,
        );
    }

    if ($mode === 'parity') {
        $checks = array_merge(
            $checks,
            compareFlowSides(
                $flow,
                $connections['baseline'],
                $connections['target'],
                $roles['baseline']['engine'],
                $roles['target']['engine'],
                $context,
                $roles['baseline']['label'],
                $roles['target']['label']
            )
        );
    } else {
        $sourceRole = $roles['source']['source_role'];
        $checks = array_merge($checks, buildSourceRoleChecks($flow, $manifestRows, $sourceRole));
        $checks = array_merge(
            $checks,
            compareFlowSides(
                $flow,
                $connections['source'],
                $connections['target'],
                $roles['source']['engine'],
                $roles['target']['engine'],
                $context,
                $roles['source']['label'],
                $roles['target']['label']
            )
        );
    }

    $summary = summarizeChecks($checks);
    return array(
        'generated_at' => gmdate('c'),
        'mode' => $mode,
        'blocked' => false,
        'check_manifest_only' => false,
        'manifest' => $manifestPath,
        'flow' => buildFlowMetadata($flow, $context),
        'roles' => buildRoleMetadata($roles),
        'summary' => $summary,
        'checks' => $checks,
    );
}

function buildFlowMetadata(array $flow, array $context): array
{
    return array(
        'id' => $flow['id'],
        'label' => $flow['label'],
        'manifest_stage' => $flow['manifest_stage'],
        'client_id' => $context['client_id'],
        'expected_vendedor' => $context['expected_vendedor'],
        'expected_personal' => $context['expected_personal'],
    );
}

function buildRoleMetadata(array $roles): array
{
    $metadata = array();
    foreach ($roles as $role => $config) {
        $metadata[$role] = array(
            'engine' => $config['engine'],
            'label' => $config['label'],
        );
        if (!empty($config['source_role'])) {
            $metadata[$role]['source_role'] = $config['source_role'];
        }
    }
    return $metadata;
}

function loadManifest(string $path): array
{
    if (!is_file($path)) {
        fail("Manifest not found: {$path}");
    }

    $handle = fopen($path, 'r');
    if ($handle === false) {
        fail("Cannot open manifest: {$path}");
    }

    $header = fgetcsv($handle, 0, "\t");
    if ($header === false) {
        fclose($handle);
        fail("Manifest is empty: {$path}");
    }

    $rows = array();
    while (($data = fgetcsv($handle, 0, "\t")) !== false) {
        if ($data === array(null) || count(array_filter($data, 'strlen')) === 0) {
            continue;
        }
        $row = array();
        foreach ($header as $index => $column) {
            $row[$column] = $data[$index] ?? '';
        }
        $rows[] = $row;
    }
    fclose($handle);
    return $rows;
}

function filterManifestRows(array $rows, string $stage): array
{
    return array_values(array_filter($rows, static function (array $row) use ($stage): bool {
        return (string) ($row['id_etapa'] ?? '') === $stage;
    }));
}

function buildManifestChecks(
    array $flow,
    array $manifestRows,
    array $baselineCatalog,
    array $targetCatalog,
    PDO $baselinePdo,
    string $baselineEngine,
    PDO $targetPdo,
    string $targetEngine
): array
{
    $checks = array();
    $manifestIndex = indexManifestRows($manifestRows);

    foreach ($flow['objects'] as $object) {
        $expectedKey = manifestKey($object['name']);
        $manifestRow = $manifestIndex[$expectedKey] ?? null;

        if ($manifestRow === null) {
            $checks[] = buildCheck(
                'manifest_resolution',
                'critical',
                $object['name'],
                $object['type'],
                'error',
                array('manifest' => false),
                'Object queried by legacy flow is missing from the manifest.'
            );
            continue;
        }

        $checks = array_merge(
            $checks,
            buildExactManifestChecks(
                $object,
                $manifestRow,
                $baselineCatalog,
                $targetCatalog,
                $baselinePdo,
                $baselineEngine,
                $targetPdo,
                $targetEngine
            )
        );
    }

    return $checks;
}

function buildExactManifestChecks(
    array $object,
    array $manifestRow,
    array $baselineCatalog,
    array $targetCatalog,
    PDO $baselinePdo,
    string $baselineEngine,
    PDO $targetPdo,
    string $targetEngine
): array
{
    $checks = array();
    $expectedType = normalizeExactType($object['type']);
    $runtimeExact = trim((string) ($manifestRow['runtime_object_exact'] ?? ''));
    $baselineExact = trim((string) ($manifestRow['baseline_object_exact'] ?? ''));
    $targetExact = trim((string) ($manifestRow['target_object_exact'] ?? ''));
    $manifestType = normalizeExactType((string) ($manifestRow['tipo'] ?? ''));

    if (isUnresolvedManifestValue($runtimeExact)) {
        $checks[] = buildCheck(
            'runtime_resolution',
            $object['severity'],
            $object['name'],
            $object['type'],
            'error',
            array(
                'manifest_runtime_object_exact' => $runtimeExact,
            ),
            'runtime_object_exact is unresolved in the manifest.'
        );
    } elseif ($runtimeExact !== $object['name']) {
        $checks[] = buildCheck(
            'runtime_resolution',
            $object['severity'],
            $object['name'],
            $object['type'],
            'error',
            array(
                'manifest_runtime_object_exact' => $runtimeExact,
                'code_runtime_object_exact' => $object['name'],
            ),
            'runtime_object_exact does not match the literal object name used by legacy code.'
        );
    }

    if (isUnresolvedManifestValue($targetExact)) {
        $checks[] = buildCheck(
            'target_resolution',
            $object['severity'],
            $object['name'],
            $object['type'],
            'error',
            array(
                'manifest_target_object_exact' => $targetExact,
            ),
            'target_object_exact is unresolved in the manifest.'
        );
    }

    if (isUnresolvedManifestValue($baselineExact)) {
        $checks[] = buildCheck(
            'baseline_resolution',
            $object['severity'],
            $object['name'],
            $object['type'],
            'error',
            array(
                'manifest_baseline_object_exact' => $baselineExact,
            ),
            'baseline_object_exact is unresolved in the manifest.'
        );
    }

    if ($manifestType !== $expectedType) {
        $checks[] = buildCheck(
            'type_resolution',
            $object['severity'],
            $object['name'],
            $object['type'],
            'error',
            array(
                'manifest_type' => (string) ($manifestRow['tipo'] ?? ''),
                'code_expected_type' => $expectedType,
            ),
            'Manifest exact type does not match the expected runtime type.'
        );
    }

    if (!isUnresolvedManifestValue($baselineExact)) {
        $baselineMatch = resolveCatalogMatch($baselineCatalog, $baselineExact, $expectedType);
        $checks[] = buildCheck(
            'baseline_information_schema',
            $object['severity'],
            $baselineExact,
            $expectedType,
            $baselineMatch['exact'] ? 'ok' : 'error',
            array('baseline' => $baselineMatch),
            $baselineMatch['exact']
                ? 'baseline_object_exact exists in baseline information_schema with the expected exact type.'
                : buildTargetMismatchDetail($baselineExact, $expectedType, $baselineMatch, 'Baseline')
        );
    }

    if (!isUnresolvedManifestValue($targetExact)) {
        $targetMatch = resolveCatalogMatch($targetCatalog, $targetExact, $expectedType);
        $checks[] = buildCheck(
            'target_information_schema',
            $object['severity'],
            $targetExact,
            $expectedType,
            $targetMatch['exact'] ? 'ok' : 'error',
            array('target' => $targetMatch),
            $targetMatch['exact']
                ? 'target_object_exact exists in target information_schema with the expected exact type.'
                : buildTargetMismatchDetail($targetExact, $expectedType, $targetMatch, 'Target')
        );
    }

    if (!isUnresolvedManifestValue($runtimeExact)) {
        $checks = array_merge(
            $checks,
            buildRuntimeResolutionChecks($object, $runtimeExact, $baselineExact, $targetExact, $expectedType)
        );
        $checks = array_merge(
            $checks,
            buildRuntimeProbeChecks(
                $object,
                $runtimeExact,
                $expectedType,
                $baselinePdo,
                $baselineEngine,
                $targetPdo,
                $targetEngine
            )
        );
    }

    return $checks;
}

function buildRuntimeResolutionChecks(
    array $object,
    string $runtimeExact,
    string $baselineExact,
    string $targetExact,
    string $expectedType
): array {
    $checks = array();

    $baselineCaseStatus = $runtimeExact === $baselineExact ? 'ok' : 'warning';
    $targetCaseStatus = $runtimeExact === $targetExact ? 'ok' : 'warning';

    $checks[] = buildCheck(
        'runtime_resolution',
        $object['severity'],
        $runtimeExact,
        $expectedType,
        'ok',
        array(
            'code_runtime_object_exact' => $object['name'],
            'manifest_runtime_object_exact' => $runtimeExact,
        ),
        'runtime_object_exact matches the literal object name used by legacy code.'
    );

    if (!isUnresolvedManifestValue($baselineExact)) {
        $checks[] = buildCheck(
            'case_compatibility',
            $object['severity'],
            $runtimeExact,
            $expectedType,
            $baselineCaseStatus,
            array(
                'runtime_object_exact' => $runtimeExact,
                'baseline_object_exact' => $baselineExact,
            ),
            $baselineCaseStatus === 'ok'
                ? 'Runtime literal and baseline physical object use the same casing.'
                : 'Runtime literal and baseline physical object differ only by casing; this is informational if runtime resolution succeeds.'
        );
    }

    if (!isUnresolvedManifestValue($targetExact)) {
        $checks[] = buildCheck(
            'case_compatibility',
            $object['severity'],
            $runtimeExact,
            $expectedType,
            $targetCaseStatus,
            array(
                'runtime_object_exact' => $runtimeExact,
                'target_object_exact' => $targetExact,
            ),
            $targetCaseStatus === 'ok'
                ? 'Runtime literal and target physical object use the same casing.'
                : 'Runtime literal and target physical object differ only by casing; this is informational if runtime resolution succeeds.'
        );
    }

    return $checks;
}

function buildRuntimeProbeChecks(
    array $object,
    string $runtimeExact,
    string $expectedType,
    PDO $baselinePdo,
    string $baselineEngine,
    PDO $targetPdo,
    string $targetEngine
): array {
    $checks = array();
    $baselineProbe = probeRuntimeResolution($baselinePdo, $baselineEngine, $runtimeExact, $expectedType);
    $targetProbe = probeRuntimeResolution($targetPdo, $targetEngine, $runtimeExact, $expectedType);

    $checks[] = buildCheck(
        'runtime_probe',
        $object['severity'],
        $runtimeExact,
        $expectedType,
        $baselineProbe['ok'] ? 'ok' : 'error',
        array('baseline' => $baselineProbe),
        $baselineProbe['ok']
            ? 'Baseline resolves the runtime literal with a read-only limited SELECT.'
            : 'Baseline does not resolve the runtime literal with a read-only limited SELECT.'
    );

    $checks[] = buildCheck(
        'runtime_probe',
        $object['severity'],
        $runtimeExact,
        $expectedType,
        $targetProbe['ok'] ? 'ok' : 'error',
        array('target' => $targetProbe),
        $targetProbe['ok']
            ? 'Target resolves the runtime literal with a read-only limited SELECT.'
            : 'Target does not resolve the runtime literal with a read-only limited SELECT.'
    );

    return $checks;
}

function buildTargetMismatchDetail(string $name, string $type, array $targetMatch, string $sideLabel): string
{
    if ($targetMatch['case_insensitive']) {
        return "{$sideLabel} information_schema expects {$type} {$name}, but exposes {$targetMatch['actual_name']} as {$targetMatch['actual_type']}.";
    }
    return "{$sideLabel} information_schema does not expose {$type} {$name}.";
}

function hasBlockingManifestMismatch(array $checks): bool
{
    foreach ($checks as $check) {
        if ($check['status'] === 'error') {
            return true;
        }
    }
    return false;
}

function buildSourceRoleChecks(array $flow, array $manifestRows, string $sourceRole): array
{
    $checks = array();
    $manifestIndex = indexManifestRows($manifestRows);
    foreach ($flow['objects'] as $object) {
        $key = manifestKey($object['name']);
        $row = $manifestIndex[$key] ?? null;
        if ($row === null) {
            continue;
        }
        $declared = strtolower((string) ($row['origen_sqlserver'] ?? ''));
        $checks[] = buildCheck(
            'source_role',
            $object['severity'],
            $object['name'],
            $object['type'],
            $declared === $sourceRole ? 'ok' : 'error',
            array(
                'manifest_origin' => $declared,
                'requested_source' => $sourceRole,
            ),
            $declared === $sourceRole
                ? 'Manifest origin matches the requested SQL Server source role.'
                : 'Manifest origin conflicts with the requested SQL Server source role.'
        );
    }
    return $checks;
}

function compareFlowSides(
    array $flow,
    PDO $leftPdo,
    PDO $rightPdo,
    string $leftEngine,
    string $rightEngine,
    array $context,
    string $leftLabel,
    string $rightLabel
): array {
    $checks = array();

    foreach ($flow['objects'] as $object) {
        $leftExists = objectExists($leftPdo, $leftEngine, $object['name'], $object['type']);
        $rightExists = objectExists($rightPdo, $rightEngine, $object['name'], $object['type']);
        $checks[] = buildCheck(
            'object',
            $object['severity'],
            $object['name'],
            $object['type'],
            ($leftExists && $rightExists) ? 'ok' : 'error',
            array($leftLabel => array('exists' => $leftExists), $rightLabel => array('exists' => $rightExists)),
            (!$leftExists || !$rightExists)
                ? 'Object missing on one side.'
                : 'Object exists on both sides.'
        );

        if (normalizeExactType($object['type']) === 'VIEW' && $leftExists && $rightExists) {
            $leftDefinition = normalizeDefinition(getViewDefinition($leftPdo, $leftEngine, $object['name']));
            $rightDefinition = normalizeDefinition(getViewDefinition($rightPdo, $rightEngine, $object['name']));
            $checks[] = buildCheck(
                'view_definition',
                $object['severity'],
                $object['name'],
                'VIEW',
                ($leftDefinition === $rightDefinition) ? 'ok' : 'error',
                array(
                    $leftLabel => array('definition_hash' => sha1($leftDefinition)),
                    $rightLabel => array('definition_hash' => sha1($rightDefinition)),
                ),
                ($leftDefinition === $rightDefinition)
                    ? 'View definition matches after normalization.'
                    : 'View definition differs after normalization.'
            );
        }
    }

    foreach ($flow['counts'] as $countCheck) {
        $leftCount = safeCount($leftPdo, $leftEngine, $countCheck['object']);
        $rightCount = safeCount($rightPdo, $rightEngine, $countCheck['object']);
        $sameCount = ((string) $leftCount === (string) $rightCount);
        $status = $sameCount ? 'ok' : (string) ($countCheck['diff_status'] ?? 'warning');
        $checks[] = buildCheck(
            (string) ($countCheck['category'] ?? 'count'),
            $countCheck['severity'],
            $countCheck['object'],
            $countCheck['type'],
            $status,
            array($leftLabel => array('count' => $leftCount), $rightLabel => array('count' => $rightCount)),
            $sameCount
                ? (string) ($countCheck['match_detail'] ?? 'Row count matches.')
                : (string) ($countCheck['diff_detail'] ?? 'Row count differs.')
        );
    }

    foreach ($flow['keys'] as $keyCheck) {
        $leftRows = safeQuery($leftPdo, $keyCheck['sql'], $context);
        $rightRows = safeQuery($rightPdo, $keyCheck['sql'], $context);
        $leftKeys = rowsToKeys($leftRows, $keyCheck['columns']);
        $rightKeys = rowsToKeys($rightRows, $keyCheck['columns']);
        $missingInLeft = array_values(array_diff($rightKeys, $leftKeys));
        $missingInRight = array_values(array_diff($leftKeys, $rightKeys));
        $status = (count($missingInLeft) === 0 && count($missingInRight) === 0) ? 'ok' : 'warning';
        $checks[] = buildCheck(
            'functional_key',
            $keyCheck['severity'],
            $keyCheck['object'],
            $keyCheck['type'],
            $status,
            array(
                $leftLabel => array('keys' => count($leftKeys), 'missing' => array_slice($missingInLeft, 0, 20)),
                $rightLabel => array('keys' => count($rightKeys), 'missing' => array_slice($missingInRight, 0, 20)),
            ),
            ($status === 'ok')
                ? 'Functional keys match.'
                : 'Functional keys differ.'
        );
    }

    foreach ($flow['joins'] as $joinCheck) {
        $leftRows = safeQuery($leftPdo, $joinCheck['sql'], $context);
        $rightRows = safeQuery($rightPdo, $joinCheck['sql'], $context);
        $checks[] = buildCheck(
            'join_break',
            $joinCheck['severity'],
            $joinCheck['object'],
            $joinCheck['type'],
            (count($leftRows) === 0 && count($rightRows) === 0) ? 'ok' : 'warning',
            array(
                $leftLabel => array('rows' => count($leftRows), 'sample' => array_slice($leftRows, 0, 5)),
                $rightLabel => array('rows' => count($rightRows), 'sample' => array_slice($rightRows, 0, 5)),
            ),
            (count($leftRows) === 0 && count($rightRows) === 0)
                ? 'No broken joins detected.'
                : 'Broken joins or orphan rows detected.'
        );
    }

    foreach ($flow['cases'] as $caseCheck) {
        $leftRows = safeQuery($leftPdo, $caseCheck['sql'], $context);
        $rightRows = safeQuery($rightPdo, $caseCheck['sql'], $context);
        $status = compareCaseRows($leftRows, $rightRows, $caseCheck['required_pairs']) ? 'ok' : 'error';
        $checks[] = buildCheck(
            'case',
            $caseCheck['severity'],
            $caseCheck['object'],
            $caseCheck['type'],
            $status,
            array($leftLabel => array('rows' => $leftRows), $rightLabel => array('rows' => $rightRows)),
            ($status === 'ok')
                ? 'Mandatory case matches.'
                : 'Mandatory case differs or is incomplete.'
        );
    }

    return $checks;
}

function getFlows(): array
{
    return array(
        'abm_clientes' => array(
            'id' => 'abm_clientes',
            'label' => 'Base de sesion y ABM Clientes',
            'manifest_stage' => 'ET01 Base de sesión y ABM Clientes',
            'objects' => array(
                array('name' => 'users', 'type' => 'BASE TABLE', 'severity' => 'critical'),
                array('name' => 'roles', 'type' => 'BASE TABLE', 'severity' => 'critical'),
                array('name' => 'roles_por_usuario', 'type' => 'BASE TABLE', 'severity' => 'critical'),
                array('name' => 'roles_por_usuario_v', 'type' => 'VIEW', 'severity' => 'critical'),
                array('name' => 'funcionalidades_por_rol', 'type' => 'BASE TABLE', 'severity' => 'critical'),
                array('name' => 'personal', 'type' => 'BASE TABLE', 'severity' => 'critical'),
                array('name' => 'Operadores', 'type' => 'BASE TABLE', 'severity' => 'critical'),
                array('name' => 'operadores_v', 'type' => 'VIEW', 'severity' => 'critical'),
                array('name' => 'Clientes', 'type' => 'BASE TABLE', 'severity' => 'critical'),
                array('name' => 'sucursales_clientes', 'type' => 'BASE TABLE', 'severity' => 'critical'),
                array('name' => 'sucursales_v', 'type' => 'VIEW', 'severity' => 'critical'),
                array('name' => 'Contactos', 'type' => 'BASE TABLE', 'severity' => 'high'),
                array('name' => 'areas_empresa', 'type' => 'BASE TABLE', 'severity' => 'high'),
                array('name' => 'condiciones_iva', 'type' => 'BASE TABLE', 'severity' => 'high'),
                array('name' => 'Formas_pago', 'type' => 'BASE TABLE', 'severity' => 'high'),
                array('name' => 'grupo_empresa', 'type' => 'BASE TABLE', 'severity' => 'high'),
                array('name' => 'Grupos_clientes', 'type' => 'BASE TABLE', 'severity' => 'high'),
                array('name' => 'Paises', 'type' => 'BASE TABLE', 'severity' => 'high'),
                array('name' => 'Provincias', 'type' => 'BASE TABLE', 'severity' => 'high'),
                array('name' => 'localidades', 'type' => 'BASE TABLE', 'severity' => 'high'),
            ),
            'counts' => array(
                array('object' => 'users', 'type' => 'BASE TABLE', 'severity' => 'medium'),
                array('object' => 'roles', 'type' => 'BASE TABLE', 'severity' => 'medium'),
                array('object' => 'roles_por_usuario', 'type' => 'BASE TABLE', 'severity' => 'medium'),
                array('object' => 'personal', 'type' => 'BASE TABLE', 'severity' => 'medium'),
                array('object' => 'Operadores', 'type' => 'BASE TABLE', 'severity' => 'medium'),
                array('object' => 'Clientes', 'type' => 'BASE TABLE', 'severity' => 'medium'),
                array('object' => 'sucursales_clientes', 'type' => 'BASE TABLE', 'severity' => 'medium'),
                array('object' => 'Contactos', 'type' => 'BASE TABLE', 'severity' => 'medium'),
                array(
                    'object' => 'condiciones_iva',
                    'type' => 'BASE TABLE',
                    'severity' => 'critical',
                    'category' => 'fixture_count',
                    'diff_status' => 'error',
                    'match_detail' => 'Mandatory fixture condiciones_iva count matches between baseline and target.',
                    'diff_detail' => 'Mandatory fixture condiciones_iva count differs between baseline and target.'
                ),
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
                    'sql' => "SELECT cod_operador, cod_personal, tipo_operador FROM operadores_v WHERE cod_operador = {expected_vendedor} OR cod_personal = {expected_personal}"
                ),
            ),
        ),
    );
}

function buildCheck(string $category, string $severity, string $object, string $type, string $status, array $sides, string $detail): array
{
    return array(
        'category' => $category,
        'severity' => $severity,
        'object' => $object,
        'type' => $type,
        'status' => $status,
        'sides' => $sides,
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

function getObjectCatalog(PDO $pdo, string $engine): array
{
    $catalog = array();
    if ($engine === 'mysql') {
        $tables = fetchAll(
            $pdo,
            "SELECT TABLE_NAME AS object_name, TABLE_TYPE AS object_type FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()",
            array()
        );
        foreach ($tables as $row) {
            $catalog[] = array('name' => (string) $row['object_name'], 'type' => normalizeExactType((string) $row['object_type']));
        }

        $routines = fetchAll(
            $pdo,
            "SELECT ROUTINE_NAME AS object_name, ROUTINE_TYPE AS object_type FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()",
            array()
        );
        foreach ($routines as $row) {
            $catalog[] = array('name' => (string) $row['object_name'], 'type' => normalizeExactType((string) $row['object_type']));
        }
        return $catalog;
    }

    $rows = fetchAll(
        $pdo,
        "SELECT name AS object_name, type FROM sysobjects WHERE type IN ('U', 'V', 'P', 'FN')",
        array()
    );
    foreach ($rows as $row) {
        $catalog[] = array(
            'name' => (string) $row['object_name'],
            'type' => sqlServerTypeToExact((string) $row['type']),
        );
    }
    return $catalog;
}

function sqlServerTypeToExact(string $type): string
{
    $map = array('U' => 'BASE TABLE', 'V' => 'VIEW', 'P' => 'PROCEDURE', 'FN' => 'FUNCTION');
    return $map[$type] ?? 'BASE TABLE';
}

function normalizeExactType(string $type): string
{
    $normalized = strtoupper(trim($type));
    if ($normalized === 'TABLE' || $normalized === 'BASE_TABLE') {
        return 'BASE TABLE';
    }
    return $normalized;
}

function resolveCatalogMatch(array $catalog, string $name, string $type): array
{
    $type = normalizeExactType($type);
    foreach ($catalog as $row) {
        if ($row['name'] === $name && $row['type'] === $type) {
            return array(
                'exact' => true,
                'case_insensitive' => true,
                'actual_name' => $row['name'],
                'actual_type' => $row['type'],
            );
        }
    }

    foreach ($catalog as $row) {
        if (strcasecmp($row['name'], $name) === 0) {
            return array(
                'exact' => false,
                'case_insensitive' => true,
                'actual_name' => $row['name'],
                'actual_type' => $row['type'],
            );
        }
    }

    return array(
        'exact' => false,
        'case_insensitive' => false,
        'actual_name' => null,
        'actual_type' => null,
    );
}

function indexManifestRows(array $rows): array
{
    $index = array();
    foreach ($rows as $row) {
        $index[manifestKey((string) $row['objeto'])] = $row;
    }
    return $index;
}

function manifestKey(string $object): string
{
    return $object;
}

function isUnresolvedManifestValue(string $value): bool
{
    if ($value === '') {
        return true;
    }
    return strpos(strtoupper($value), 'PENDIENTE') === 0;
}

function objectExists(PDO $pdo, string $engine, string $name, string $type): bool
{
    $type = normalizeExactType($type);
    if ($engine === 'mysql') {
        if ($type === 'PROCEDURE' || $type === 'FUNCTION') {
            $sql = 'SELECT 1 FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = ? AND ROUTINE_TYPE = ?';
            return (bool) fetchOne($pdo, $sql, array($name, $type));
        }
        $tableType = ($type === 'VIEW') ? 'VIEW' : 'BASE TABLE';
        $sql = 'SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND TABLE_TYPE = ?';
        return (bool) fetchOne($pdo, $sql, array($name, $tableType));
    }

    $map = array('BASE TABLE' => 'U', 'VIEW' => 'V', 'PROCEDURE' => 'P', 'FUNCTION' => 'FN');
    $sql = 'SELECT 1 FROM sysobjects WHERE name = ? AND type = ?';
    return (bool) fetchOne($pdo, $sql, array($name, $map[$type] ?? 'U'));
}

function probeRuntimeResolution(PDO $pdo, string $engine, string $name, string $type): array
{
    $type = normalizeExactType($type);
    if ($type === 'PROCEDURE' || $type === 'FUNCTION') {
        return array(
            'ok' => true,
            'skipped' => true,
            'detail' => 'Runtime probe skipped for routines.',
        );
    }

    $sql = 'SELECT 1 AS probe FROM ' . quoteIdentifier($engine, $name);
    if ($engine === 'mysql') {
        $sql .= ' LIMIT 1';
    }

    try {
        $rows = fetchAll($pdo, $sql, array());
        return array(
            'ok' => true,
            'rows' => count($rows),
            'sql' => $sql,
        );
    } catch (Throwable $throwable) {
        return array(
            'ok' => false,
            'error' => $throwable->getMessage(),
            'sql' => $sql,
        );
    }
}

function getViewDefinition(PDO $pdo, string $engine, string $name): string
{
    if ($engine === 'mysql') {
        $identifier = '`' . str_replace('`', '``', $name) . '`';
        $row = $pdo->query("SHOW CREATE VIEW {$identifier}")->fetch(PDO::FETCH_ASSOC);
        return (string) ($row['Create View'] ?? '');
    }

    $rows = fetchAll(
        $pdo,
        'SELECT c.text FROM sysobjects o JOIN syscomments c ON o.id = c.id WHERE o.name = ? AND o.type = ? ORDER BY c.colid',
        array($name, 'V')
    );
    $definition = '';
    foreach ($rows as $row) {
        $definition .= (string) ($row['text'] ?? reset($row));
    }
    return $definition;
}

function normalizeDefinition(string $definition): string
{
    $definition = strtolower($definition);
    $definition = str_replace(array('[', ']', '`', '"', "\r", "\n", "\t"), ' ', $definition);
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

function compareCaseRows(array $leftRows, array $rightRows, array $requiredPairs): bool
{
    if (count($leftRows) === 0 || count($rightRows) === 0) {
        return false;
    }
    $left = $leftRows[0];
    $right = $rightRows[0];
    foreach ($requiredPairs as $pair) {
        if ((string) ($left[$pair[0]] ?? '') !== (string) ($right[$pair[1]] ?? '')) {
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
    $lines[] = 'Audit mode: ' . $result['mode'];
    $lines[] = 'Flow: ' . $result['flow']['label'];
    $lines[] = 'Roles: ' . implode(', ', array_map(static function (array $role): string {
        return $role['label'] . ' [' . $role['engine'] . ']';
    }, $result['roles']));
    $lines[] = 'Manifest: ' . $result['manifest'];
    if (!empty($result['check_manifest_only'])) {
        $lines[] = 'Check manifest only: yes';
    }
    $lines[] = 'Client case: ' . $result['flow']['client_id']
        . ' -> ' . $result['flow']['expected_vendedor']
        . ' -> ' . $result['flow']['expected_personal'];
    $lines[] = 'Summary: '
        . $result['summary']['ok'] . ' ok, '
        . $result['summary']['warnings'] . ' warnings, '
        . $result['summary']['errors'] . ' errors';
    if (!empty($result['blocked'])) {
        $lines[] = 'Blocked: yes, manifest/name resolution mismatch against target information_schema.';
    }
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
    fputcsv($fh, array('category', 'severity', 'object', 'type', 'status', 'sides', 'detail'));
    foreach ($result['checks'] as $check) {
        fputcsv($fh, array(
            $check['category'],
            $check['severity'],
            $check['object'],
            $check['type'],
            $check['status'],
            json_encode($check['sides'], JSON_UNESCAPED_SLASHES),
            $check['detail'],
        ));
    }
    rewind($fh);
    return stream_get_contents($fh) ?: '';
}

function fail(string $message): void
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(2);
}
