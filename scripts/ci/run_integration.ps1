# Local CI runner for integration audit scripts (PowerShell)
# Mirrors the CI job so the integration scripts can be run locally on Windows.

param(
    [switch]$RunFullAudit = $true
)

Write-Host "Starting local integration runner"

# Check PHP
$php = Get-Command php -ErrorAction SilentlyContinue
if (-not $php) {
    Write-Error "php not found in PATH. Install PHP 8.2+ or adjust PATH."
    exit 2
}

$serverStartedByScript = $false
$serverProcess = $null

function Test-PortOpen {
    param($HostName='127.0.0.1', $PortNum=8000)
    try {
        return Test-NetConnection -ComputerName $HostName -Port $PortNum -InformationLevel Quiet
    } catch {
        return $false
    }
}

if (Test-PortOpen -HostName '127.0.0.1' -PortNum 8000) {
    Write-Host "Port 8000 already open; assuming Laravel server is running."
} else {
    Write-Host "Port 8000 not open. Preparing environment and starting artisan serve..."

    # Ensure .env exists
    if (-not (Test-Path .env)) {
        if (Test-Path .env.example) {
            Copy-Item .env.example .env
            Write-Host "Copied .env.example -> .env"
        } else {
            Write-Warning ".env.example not found. Continuing without copying."
        }
    } else {
        Write-Host ".env found"
    }

    # Ensure database sqlite exists
    if (-not (Test-Path database)) { New-Item -ItemType Directory -Path database | Out-Null }
    $sqlitePath = "database\database.sqlite"
    if (-not (Test-Path $sqlitePath)) {
        New-Item -ItemType File -Path $sqlitePath | Out-Null
        Write-Host "Created $sqlitePath"
    }

    Write-Host "Running migrations (may be no-op)"
    & php artisan migrate --force

    Write-Host "Starting artisan serve on 127.0.0.1:8000"
    $arg = "artisan serve --host=127.0.0.1 --port=8000"
    $serverProcess = Start-Process -FilePath php -ArgumentList $arg -NoNewWindow -PassThru
    $serverStartedByScript = $true

    # Wait for port
    $waited = 0
    while (-not (Test-PortOpen -HostName '127.0.0.1' -PortNum 8000) -and $waited -lt 30) {
        Start-Sleep -Seconds 1
        $waited += 1
    }
    if (-not (Test-PortOpen -HostName '127.0.0.1' -PortNum 8000)) {
        Write-Error "Laravel server did not become available on port 8000. Check logs and ensure php is configured correctly."
        if ($serverProcess) { $serverProcess | Stop-Process -Force -ErrorAction SilentlyContinue }
        exit 3
    }
    Write-Host "Laravel server is listening on port 8000"
}

# Run the audit scripts
$allPassed = $true

if ($RunFullAudit) {
    Write-Host "Running scripts/integration_full_audit.php"
    & php scripts\integration_full_audit.php
    $exit1 = $LASTEXITCODE
    if ($exit1 -ne 0) {
        Write-Error "integration_full_audit.php exited with code $exit1"
        $allPassed = $false
    } else { Write-Host "integration_full_audit.php passed" }
}

Write-Host "Running scripts/integration_sanctum_flow.php"
& php scripts\integration_sanctum_flow.php
$exit2 = $LASTEXITCODE
if ($exit2 -ne 0) {
    Write-Error "integration_sanctum_flow.php exited with code $exit2"
    $allPassed = $false
} else { Write-Host "integration_sanctum_flow.php passed" }

# Cleanup
if ($serverStartedByScript -and $serverProcess) {
    try {
        Write-Host "Stopping artisan server (PID $($serverProcess.Id))"
        Stop-Process -Id $serverProcess.Id -Force -ErrorAction SilentlyContinue
    } catch {
        Write-Warning "Failed to stop server process: $_"
    }
}

if ($allPassed) { Write-Host "All integration scripts passed"; exit 0 } else { Write-Error "One or more integration scripts failed"; exit 4 }
