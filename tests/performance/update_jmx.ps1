param(
    [string]$WssValue = "PASTE_WSSPLASHCHK_HERE"
)

$jmxPath = Join-Path $PSScriptRoot "RFD_Transaksi_Local_Deploy_Test.jmx"
$content  = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)

# 1. Ganti HtmlExtractor dengan RegexExtractor (semua kemunculan di TC-TRX-02 area)
$htmlPattern = @'
          <HtmlExtractor guiclass="HtmlExtractorGui" testclass="HtmlExtractor" testname="Ekstrak CSRF Token" enabled="true">
            <stringProp name="HtmlExtractor.refname">csrf_token</stringProp>
            <stringProp name="HtmlExtractor.expr">input[name=_token]</stringProp>
            <stringProp name="HtmlExtractor.attribute">value</stringProp>
            <stringProp name="HtmlExtractor.match_no">1</stringProp>
            <stringProp name="HtmlExtractor.default">TOKEN_NOT_FOUND</stringProp>
            <boolProp name="HtmlExtractor.useEmptyDefaultValue">false</boolProp>
            <boolProp name="HtmlExtractor.default_empty_value">false</boolProp>
            <stringProp name="HtmlExtractor.match_number"></stringProp>
            <stringProp name="HtmlExtractor.extractor_impl"></stringProp>
          </HtmlExtractor>
'@

$regexReplacement = @'
          <RegexExtractor guiclass="RegexExtractorGui" testclass="RegexExtractor" testname="[FIX] Ekstrak CSRF Token via Regex" enabled="true">
            <stringProp name="RegexExtractor.useHeaders">false</stringProp>
            <stringProp name="RegexExtractor.refname">csrf_token</stringProp>
            <stringProp name="RegexExtractor.regex">name="_token" value="([^"]+)"</stringProp>
            <stringProp name="RegexExtractor.template">$1</stringProp>
            <stringProp name="RegexExtractor.default">TOKEN_NOT_FOUND</stringProp>
            <stringProp name="RegexExtractor.match_number">1</stringProp>
            <boolProp name="RegexExtractor.default_empty_value">false</boolProp>
            <stringProp name="RegexExtractor.scope">body</stringProp>
          </RegexExtractor>
'@

$count = ([regex]::Matches($content, [regex]::Escape($htmlPattern.Trim()))).Count
Write-Host "HtmlExtractor ditemukan: $count kali"
$content = $content.Replace($htmlPattern, $regexReplacement)

# 2. Ganti PASTE_WSSPLASHCHK_HERE dengan nilai aktual
if ($WssValue -ne "PASTE_WSSPLASHCHK_HERE") {
    $content = $content.Replace("PASTE_WSSPLASHCHK_HERE", $WssValue)
    Write-Host "wssplashchk value diset: $($WssValue.Substring(0,[Math]::Min(20,$WssValue.Length)))..."
}

# 3. Simpan
[System.IO.File]::WriteAllText($jmxPath, $content, [System.Text.Encoding]::UTF8)

# 4. Verifikasi
$verify = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)
Write-Host ""
Write-Host "=== HASIL VERIFIKASI ==="
Write-Host "RegexExtractor ada : $($verify.Contains('RegexExtractor'))"
Write-Host "HtmlExtractor sisa : $(([regex]::Matches($verify,'HtmlExtractor')).Count) (0 = sudah bersih)"
Write-Host "wssplashchk ada    : $($verify.Contains('wssplashchk'))"
Write-Host "Placeholder tersisa: $($verify.Contains('PASTE_WSSPLASHCHK_HERE'))"
Write-Host ""
Write-Host "SELESAI. Tutup JMeter -> buka ulang JMX -> Run TC-TRX-02."
