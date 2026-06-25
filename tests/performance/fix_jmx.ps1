$jmxPath = "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance\RFD_Transaksi_Local_Deploy_Test.jmx"
$wssValue = "3529613bcfa9c87f06e0889d2f37efc03b7c48aa.1781257098.1"

$content = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)
Write-Host "File dibaca: $($content.Length) bytes"

# === FIX 1: Ganti HtmlExtractor dengan RegexExtractor ===
$htmlOld = '<HtmlExtractor guiclass="HtmlExtractorGui" testclass="HtmlExtractor" testname="Ekstrak CSRF Token" enabled="true">'
$regexNew = '<RegexExtractor guiclass="RegexExtractorGui" testclass="RegexExtractor" testname="[FIX] Ekstrak CSRF Token via Regex" enabled="true">'

$content = $content.Replace($htmlOld, $regexNew)
$content = $content.Replace('<stringProp name="HtmlExtractor.refname">csrf_token</stringProp>', '<stringProp name="RegexExtractor.useHeaders">false</stringProp><stringProp name="RegexExtractor.refname">csrf_token</stringProp>')
$content = $content.Replace('<stringProp name="HtmlExtractor.expr">input[name=_token]</stringProp>', '<stringProp name="RegexExtractor.regex">name="_token" value="([^"]+)"</stringProp>')
$content = $content.Replace('<stringProp name="HtmlExtractor.attribute">value</stringProp>', '<stringProp name="RegexExtractor.template">$1</stringProp>')
$content = $content.Replace('<stringProp name="HtmlExtractor.match_no">1</stringProp>', '<stringProp name="RegexExtractor.match_number">1</stringProp>')
$content = $content.Replace('<stringProp name="HtmlExtractor.default">TOKEN_NOT_FOUND</stringProp>', '<stringProp name="RegexExtractor.default">TOKEN_NOT_FOUND</stringProp>')
$content = $content.Replace('<boolProp name="HtmlExtractor.useEmptyDefaultValue">false</boolProp>', '<boolProp name="RegexExtractor.default_empty_value">false</boolProp>')
$content = $content.Replace('<boolProp name="HtmlExtractor.default_empty_value">false</boolProp>', '<stringProp name="RegexExtractor.scope">body</stringProp>')
$content = $content.Replace('<stringProp name="HtmlExtractor.match_number"></stringProp>', '')
$content = $content.Replace('<stringProp name="HtmlExtractor.extractor_impl"></stringProp>', '')
$content = $content.Replace('</HtmlExtractor>', '</RegexExtractor>')

Write-Host "RegexExtractor ada: $($content.Contains('RegexExtractor'))"
Write-Host "HtmlExtractor.refname sisa: $(($content -split 'HtmlExtractor.refname').Count - 1)"

# === FIX 2: Inject cookie wssplashchk ke Cookie Manager TC-TRX-02 ===
$oldCookieMgr = '<CookieManager guiclass="CookiePanel" testclass="CookieManager" testname="[TC-TRX-02] Cookie Manager DEPLOY" enabled="true">
          <collectionProp name="CookieManager.cookies"/>
          <boolProp name="CookieManager.clearEachIteration">true</boolProp>
          <boolProp name="CookieManager.controlledByThreadGroup">false</boolProp>
        </CookieManager>'

$newCookieMgr = '<CookieManager guiclass="CookiePanel" testclass="CookieManager" testname="[TC-TRX-02] Cookie Manager DEPLOY (wssplashchk bypass)" enabled="true">
          <collectionProp name="CookieManager.cookies">
            <elementProp name="wssplashchk" elementType="Cookie" testname="wssplashchk">
              <stringProp name="Cookie.value">' + $wssValue + '</stringProp>
              <stringProp name="Cookie.domain">laundry.aksaralearning.com</stringProp>
              <stringProp name="Cookie.path">/</stringProp>
              <boolProp name="Cookie.secure">true</boolProp>
              <longProp name="Cookie.expires">0</longProp>
              <boolProp name="Cookie.path_specified">true</boolProp>
              <boolProp name="Cookie.domain_specified">true</boolProp>
            </elementProp>
          </collectionProp>
          <boolProp name="CookieManager.clearEachIteration">false</boolProp>
          <boolProp name="CookieManager.controlledByThreadGroup">false</boolProp>
        </CookieManager>'

if ($content.Contains('Cookie Manager DEPLOY')) {
    $content = $content.Replace($oldCookieMgr, $newCookieMgr)
    Write-Host "Cookie Manager TC-TRX-02 ditemukan dan diupdate"
} else {
    Write-Host "PERINGATAN: Cookie Manager TC-TRX-02 tidak ditemukan di file"
    # Cek nama cookie manager yang ada
    $idx = $content.IndexOf('CookieManager')
    if ($idx -ge 0) {
        Write-Host "CookieManager ditemukan di index $idx"
        Write-Host $content.Substring($idx, 200)
    }
}

# === SIMPAN ===
[System.IO.File]::WriteAllText($jmxPath, $content, [System.Text.Encoding]::UTF8)

# === VERIFIKASI AKHIR ===
$v = [System.IO.File]::ReadAllText($jmxPath, [System.Text.Encoding]::UTF8)
Write-Host ""
Write-Host "======= VERIFIKASI AKHIR ======="
Write-Host "wssplashchk     : $($v.Contains('wssplashchk'))"
Write-Host "RegexExtractor  : $($v.Contains('RegexExtractor'))"
Write-Host "HtmlExtractor.refname sisa: $(($v -split 'HtmlExtractor.refname').Count - 1)"
Write-Host "Cookie value OK : $($v.Contains($wssValue))"
Write-Host "================================"
