# ============================================================
#  RFD Laundry Management System - Start Script
#  Menjalankan Laravel dev server + Vite secara bersamaan
# ============================================================

$Host.UI.RawUI.WindowTitle = "RFD Laundry - Dev Server"

# Warna output
function Write-Header($msg) {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "  $msg" -ForegroundColor Cyan
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
}

function Write-Success($msg) { Write-Host "[OK] $msg" -ForegroundColor Green }
function Write-Info($msg)    { Write-Host "[..] $msg" -ForegroundColor Yellow }
function Write-Err($msg)     { Write-Host "[!!] $msg" -ForegroundColor Red }

# ─── Tentukan direktori project ───────────────────────────
$ProjectDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
Set-Location $ProjectDir

Write-Header "RFD Laundry Management System"
Write-Info "Direktori project : $ProjectDir"

# ─── Cek PHP ──────────────────────────────────────────────
Write-Info "Memeriksa PHP..."
$phpVersion = php -v 2>&1 | Select-String "PHP \d"
if (-not $phpVersion) {
    Write-Err "PHP tidak ditemukan! Pastikan PHP sudah ada di PATH."
    Pause; exit 1
}
Write-Success "PHP ditemukan : $($phpVersion.ToString().Trim())"

# ─── Cek Node / NPM ───────────────────────────────────────
Write-Info "Memeriksa Node.js / NPM..."
$nodeVersion = node -v 2>&1
$npmVersion  = npm -v  2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Err "Node.js / NPM tidak ditemukan! Pastikan Node.js sudah diinstall."
    Pause; exit 1
}
Write-Success "Node  : $nodeVersion   NPM : $npmVersion"

# ─── Cek vendor/ (composer) ───────────────────────────────
if (-not (Test-Path "$ProjectDir\vendor")) {
    Write-Info "Folder vendor tidak ditemukan. Menjalankan composer install..."
    composer install
    if ($LASTEXITCODE -ne 0) {
        Write-Err "composer install gagal!"
        Pause; exit 1
    }
    Write-Success "composer install selesai."
}

# ─── Cek node_modules/ (npm) ──────────────────────────────
if (-not (Test-Path "$ProjectDir\node_modules")) {
    Write-Info "Folder node_modules tidak ditemukan. Menjalankan npm install..."
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Err "npm install gagal!"
        Pause; exit 1
    }
    Write-Success "npm install selesai."
}

# ─── Generate APP_KEY jika belum ada ──────────────────────
$envContent = Get-Content "$ProjectDir\.env" -Raw
if ($envContent -match "APP_KEY=\s*$" -or $envContent -match "APP_KEY=$") {
    Write-Info "APP_KEY kosong. Menjalankan php artisan key:generate..."
    php artisan key:generate
}

# ─── Buat storage link jika belum ada ─────────────────────
if (-not (Test-Path "$ProjectDir\public\storage")) {
    Write-Info "Membuat storage link..."
    php artisan storage:link
}

# ─── Jalankan Laravel server di jendela baru ──────────────
Write-Info "Memulai Laravel development server (port 8000)..."
$laravelJob = Start-Process powershell -ArgumentList `
    "-NoExit", "-Command", `
    "Set-Location '$ProjectDir'; `$Host.UI.RawUI.WindowTitle = 'RFD Laundry - Laravel'; php artisan serve" `
    -PassThru

Write-Success "Laravel server berjalan  -> http://127.0.0.1:8000"

# ─── Jalankan Vite di jendela baru ────────────────────────
Write-Info "Memulai Vite dev server..."
$viteJob = Start-Process powershell -ArgumentList `
    "-NoExit", "-Command", `
    "Set-Location '$ProjectDir'; `$Host.UI.RawUI.WindowTitle = 'RFD Laundry - Vite'; npm run dev" `
    -PassThru

Write-Success "Vite server berjalan     -> http://localhost:5173"

Write-Host ""
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  Semua server sudah berjalan!" -ForegroundColor Magenta
Write-Host ""
Write-Host "  Aplikasi  : http://127.0.0.1:8000" -ForegroundColor White
Write-Host "  Vite HMR  : http://localhost:5173" -ForegroundColor White
Write-Host ""
Write-Host "  Tekan ENTER untuk menghentikan semua server" -ForegroundColor DarkGray
Write-Host "============================================" -ForegroundColor Magenta
Write-Host ""

# Tunggu input user, lalu stop semua proses
Read-Host | Out-Null

Write-Info "Menghentikan semua server..."
Stop-Process -Id $laravelJob.Id -ErrorAction SilentlyContinue
Stop-Process -Id $viteJob.Id   -ErrorAction SilentlyContinue
Write-Success "Semua server sudah dihentikan. Sampai jumpa!"
Start-Sleep -Seconds 1
