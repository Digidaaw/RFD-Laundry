$jmxPath = "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance\RFD_Transaksi_Local_Deploy_Test.jmx"
$content = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)

# Enable TC-TRX-01 (LOCAL), Disable TC-TRX-02 (DEPLOY)
# TC-TRX-01 biasanya enabled="false", ganti jadi true
# TC-TRX-02 biasanya enabled="true", ganti jadi false

# Cari dan tampilkan ThreadGroup yang ada
$tg1 = [regex]::Matches($content, 'ThreadGroup[^>]+testname="([^"]*)"[^>]+enabled="([^"]*)"')
Write-Host "=== ThreadGroups ditemukan ==="
foreach ($m in $tg1) {
    Write-Host "  Name: $($m.Groups[1].Value) | Enabled: $($m.Groups[2].Value)"
}

# Enable TC-TRX-01, Disable TC-TRX-02
$content = $content -replace '(ThreadGroup[^>]+TC-TRX-01[^>]+enabled=)"false"', '$1"true"'
$content = $content -replace '(ThreadGroup[^>]+TC-TRX-02[^>]+enabled=)"true"', '$1"false"'

[System.IO.File]::WriteAllText($jmxPath, $content, [System.Text.Encoding]::UTF8)

# Verifikasi
$v = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)
$tg2 = [regex]::Matches($v, 'ThreadGroup[^>]+testname="([^"]*)"[^>]+enabled="([^"]*)"')
Write-Host ""
Write-Host "=== Setelah update ==="
foreach ($m in $tg2) {
    Write-Host "  Name: $($m.Groups[1].Value) | Enabled: $($m.Groups[2].Value)"
}

Write-Host ""
Write-Host "SELESAI. Buka ulang JMX di JMeter lalu Run."
