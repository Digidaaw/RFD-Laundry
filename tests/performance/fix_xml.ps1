$jmxPath = "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance\RFD_Transaksi_Local_Deploy_Test.jmx"
$content = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)

# Fix semua HtmlExtractor yang masih punya opening tag HtmlExtractor tapi closing RegexExtractor
# Langkah: cari pola <HtmlExtractor ... > ... </RegexExtractor> dan jadikan full RegexExtractor

# Ganti opening tag yang masih HtmlExtractor
$content = $content -replace '<HtmlExtractor guiclass="HtmlExtractorGui" testclass="HtmlExtractor"([^>]*)>', '<RegexExtractor guiclass="RegexExtractorGui" testclass="RegexExtractor"$1>'

# Pastikan semua closing tag konsisten
$content = $content.Replace('</HtmlExtractor>', '</RegexExtractor>')

# Bersihkan properti HtmlExtractor yang mungkin masih tersisa
$content = $content.Replace('<stringProp name="HtmlExtractor.refname">', '<stringProp name="RegexExtractor.refname">')
$content = $content.Replace('<stringProp name="HtmlExtractor.expr">', '<stringProp name="RegexExtractor.regex">')
$content = $content.Replace('<stringProp name="HtmlExtractor.attribute">', '<stringProp name="RegexExtractor.template">')
$content = $content.Replace('<stringProp name="HtmlExtractor.match_no">', '<stringProp name="RegexExtractor.match_number">')
$content = $content.Replace('<stringProp name="HtmlExtractor.default">', '<stringProp name="RegexExtractor.default">')
$content = $content.Replace('<boolProp name="HtmlExtractor.useEmptyDefaultValue">', '<boolProp name="RegexExtractor.default_empty_value">')
$content = $content.Replace('<boolProp name="HtmlExtractor.default_empty_value">', '<boolProp name="RegexExtractor.default_empty_value">')
$content = $content.Replace('<stringProp name="HtmlExtractor.match_number">', '<stringProp name="RegexExtractor.match_number">')
$content = $content.Replace('<stringProp name="HtmlExtractor.extractor_impl"></stringProp>', '')

[System.IO.File]::WriteAllText($jmxPath, $content, [System.Text.Encoding]::UTF8)

# Verifikasi
$v = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)
$htmlRemain = [System.Text.RegularExpressions.Regex]::Matches($v, '<HtmlExtractor').Count
$regexOK    = [System.Text.RegularExpressions.Regex]::Matches($v, '<RegexExtractor').Count
$mismatch   = [System.Text.RegularExpressions.Regex]::Matches($v, '</RegexExtractor>').Count

Write-Host "=== VERIFIKASI ==="
Write-Host "<HtmlExtractor sisa  : $htmlRemain  (harus 0)"
Write-Host "<RegexExtractor ada  : $regexOK"
Write-Host "</RegexExtractor>    : $mismatch   (harus sama dengan RegexExtractor)"
Write-Host "wssplashchk ada      : $($v.Contains('wssplashchk'))"
Write-Host "SELESAI - silakan buka ulang di JMeter"
