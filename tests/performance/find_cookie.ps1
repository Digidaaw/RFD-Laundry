$jmxPath = "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance\RFD_Transaksi_Local_Deploy_Test.jmx"
$wssValue = "3529613bcfa9c87f06e0889d2f37efc03b7c48aa.1781257098.1"

$content = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)

# Cari posisi TC-TRX-02 thread group
$idxTG02 = $content.IndexOf('TC-TRX-02')
Write-Host "TC-TRX-02 ditemukan di index: $idxTG02"

# Cari CookieManager yang ada SETELAH TC-TRX-02
$idxCM = $content.IndexOf('CookieManager', $idxTG02)
Write-Host "CookieManager setelah TC-TRX-02 di index: $idxCM"

if ($idxCM -gt 0) {
    Write-Host "Potongan teks sekitar CookieManager:"
    Write-Host $content.Substring($idxCM - 20, 400)
} else {
    Write-Host "TIDAK ADA CookieManager setelah TC-TRX-02 - perlu ditambahkan"
    
    # Cari hashTree pertama setelah ThreadGroup TC-TRX-02
    $idxHashTree = $content.IndexOf('<hashTree>', $idxTG02)
    Write-Host "hashTree setelah TC-TRX-02 di index: $idxHashTree"
    if ($idxHashTree -gt 0) {
        Write-Host "Context:"
        Write-Host $content.Substring($idxHashTree - 50, 200)
    }
}
