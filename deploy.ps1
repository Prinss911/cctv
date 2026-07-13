param(
    [string]$NasHost = "192.168.7.10",
    [string]$NasUser = "",         # SSH user NAS
    [string]$NasPath = "",         # Path project di NAS
    [switch]$PromptPassword        # Tampilkan prompt password
)

$ErrorActionPreference = "Stop"

# ── Validasi ──────────────────────────────────────────────
if (-not $NasUser) { $NasUser = Read-Host "SSH user NAS (contoh: root)" }
if (-not $NasPath) { $NasPath = Read-Host "Path project di NAS (contoh: /opt/cctv)" }

Write-Host ""
Write-Host "=== Deploy CCTV ke NAS ===" -ForegroundColor Cyan
Write-Host "Target : $NasUser@$NasHost`:$NasPath"
Write-Host "Branch : main"
Write-Host ""

# ── 1. Git push ───────────────────────────────────────────
Write-Host ">>> Push ke GitHub..." -ForegroundColor Yellow
git push origin main
if ($LASTEXITCODE -ne 0) { throw "Git push gagal" }

# ── 2. Cari SSH client ────────────────────────────────────
$sshClient = $null
$sshArgs = @()

# Coba plink (PuTTY)
$plink = (Get-Command "plink" -ErrorAction SilentlyContinue).Source
if ($plink) {
    $sshClient = $plink
    if ($PromptPassword -or -not [string]::IsNullOrEmpty($env:DEPLOY_SSH_PASS)) {
        $pass = $env:DEPLOY_SSH_PASS
        if (-not $pass) {
            $sec = Read-Host -Prompt "Password SSH $NasUser@$NasHost" -AsSecureString
            $bstr = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($sec)
            $pass = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($bstr)
            [System.Runtime.InteropServices.Marshal]::ZeroFreeBSTR($bstr)
        }
        $sshArgs = @("-ssh", "-batch", "-pw", $pass, "$NasUser@$NasHost")
    } else {
        Write-Host "! Set env DEPLOY_SSH_PASS atau pake -PromptPassword" -ForegroundColor Yellow
        $sshArgs = @("-ssh", "$NasUser@$NasHost")
    }
}
# Coba native ssh
else {
    $nativeSsh = (Get-Command "ssh" -ErrorAction SilentlyContinue).Source
    if (-not $nativeSsh) { throw "Tidak ada SSH client (install PuTTY atau OpenSSH)" }
    $sshClient = $nativeSsh
    $sshArgs = @("-t", "$NasUser@$NasHost")
    Write-Host "! Native SSH: koneksi manual (password diminta langsung)" -ForegroundColor Yellow
}

# ── 3. SSH commands ───────────────────────────────────────
$remoteCmds = @(
    "cd $NasPath",
    "echo '>>> Git pull...'",
    "git pull origin main",
    "echo '>>> Rebuild container...'",
    "docker compose up -d --build",
    "echo '>>> Done! Container status:'",
    "docker compose ps"
)

$cmdStr = [string]::Join(" && ", $remoteCmds)
$fullArgs = $sshArgs + @("--", $cmdStr)

Write-Host ">>> Eksekusi remote..." -ForegroundColor Yellow
Write-Host ""

try {
    & $sshClient @fullArgs
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "=== Deploy berhasil! ===" -ForegroundColor Green
    } else {
        throw "SSH exit code: $LASTEXITCODE"
    }
}
catch {
    Write-Host "=== Deploy gagal: $_ ===" -ForegroundColor Red
    Write-Host ""
    Write-Host "Manual: ssh $NasUser@$NasHost lalu jalankan:" -ForegroundColor Cyan
    Write-Host "  cd $NasPath && git pull origin main && docker compose up -d --build" -ForegroundColor White
    exit 1
}
