$jmxPath = "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance\RFD_Transaksi_Spike_Test.jmx"
$content = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)

# Cari dan tampilkan ThreadGroup LOCAL saat ini
$match = [regex]::Match($content, 'TC-SPK-01[^"]*"\s+enabled="[^"]*"')
Write-Host "Sebelum:"
Write-Host $match.Value

# Update TC-SPK-01 LOCAL: 500 -> 100 user (lebih realistis untuk artisan serve)
# Ganti num_threads dari 500 ke 100
$oldLocal = @'
      <ThreadGroup guiclass="ThreadGroupGui" testclass="ThreadGroup"
                   testname="TC-SPK-01 - LOCAL (500 User, Ramp-up 5s, Loop 1)"
                   enabled="true">
        <stringProp name="ThreadGroup.on_sample_error">continue</stringProp>
        <elementProp name="ThreadGroup.main_controller" elementType="LoopController"
                     guiclass="LoopControlPanel" testclass="LoopController" enabled="true">
          <boolProp name="LoopController.continue_forever">false</boolProp>
          <stringProp name="LoopController.loops">1</stringProp>
        </elementProp>
        <stringProp name="ThreadGroup.num_threads">500</stringProp>
        <stringProp name="ThreadGroup.ramp_time">5</stringProp>
'@

$newLocal = @'
      <ThreadGroup guiclass="ThreadGroupGui" testclass="ThreadGroup"
                   testname="TC-SPK-01 - LOCAL (100 User, Ramp-up 5s, Loop 1)"
                   enabled="true">
        <stringProp name="ThreadGroup.on_sample_error">continue</stringProp>
        <elementProp name="ThreadGroup.main_controller" elementType="LoopController"
                     guiclass="LoopControlPanel" testclass="LoopController" enabled="true">
          <boolProp name="LoopController.continue_forever">false</boolProp>
          <stringProp name="LoopController.loops">1</stringProp>
        </elementProp>
        <stringProp name="ThreadGroup.num_threads">100</stringProp>
        <stringProp name="ThreadGroup.ramp_time">5</stringProp>
'@

if ($content.Contains('TC-SPK-01 - LOCAL (500 User')) {
    $content = $content.Replace($oldLocal, $newLocal)
    Write-Host "TC-SPK-01 diupdate: 500 -> 100 user"
} else {
    # Fallback: ganti angka langsung
    $content = $content -replace '(<stringProp name="ThreadGroup\.num_threads">)500(</stringProp>)', '${1}100${2}'
    Write-Host "Fallback: ganti num_threads 500 -> 100"
}

[System.IO.File]::WriteAllText($jmxPath, $content, [System.Text.Encoding]::UTF8)

# Verifikasi
$v = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)
$has100 = $v.Contains('<stringProp name="ThreadGroup.num_threads">100</stringProp>')
$has500 = $v.Contains('<stringProp name="ThreadGroup.num_threads">500</stringProp>')
Write-Host "num_threads=100 ada: $has100"
Write-Host "num_threads=500 sisa: $has500"
Write-Host "SELESAI"
