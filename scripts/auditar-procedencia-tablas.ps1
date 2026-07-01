param(
  [string]$Root = 'C:\dev\encinitas',
  [string]$TriggersInventarioPath = '\\SERVER\Produccion\spiral_triggers_inventario.CSV',
  [string]$TriggersEncinitasPath = '\\SERVER\Produccion\spiral_triggers_encinitas.CSV',
  [string]$ProduccionCsvPath = 'C:\dev\encinitas\docs\migracion\produccion_operativa_spiral.csv',
  [string]$OutCsv = 'C:\dev\encinitas\docs\migracion\manifiesto_procedencia_tablas_spiral.csv',
  [string]$OutMd = 'C:\dev\encinitas\docs\migracion\manifiesto_procedencia_tablas_spiral.md'
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Load-EnvFile {
  param([string]$Path)
  $map = @{}
  if (-not (Test-Path $Path)) { return $map }
  foreach ($line in Get-Content $Path) {
    $trim = $line.Trim()
    if ($trim.Length -eq 0 -or $trim.StartsWith('#') -or -not $trim.Contains('=')) { continue }
    $parts = $trim.Split('=',2)
    $k = $parts[0].Trim()
    $v = $parts[1].Trim().Trim('"').Trim("'")
    if ($k) { $map[$k] = $v }
  }
  return $map
}

function New-OdbcConnection {
  param([string]$ConnString)
  $conn = New-Object System.Data.Odbc.OdbcConnection($ConnString)
  $conn.Open()
  return $conn
}

function Open-FirstConnection {
  param([string[]]$Candidates,[string]$Label)
  $errors = @()
  foreach ($cs in $Candidates) {
    if ([string]::IsNullOrWhiteSpace($cs)) { continue }
    try {
      $c = New-OdbcConnection -ConnString $cs
      return $c
    } catch {
      $errors += $_.Exception.Message
    }
  }
  $msg = if ($errors.Count -gt 0) { $errors[0] } else { 'sin candidatos de conexion' }
  throw "No se pudo abrir conexion $Label. Primer error: $msg"
}

function Invoke-Query {
  param(
    [System.Data.Odbc.OdbcConnection]$Connection,
    [string]$Sql
  )
  $cmd = $Connection.CreateCommand()
  $cmd.CommandTimeout = 120
  $cmd.CommandText = $Sql
  $da = New-Object System.Data.Odbc.OdbcDataAdapter($cmd)
  $dt = New-Object System.Data.DataTable
  [void]$da.Fill($dt)
  return ,$dt
}

function Invoke-Scalar {
  param([System.Data.Odbc.OdbcConnection]$Connection,[string]$Sql)
  $cmd = $Connection.CreateCommand()
  $cmd.CommandTimeout = 120
  $cmd.CommandText = $Sql
  return $cmd.ExecuteScalar()
}

function Quote-SqlLiteral([string]$s) {
  if ($null -eq $s) { return "''" }
  return "'" + $s.Replace("'","''") + "'"
}

function Quote-SqlSrvIdent([string]$s) {
  return '[' + $s.Replace(']',']]') + ']'
}

function Quote-MySqlIdent([string]$s) {
  return '`' + $s.Replace('`','``') + '`'
}

$envMap = Load-EnvFile -Path (Join-Path $Root '.env')

$sqlUserCandidates = @($envMap['DB_KOI_USERNAME'], $envMap['DB_K1_USERNAME'], $envMap['DB_USERNAME']) | Where-Object { $_ }
$sqlPassCandidates = @($envMap['DB_KOI_PASSWORD'], $envMap['DB_K1_PASSWORD'], $envMap['DB_PASSWORD']) | Where-Object { $_ }
$sqlHostCandidates = @($envMap['DB_ODBC_HOST'], '192.168.2.100') | Where-Object { $_ } | Select-Object -Unique

$spiralCandidates = @()
foreach ($driver in @('SQL Server','ODBC Driver 18 for SQL Server','ODBC Driver 17 for SQL Server')) {
  foreach ($dbHost in $sqlHostCandidates) {
    foreach ($u in $sqlUserCandidates) {
      foreach ($p in $sqlPassCandidates) {
        $spiralCandidates += "Driver={$driver};Server=$dbHost;Database=spiral;Uid=$u;Pwd=$p"
      }
    }
  }
}

$mysqlUserCandidates = @($envMap['DB_KOI_USERNAME'], $envMap['DB_USERNAME'], $envMap['DB_K1_USERNAME']) | Where-Object { $_ }
$mysqlPassCandidates = @($envMap['DB_KOI_PASSWORD'], $envMap['DB_PASSWORD'], $envMap['DB_K1_PASSWORD']) | Where-Object { $_ }
$mysqlHostCandidates = @($envMap['DB_HOST'], $envMap['DB_K1_HOST'], '192.168.2.210') | Where-Object { $_ } | Select-Object -Unique

$targetCandidates = @()
foreach ($driver in @('MySQL ODBC 9.7 Unicode Driver','MySQL ODBC 8.0 Unicode Driver')) {
  foreach ($dbHost in $mysqlHostCandidates) {
    foreach ($u in $mysqlUserCandidates) {
      foreach ($p in $mysqlPassCandidates) {
        $targetCandidates += "Driver={$driver};Server=$dbHost;Database=encinitas_test;User=$u;Password=$p;Option=3;"
      }
    }
  }
}

$spiralConn = Open-FirstConnection -Candidates $spiralCandidates -Label 'sqlsrv_spiral'
$targetConn = Open-FirstConnection -Candidates $targetCandidates -Label 'encinitas_test'

try {
  if (-not (Test-Path $TriggersInventarioPath)) { throw "No existe $TriggersInventarioPath" }
  if (-not (Test-Path $TriggersEncinitasPath)) { throw "No existe $TriggersEncinitasPath" }
  if (-not (Test-Path $ProduccionCsvPath)) { throw "No existe $ProduccionCsvPath" }

  $triggerHeaders = 'schema_name','table_name','trigger_name','events','modify_date','object_id'
  $tr1 = Import-Csv -Path $TriggersInventarioPath -Header $triggerHeaders
  $tr2 = Import-Csv -Path $TriggersEncinitasPath -Header $triggerHeaders
  $triggerRows = @($tr1 + $tr2)

  $triggerByTable = @{}
  foreach ($r in $triggerRows) {
    $tableName = ([string]$r.table_name).Trim()
    if (-not $tableName) { continue }
    $k = $tableName.ToLower()
    if (-not $triggerByTable.ContainsKey($k)) {
      $triggerByTable[$k] = [ordered]@{
        table = $tableName
        has_any = $false
        has_sync = $false
        names = New-Object System.Collections.Generic.List[string]
      }
    }
    $e = $triggerByTable[$k]
    $e.has_any = $true
    $tn = ([string]$r.trigger_name).Trim()
    if ($tn) {
      if (-not $e.names.Contains($tn)) { [void]$e.names.Add($tn) }
      if ($tn -match '(?i)to_encinitas' -or $tn -match '(?i)^trg_sync_' -or $tn -match '(?i)_sync_') {
        $e.has_sync = $true
      }
    }
  }

  $inv = Import-Csv -Path $ProduccionCsvPath
  $objByLower = @{}
  foreach ($r in $inv) {
    $name = ([string]$r.objeto_sql).Trim()
    if (-not $name) { continue }
    $k = $name.ToLower()
    if (-not $objByLower.ContainsKey($k)) {
      $objByLower[$k] = [ordered]@{ objeto_sql = $name; tipos = New-Object System.Collections.Generic.HashSet[string] }
    }
    [void]$objByLower[$k].tipos.Add((([string]$r.tipo).Trim().ToLower()))
  }

  $objects59 = $objByLower.Keys | Sort-Object
  $tableKeys = @()
  foreach ($k in $objects59) {
    if ($objByLower[$k].tipos.Contains('tabla')) { $tableKeys += $k }
  }
  $tableKeys = $tableKeys | Sort-Object -Unique

  $inList = ($tableKeys | ForEach-Object { Quote-SqlLiteral $_ }) -join ','

  $spiralObjSql = @"
SELECT LOWER(o.name) AS name_lc, o.name AS object_name,
CASE
  WHEN o.xtype = 'U' THEN 'BASE TABLE'
  WHEN o.xtype = 'V' THEN 'VIEW'
  WHEN o.xtype IN ('P') THEN 'PROCEDURE'
  WHEN o.xtype IN ('FN','IF','TF') THEN 'FUNCTION'
  ELSE o.xtype
END AS object_type
FROM dbo.sysobjects o
INNER JOIN dbo.sysusers u ON u.uid = o.uid
WHERE u.name = 'dbo'
  AND LOWER(o.name) IN ($inList)
  AND o.xtype IN ('U','V','P','FN','IF','TF')
"@
  $spiralObj = Invoke-Query -Connection $spiralConn -Sql $spiralObjSql
  $spiralObjMap = @{}
  foreach ($r in $spiralObj.Rows) {
    $k = [string]$r['name_lc']
    if (-not $spiralObjMap.ContainsKey($k)) {
      $spiralObjMap[$k] = [ordered]@{ object_name = [string]$r['object_name']; object_type = [string]$r['object_type'] }
    }
  }

  $myObjSql = @"
SELECT LOWER(t.table_name) AS name_lc, t.table_name, t.table_type
FROM information_schema.tables t
WHERE t.table_schema = 'encinitas_test'
  AND LOWER(t.table_name) IN ($inList)
"@
  $myObj = Invoke-Query -Connection $targetConn -Sql $myObjSql
  $myObjMap = @{}
  foreach ($r in $myObj.Rows) {
    $k = [string]$r['name_lc']
    if (-not $myObjMap.ContainsKey($k)) {
      $myObjMap[$k] = [ordered]@{ table_name = [string]$r['table_name']; table_type = [string]$r['table_type'] }
    }
  }

  $pkSrcSql = @"
SELECT LOWER(t.name) AS name_lc, c.name AS column_name, k.keyno AS key_ordinal
FROM dbo.sysobjects t
INNER JOIN dbo.sysusers u ON u.uid=t.uid
INNER JOIN dbo.sysindexes i ON i.id=t.id AND (i.status & 2048)=2048
INNER JOIN dbo.sysindexkeys k ON k.id=i.id AND k.indid=i.indid
INNER JOIN dbo.syscolumns c ON c.id=k.id AND c.colid=k.colid
WHERE u.name='dbo'
  AND t.xtype='U'
  AND LOWER(t.name) IN ($inList)
ORDER BY name_lc, k.keyno
"@
  $pkSrcDt = Invoke-Query -Connection $spiralConn -Sql $pkSrcSql
  $pkSrcMap = @{}
  foreach ($r in $pkSrcDt.Rows) {
    $k = [string]$r['name_lc']
    if (-not $pkSrcMap.ContainsKey($k)) { $pkSrcMap[$k] = New-Object System.Collections.Generic.List[string] }
    [void]$pkSrcMap[$k].Add([string]$r['column_name'])
  }

  $pkTgtSql = @"
SELECT LOWER(k.table_name) AS name_lc, k.column_name, k.ordinal_position
FROM information_schema.key_column_usage k
WHERE k.table_schema='encinitas_test'
  AND k.constraint_name='PRIMARY'
  AND LOWER(k.table_name) IN ($inList)
ORDER BY name_lc, k.ordinal_position
"@
  $pkTgtDt = Invoke-Query -Connection $targetConn -Sql $pkTgtSql
  $pkTgtMap = @{}
  foreach ($r in $pkTgtDt.Rows) {
    $k = [string]$r['name_lc']
    if (-not $pkTgtMap.ContainsKey($k)) { $pkTgtMap[$k] = New-Object System.Collections.Generic.List[string] }
    [void]$pkTgtMap[$k].Add([string]$r['column_name'])
  }

  $rowsOut = New-Object System.Collections.Generic.List[object]

  foreach ($k in $tableKeys) {
    $logical = $objByLower[$k].objeto_sql
    $tr = if ($triggerByTable.ContainsKey($k)) { $triggerByTable[$k] } else { $null }
    $hasAnyTrigger = $tr -ne $null -and [bool]$tr.has_any
    $hasSyncTrigger = $tr -ne $null -and [bool]$tr.has_sync

    $spiralExists = $spiralObjMap.ContainsKey($k)
    $spiralType = if ($spiralExists) { $spiralObjMap[$k].object_type } else { '' }
    $spiralName = if ($spiralExists) { $spiralObjMap[$k].object_name } else { $logical }

    $targetExists = $myObjMap.ContainsKey($k)
    $targetType = if ($targetExists) { $myObjMap[$k].table_type } else { '' }
    $targetName = if ($targetExists) { $myObjMap[$k].table_name } else { '' }

    $classification = if ($hasSyncTrigger) {
      'CONFIRMADA_POR_TRIGGER_SPIRAL'
    } elseif ($hasAnyTrigger) {
      'POSIBLE_POR_TRIGGER'
    } else {
      'PRODUCTIVA_OBLIGATORIA_POR_INVENTARIO'
    }

    if (-not $spiralExists -and $targetExists -and -not $hasSyncTrigger) {
      $classification = 'LOCAL_SIN_EVIDENCIA_DE_SYNC'
    }

    $spiralCount = $null
    $targetCount = $null
    if ($spiralExists -and $spiralType -in @('BASE TABLE','VIEW')) {
      $sqlCount = "SELECT COUNT_BIG(1) FROM [dbo]." + (Quote-SqlSrvIdent $spiralName)
      try { $spiralCount = [int64](Invoke-Scalar -Connection $spiralConn -Sql $sqlCount) } catch { $spiralCount = $null }
    }
    if ($targetExists -and $targetType -in @('BASE TABLE','VIEW')) {
      $myCountSql = "SELECT COUNT(1) FROM " + (Quote-MySqlIdent $targetName)
      try { $targetCount = [int64](Invoke-Scalar -Connection $targetConn -Sql $myCountSql) } catch { $targetCount = $null }
    }

    $pkSrc = if ($pkSrcMap.ContainsKey($k)) { ($pkSrcMap[$k] -join ',') } else { '' }
    $pkTgt = if ($pkTgtMap.ContainsKey($k)) { ($pkTgtMap[$k] -join ',') } else { '' }

    $pkStatus = if (-not $pkSrc) {
      'SIN_PK_EN_SPIRAL'
    } elseif (-not $targetExists) {
      'TARGET_FALTANTE'
    } elseif (-not $pkTgt) {
      'PK_FALTANTE_TARGET'
    } elseif ($pkSrc.ToLower() -eq $pkTgt.ToLower()) {
      'PK_OK'
    } else {
      'PK_DIFERENTE'
    }

    $missingKeys = $null
    $missingMethod = ''
    if ($spiralExists -and $targetExists -and $pkStatus -eq 'PK_OK' -and $pkSrcMap[$k].Count -eq 1 -and $spiralCount -ne $null -and $spiralCount -le 50000) {
      $pkColSrc = $pkSrcMap[$k][0]
      $pkColTgt = $pkTgtMap[$k][0]
      $srcSql = "SELECT CAST(" + (Quote-SqlSrvIdent $pkColSrc) + " AS NVARCHAR(255)) AS k FROM [dbo]." + (Quote-SqlSrvIdent $spiralName)
      $tgtSql = "SELECT CAST(" + (Quote-MySqlIdent $pkColTgt) + " AS CHAR(255)) AS k FROM " + (Quote-MySqlIdent $targetName)
      try {
        $srcDt = Invoke-Query -Connection $spiralConn -Sql $srcSql
        $tgtDt = Invoke-Query -Connection $targetConn -Sql $tgtSql
        $set = New-Object System.Collections.Generic.HashSet[string]
        foreach ($r in $tgtDt.Rows) { [void]$set.Add(([string]$r['k'])) }
        $miss = 0
        foreach ($r in $srcDt.Rows) {
          $kv = [string]$r['k']
          if (-not $set.Contains($kv)) { $miss++ }
        }
        $missingKeys = $miss
        $missingMethod = 'comparacion_pk_1col_hasta_50000_filas'
      } catch {
        $missingMethod = 'no_disponible_error_consulta'
      }
    } elseif ($pkSrcMap.ContainsKey($k) -and $pkSrcMap[$k].Count -gt 1) {
      $missingMethod = 'no_evaluado_pk_compuesta'
    } elseif ($spiralCount -ne $null -and $spiralCount -gt 50000) {
      $missingMethod = 'no_evaluado_limite_filas'
    } else {
      $missingMethod = 'no_evaluable'
    }

    $aliasStatus = if (-not $targetExists) {
      'TARGET_FALTANTE'
    } elseif ($targetType -eq 'BASE TABLE') {
      'TABLA_FISICA'
    } elseif ($targetType -eq 'VIEW') {
      'VIEW_ALIAS_COMPATIBLE'
    } else {
      'TIPO_NO_ESPERADO'
    }

    $estadoGeneral = 'OK'
    if (-not $spiralExists) { $estadoGeneral = 'ERROR' }
    if (-not $targetExists) { $estadoGeneral = 'BLOCKER' }
    if ($pkStatus -in @('PK_FALTANTE_TARGET','PK_DIFERENTE')) { if ($estadoGeneral -eq 'OK') { $estadoGeneral = 'WARNING' } }
    if ($missingKeys -is [int] -and $missingKeys -gt 0) { $estadoGeneral = 'ERROR' }
    if ($spiralCount -is [long] -and $targetCount -is [long] -and $spiralCount -gt 0 -and $targetCount -eq 0) { $estadoGeneral = 'BLOCKER' }

    $obs = @()
    if ($classification -eq 'PRODUCTIVA_OBLIGATORIA_POR_INVENTARIO' -or $classification -eq 'LOCAL_SIN_EVIDENCIA_DE_SYNC') {
      $obs += 'No hay trigger sync explicito; se exige carga por inventario productivo.'
    }
    if ($aliasStatus -eq 'VIEW_ALIAS_COMPATIBLE') { $obs += 'Resuelto como VIEW alias en target (compatible por nombre logico).' }

    $rowsOut.Add([pscustomobject]@{
      objeto_sql = $logical
      clasificacion_procedencia = $classification
      trigger_sync_detectado = $hasSyncTrigger
      trigger_cualquier_tipo = $hasAnyTrigger
      triggers_detectados = if ($tr -ne $null) { ($tr.names -join ' | ') } else { '' }
      inventario_productivo = 'SI'
      spiral_existe = $spiralExists
      spiral_tipo = $spiralType
      spiral_objeto_resuelto = $spiralName
      encinitas_test_existe = $targetExists
      encinitas_test_tipo = $targetType
      encinitas_test_objeto_resuelto = $targetName
      alias_estado = $aliasStatus
      spiral_count = $spiralCount
      encinitas_test_count = $targetCount
      diferencia_count_spiral_menos_target = if (($spiralCount -is [long]) -and ($targetCount -is [long])) { [long]($spiralCount - $targetCount) } else { $null }
      pk_spiral = $pkSrc
      pk_encinitas_test = $pkTgt
      estado_pk = $pkStatus
      claves_faltantes_aprox = $missingKeys
      metodo_claves_faltantes = $missingMethod
      estado_general = $estadoGeneral
      observaciones = ($obs -join ' ')
    }) | Out-Null
  }

  $rows = $rowsOut | Sort-Object objeto_sql
  $rows | Export-Csv -Path $OutCsv -NoTypeInformation -Encoding UTF8

  $summaryByClass = @($rows | Group-Object clasificacion_procedencia | Sort-Object Name)
  $summaryByState = @($rows | Group-Object estado_general | Sort-Object Name)
  $missingTarget = @($rows | Where-Object { -not $_.encinitas_test_existe })
  $pkIssues = @($rows | Where-Object { $_.estado_pk -in @('PK_FALTANTE_TARGET','PK_DIFERENTE') })

  $md = New-Object System.Text.StringBuilder
  [void]$md.AppendLine('# Manifiesto de Procedencia por Tabla (Spiral -> Encinitas Test)')
  [void]$md.AppendLine('')
  [void]$md.AppendLine('- Fecha auditoria: ' + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'))
  [void]$md.AppendLine('- Fuente triggers: `spiral_triggers_inventario.CSV`, `spiral_triggers_encinitas.CSV`')
  [void]$md.AppendLine('- Cruce de inventario productivo: `docs/migracion/produccion_operativa_spiral.csv`')
  [void]$md.AppendLine('- Objetos productivos unicos relevados: ' + $objects59.Count)
  [void]$md.AppendLine('- Tablas productivas auditadas: ' + $rows.Count)
  [void]$md.AppendLine('')

  [void]$md.AppendLine('## Clasificacion de procedencia')
  foreach ($g in $summaryByClass) {
    [void]$md.AppendLine('- ' + $g.Name + ': ' + $g.Count)
  }
  [void]$md.AppendLine('')

  [void]$md.AppendLine('## Estado general de validacion')
  foreach ($g in $summaryByState) {
    [void]$md.AppendLine('- ' + $g.Name + ': ' + $g.Count)
  }
  [void]$md.AppendLine('')

  [void]$md.AppendLine('## Hallazgos criticos')
  if ($missingTarget.Count -eq 0) {
    [void]$md.AppendLine('- Sin tablas faltantes en `encinitas_test` para el set productivo auditado.')
  } else {
    [void]$md.AppendLine('- Tablas faltantes en `encinitas_test` (' + $missingTarget.Count + '):')
    foreach ($r in $missingTarget) {
      [void]$md.AppendLine('  - ' + $r.objeto_sql + ' [' + $r.clasificacion_procedencia + ']')
    }
  }

  if ($pkIssues.Count -eq 0) {
    [void]$md.AppendLine('- Sin diferencias de PK detectadas entre `spiral` y `encinitas_test` en tablas presentes.')
  } else {
    [void]$md.AppendLine('- Diferencias de PK detectadas (' + $pkIssues.Count + '):')
    foreach ($r in $pkIssues) {
      [void]$md.AppendLine('  - ' + $r.objeto_sql + ': ' + $r.estado_pk + ' (spiral=' + $r.pk_spiral + '; target=' + $r.pk_encinitas_test + ')')
    }
  }

  [void]$md.AppendLine('')
  [void]$md.AppendLine('## Regla aplicada para no asumir cobertura por trigger')
  [void]$md.AppendLine('- Si una tabla productiva no tiene trigger sync explicito, se clasifica `PRODUCTIVA_OBLIGATORIA_POR_INVENTARIO`.')
  [void]$md.AppendLine('- Si existe solo en target sin evidencia de sync desde Spiral, se clasifica `LOCAL_SIN_EVIDENCIA_DE_SYNC`.')
  [void]$md.AppendLine('- Las validaciones de tipo permiten `VIEW` en target como alias compatible por nombre logico.')
  [void]$md.AppendLine('')
  [void]$md.AppendLine('## Artefactos')
  [void]$md.AppendLine('- Detalle completo CSV: `docs/migracion/manifiesto_procedencia_tablas_spiral.csv`')

  [System.IO.File]::WriteAllText($OutMd, $md.ToString(), [System.Text.UTF8Encoding]::new($false))

  Write-Output "OK: $OutCsv"
  Write-Output "OK: $OutMd"
  Write-Output ("Resumen clases: " + (($summaryByClass | ForEach-Object { $_.Name + '=' + $_.Count }) -join ', '))
  Write-Output ("Resumen estado: " + (($summaryByState | ForEach-Object { $_.Name + '=' + $_.Count }) -join ', '))
}
finally {
  if ($null -ne $spiralConn) { $spiralConn.Close(); $spiralConn.Dispose() }
  if ($null -ne $targetConn) { $targetConn.Close(); $targetConn.Dispose() }
}
