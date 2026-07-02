param(
    [string]$Host = '192.168.2.210',
    [int]$Port = 3306,
    [ValidateSet('Auto','x86','x64')]
    [string]$AccessArchitecture = 'Auto'
)

$ErrorActionPreference = 'Stop'

function Get-MysqlOdbcDrivers {
    $drivers = [ordered]@{
        x64 = @()
        x86 = @()
    }

    $paths = @(
        @{ Arch = 'x64'; Path = 'HKLM:\SOFTWARE\ODBC\ODBCINST.INI\ODBC Drivers' },
        @{ Arch = 'x86'; Path = 'HKLM:\SOFTWARE\WOW6432Node\ODBC\ODBCINST.INI\ODBC Drivers' }
    )

    foreach ($entry in $paths) {
        if (Test-Path $entry.Path) {
            $props = Get-ItemProperty -Path $entry.Path
            $names = $props.PSObject.Properties |
                Where-Object {
                    $_.Name -notlike 'PS*' -and
                    $_.Name -match 'MySQL' -and
                    ($_.Value -eq 'Installed' -or $_.Value -eq 'installed')
                } |
                Select-Object -ExpandProperty Name
            $drivers[$entry.Arch] = @($names)
        }
    }

    return $drivers
}

function Get-AccessArchitectureHint {
    $clickToRun = 'HKLM:\SOFTWARE\Microsoft\Office\ClickToRun\Configuration'
    if (Test-Path $clickToRun) {
        $platform = (Get-ItemProperty -Path $clickToRun -ErrorAction SilentlyContinue).Platform
        if ($platform -eq 'x86' -or $platform -eq 'x64') {
            return $platform
        }
    }

    $officePath64 = 'HKLM:\SOFTWARE\Microsoft\Office\16.0\Access\InstallRoot'
    $officePath32 = 'HKLM:\SOFTWARE\WOW6432Node\Microsoft\Office\16.0\Access\InstallRoot'

    if (Test-Path $officePath64) { return 'x64' }
    if (Test-Path $officePath32) { return 'x86' }

    return 'unknown'
}

Write-Host '== KOI ODBC preflight (read-only) ==' -ForegroundColor Cyan
Write-Host "Host: $Host"
Write-Host "Port: $Port"
Write-Host "Access architecture input: $AccessArchitecture"
Write-Host ''

Write-Host '1) Network reachability (Test-NetConnection)' -ForegroundColor Yellow
$tnc = Test-NetConnection -ComputerName $Host -Port $Port -WarningAction SilentlyContinue
$tnc | Select-Object ComputerName, RemotePort, NameResolutionSucceeded, TcpTestSucceeded | Format-Table -AutoSize
Write-Host ''

Write-Host '2) Installed MySQL ODBC drivers' -ForegroundColor Yellow
$drivers = Get-MysqlOdbcDrivers
Write-Host "x64 drivers: $((@($drivers.x64) -join ', '))"
Write-Host "x86 drivers: $((@($drivers.x86) -join ', '))"
if (-not $drivers.x64 -and -not $drivers.x86) {
    Write-Warning 'No MySQL ODBC driver found in common x64/x86 locations.'
}
Write-Host ''

Write-Host '3) ODBC administrator recommendation for Access' -ForegroundColor Yellow
$detected = Get-AccessArchitectureHint
$effective = if ($AccessArchitecture -eq 'Auto') { $detected } else { $AccessArchitecture }

switch ($effective) {
    'x86' {
        Write-Host 'Use 32-bit ODBC Administrator:' -ForegroundColor Green
        Write-Host 'C:\Windows\SysWOW64\odbcad32.exe'
    }
    'x64' {
        Write-Host 'Use 64-bit ODBC Administrator:' -ForegroundColor Green
        Write-Host 'C:\Windows\System32\odbcad32.exe'
    }
    default {
        Write-Warning 'Access architecture could not be determined. Validate Office/Access bitness first.'
        Write-Host 'x86 admin: C:\Windows\SysWOW64\odbcad32.exe'
        Write-Host 'x64 admin: C:\Windows\System32\odbcad32.exe'
    }
}

Write-Host ''
Write-Host 'Guardrails:' -ForegroundColor Yellow
Write-Host '- This script does not create DSN.'
Write-Host '- This script does not request or store passwords.'
Write-Host '- This script does not open authenticated MySQL sessions.'
Write-Host '- This script does not modify Windows Registry.'
