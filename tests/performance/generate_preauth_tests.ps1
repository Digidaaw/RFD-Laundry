param(
    [Parameter(Mandatory=$true)]
    [string]$LocalSession,
    [string]$DeploySession = "eyJpdiI6ImlDZEJFSWNha3FxZTc0WWc4YXBZZlE9PSIsInZhbHVlIjoiTVNJeXlpVUdzL2kzdVdxcStobmd2MlR6MjJ6Uk5lTTc5QjlFZ3V4TVN0dEUrNkY0L1VGQmhPRklzYVJvU1JWTkkwcWZTNGFldjlsVDkwUUJ3blRFemRyb1pRTys5N0x3SUdITTZOT1M2cXZVUFErcVdnSXJaMlRHVGxIb2JTM3ciLCJtYWMiOiI0NzI0ZWQwOTU5YzI2NzMyOWZiZDdhNjI0NTRmZDJiZTM0YjBlMjg3N2I2NzY4MDc4NWE2NGI2MjQyNmUxN2Q1IiwidGFnIjoiIn0=",
    [string]$WssValue     = "3529613bcfa9c87f06e0889d2f37efc03b7c48aa.1781257098.1"
)

$basePath = "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance"

function Get-TransaksiBlock {
    param([string]$prefix, [string]$thinkMs = "500")
    return @"
        <HTTPSamplerProxy guiclass="HttpTestSampleGui" testclass="HTTPSamplerProxy"
                          testname="[$prefix] GET /transaksi - Daftar Transaksi" enabled="true">
          <intProp name="HTTPSampler.connect_timeout">10000</intProp>
          <intProp name="HTTPSampler.response_timeout">30000</intProp>
          <stringProp name="HTTPSampler.path">/transaksi</stringProp>
          <boolProp name="HTTPSampler.follow_redirects">true</boolProp>
          <stringProp name="HTTPSampler.method">GET</stringProp>
          <boolProp name="HTTPSampler.use_keepalive">true</boolProp>
          <boolProp name="HTTPSampler.postBodyRaw">false</boolProp>
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments">
            <collectionProp name="Arguments.arguments"/>
          </elementProp>
        </HTTPSamplerProxy>
        <hashTree>
          <ResponseAssertion guiclass="AssertionGui" testclass="ResponseAssertion" testname="Assert HTTP 200" enabled="true">
            <collectionProp name="Asserion.test_strings"><stringProp name="49586">200</stringProp></collectionProp>
            <stringProp name="Assertion.custom_message">[$prefix] /transaksi gagal - session expired?</stringProp>
            <stringProp name="Assertion.test_field">Assertion.response_code</stringProp>
            <boolProp name="Assertion.assume_success">false</boolProp>
            <intProp name="Assertion.test_type">8</intProp>
          </ResponseAssertion>
          <hashTree/>
          <DurationAssertion guiclass="DurationAssertionGui" testclass="DurationAssertion" testname="Assert Response Time &lt; 5000ms" enabled="true">
            <stringProp name="DurationAssertion.duration">5000</stringProp>
          </DurationAssertion>
          <hashTree/>
          <ConstantTimer guiclass="ConstantTimerGui" testclass="ConstantTimer" testname="Think Time ${thinkMs}ms" enabled="true">
            <stringProp name="ConstantTimer.delay">$thinkMs</stringProp>
          </ConstantTimer>
          <hashTree/>
        </hashTree>
        <HTTPSamplerProxy guiclass="HttpTestSampleGui" testclass="HTTPSamplerProxy"
                          testname="[$prefix] GET /transaksi?search - Pencarian" enabled="true">
          <intProp name="HTTPSampler.connect_timeout">10000</intProp>
          <intProp name="HTTPSampler.response_timeout">30000</intProp>
          <stringProp name="HTTPSampler.path">/transaksi</stringProp>
          <boolProp name="HTTPSampler.follow_redirects">true</boolProp>
          <stringProp name="HTTPSampler.method">GET</stringProp>
          <boolProp name="HTTPSampler.use_keepalive">true</boolProp>
          <boolProp name="HTTPSampler.postBodyRaw">false</boolProp>
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments">
            <collectionProp name="Arguments.arguments">
              <elementProp name="search" elementType="HTTPArgument">
                <boolProp name="HTTPArgument.always_encode">true</boolProp>
                <stringProp name="Argument.name">search</stringProp>
                <stringProp name="Argument.value">`${SEARCH_KEYWORD}</stringProp>
                <stringProp name="Argument.metadata">=</stringProp>
              </elementProp>
            </collectionProp>
          </elementProp>
        </HTTPSamplerProxy>
        <hashTree>
          <ResponseAssertion guiclass="AssertionGui" testclass="ResponseAssertion" testname="Assert HTTP 200" enabled="true">
            <collectionProp name="Asserion.test_strings"><stringProp name="49586">200</stringProp></collectionProp>
            <stringProp name="Assertion.test_field">Assertion.response_code</stringProp>
            <boolProp name="Assertion.assume_success">false</boolProp>
            <intProp name="Assertion.test_type">8</intProp>
          </ResponseAssertion>
          <hashTree/>
          <DurationAssertion guiclass="DurationAssertionGui" testclass="DurationAssertion" testname="Assert Response Time &lt; 5000ms" enabled="true">
            <stringProp name="DurationAssertion.duration">5000</stringProp>
          </DurationAssertion>
          <hashTree/>
        </hashTree>
"@
}

function Get-LocalCookieMgr {
    param([string]$session)
    return @"
        <CookieManager guiclass="CookiePanel" testclass="CookieManager"
                       testname="Cookie Manager LOCAL - Pre-Auth Session" enabled="true">
          <collectionProp name="CookieManager.cookies">
            <elementProp name="laravel_session" elementType="Cookie" testname="laravel_session">
              <stringProp name="Cookie.value">$session</stringProp>
              <stringProp name="Cookie.domain">localhost</stringProp>
              <stringProp name="Cookie.path">/</stringProp>
              <boolProp name="Cookie.secure">false</boolProp>
              <longProp name="Cookie.expires">0</longProp>
              <boolProp name="Cookie.path_specified">true</boolProp>
              <boolProp name="Cookie.domain_specified">true</boolProp>
            </elementProp>
          </collectionProp>
          <boolProp name="CookieManager.clearEachIteration">false</boolProp>
          <boolProp name="CookieManager.controlledByThreadGroup">false</boolProp>
        </CookieManager>
        <hashTree/>
"@
}

function Get-DeployCookieMgr {
    param([string]$session, [string]$wss)
    return @"
        <CookieManager guiclass="CookiePanel" testclass="CookieManager"
                       testname="Cookie Manager DEPLOY - Pre-Auth Session" enabled="true">
          <collectionProp name="CookieManager.cookies">
            <elementProp name="laravel_session" elementType="Cookie" testname="laravel_session">
              <stringProp name="Cookie.value">$session</stringProp>
              <stringProp name="Cookie.domain">laundry.aksaralearning.com</stringProp>
              <stringProp name="Cookie.path">/</stringProp>
              <boolProp name="Cookie.secure">true</boolProp>
              <longProp name="Cookie.expires">0</longProp>
              <boolProp name="Cookie.path_specified">true</boolProp>
              <boolProp name="Cookie.domain_specified">true</boolProp>
            </elementProp>
            <elementProp name="wssplashchk" elementType="Cookie" testname="wssplashchk">
              <stringProp name="Cookie.value">$wss</stringProp>
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
        </CookieManager>
        <hashTree/>
"@
}

function Get-HttpDefaults {
    param([string]$domain, [int]$port, [string]$proto)
    return @"
        <ConfigTestElement guiclass="HttpDefaultsGui" testclass="ConfigTestElement"
                           testname="HTTP Defaults" enabled="true">
          <stringProp name="HTTPSampler.domain">$domain</stringProp>
          <stringProp name="HTTPSampler.port">$port</stringProp>
          <stringProp name="HTTPSampler.protocol">$proto</stringProp>
          <stringProp name="HTTPSampler.contentEncoding">UTF-8</stringProp>
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments">
            <collectionProp name="Arguments.arguments"/>
          </elementProp>
        </ConfigTestElement>
        <hashTree/>
"@
}

function Get-Listeners {
    param([string]$tcName, [string]$csvPrefix)
    return @"
        <ResultCollector guiclass="SummaryReport" testclass="ResultCollector" testname="[$tcName] Summary Report" enabled="true">
          <boolProp name="ResultCollector.error_logging">false</boolProp>
          <objProp><name>saveConfig</name><value class="SampleSaveConfiguration">
            <time>true</time><latency>true</latency><timestamp>true</timestamp><success>true</success>
            <label>true</label><code>true</code><message>true</message><threadName>true</threadName>
            <dataType>true</dataType><encoding>false</encoding><assertions>true</assertions>
            <subresults>true</subresults><responseData>false</responseData><samplerData>false</samplerData>
            <xml>false</xml><fieldNames>true</fieldNames><responseHeaders>false</responseHeaders>
            <requestHeaders>false</requestHeaders><responseDataOnError>false</responseDataOnError>
            <saveAssertionResultsFailureMessage>true</saveAssertionResultsFailureMessage>
            <bytes>true</bytes><sentBytes>true</sentBytes><url>true</url>
            <threadCounts>true</threadCounts><idleTime>true</idleTime><connectTime>true</connectTime>
          </value></objProp>
          <stringProp name="filename">tests/performance/results/Transaksi/${csvPrefix}_summary.csv</stringProp>
        </ResultCollector>
        <hashTree/>
        <ResultCollector guiclass="StatVisualizer" testclass="ResultCollector" testname="[$tcName] Aggregate Report" enabled="true">
          <boolProp name="ResultCollector.error_logging">false</boolProp>
          <objProp><name>saveConfig</name><value class="SampleSaveConfiguration">
            <time>true</time><latency>true</latency><timestamp>true</timestamp><success>true</success>
            <label>true</label><code>true</code><message>true</message><threadName>true</threadName>
            <dataType>true</dataType><encoding>false</encoding><assertions>true</assertions>
            <subresults>true</subresults><responseData>false</responseData><samplerData>false</samplerData>
            <xml>false</xml><fieldNames>true</fieldNames><responseHeaders>false</responseHeaders>
            <requestHeaders>false</requestHeaders><responseDataOnError>false</responseDataOnError>
            <saveAssertionResultsFailureMessage>true</saveAssertionResultsFailureMessage>
            <bytes>true</bytes><sentBytes>true</sentBytes><url>true</url>
            <threadCounts>true</threadCounts><idleTime>true</idleTime><connectTime>true</connectTime>
          </value></objProp>
          <stringProp name="filename">tests/performance/results/Transaksi/${csvPrefix}_aggregate.csv</stringProp>
        </ResultCollector>
        <hashTree/>
        <ResultCollector guiclass="ViewResultsFullVisualizer" testclass="ResultCollector" testname="[$tcName] View Results Tree" enabled="true">
          <boolProp name="ResultCollector.error_logging">false</boolProp>
          <objProp><name>saveConfig</name><value class="SampleSaveConfiguration">
            <time>true</time><latency>true</latency><timestamp>true</timestamp><success>true</success>
            <label>true</label><code>true</code><message>true</message><threadName>true</threadName>
            <dataType>true</dataType><encoding>false</encoding><assertions>true</assertions>
            <subresults>true</subresults><responseData>false</responseData><samplerData>false</samplerData>
            <xml>false</xml><fieldNames>true</fieldNames><responseHeaders>false</responseHeaders>
            <requestHeaders>false</requestHeaders><responseDataOnError>true</responseDataOnError>
            <saveAssertionResultsFailureMessage>true</saveAssertionResultsFailureMessage>
            <bytes>true</bytes><sentBytes>true</sentBytes><url>true</url>
            <threadCounts>true</threadCounts><idleTime>true</idleTime><connectTime>true</connectTime>
          </value></objProp>
          <stringProp name="filename"></stringProp>
        </ResultCollector>
        <hashTree/>
"@
}

function New-PreAuthJmx {
    param(
        [string]$outFile,
        [string]$planName,
        [string]$jenis,
        [string]$tcLocal,  [int]$lusr, [int]$lramp, [int]$lloop, [string]$lLocalEnabled,
        [string]$tcDeploy, [int]$dusr, [int]$dramp, [int]$dloop, [string]$lDeployEnabled,
        [string]$localThink = "500",
        [string]$komentar
    )
    $lCM  = Get-LocalCookieMgr  -session $LocalSession
    $dCM  = Get-DeployCookieMgr -session $DeploySession -wss $WssValue
    $lHD  = Get-HttpDefaults -domain "localhost" -port 8000 -proto "http"
    $dHD  = Get-HttpDefaults -domain "laundry.aksaralearning.com" -port 443 -proto "https"
    $lSteps = Get-TransaksiBlock -prefix $tcLocal  -thinkMs $localThink
    $dSteps = Get-TransaksiBlock -prefix $tcDeploy -thinkMs "500"
    $lLst = Get-Listeners -tcName $tcLocal  -csvPrefix "${tcLocal}_LOCAL"
    $dLst = Get-Listeners -tcName $tcDeploy -csvPrefix "${tcDeploy}_DEPLOY"

    $jmx = @"
<?xml version="1.0" encoding="UTF-8"?>
<jmeterTestPlan version="1.2" properties="5.0" jmeter="5.6.3">
  <hashTree>
    <TestPlan guiclass="TestPlanGui" testclass="TestPlan"
              testname="$planName" enabled="true">
      <stringProp name="TestPlan.comments">
====================================================================
JENIS TEST : $jenis TESTING - PRE-AUTH SESSION
FITUR      : MENGELOLA DATA TRANSAKSI
TESTER     : FAI

METODE     : Pre-Authenticated Session
             Login manual via browser, laravel_session dipakai JMeter.
             Tidak ada step login - langsung akses /transaksi.
             Menghindari CSRF issue pada artisan serve (single-thread).

$komentar
====================================================================
      </stringProp>
      <boolProp name="TestPlan.functional_mode">false</boolProp>
      <boolProp name="TestPlan.serialize_threadgroups">false</boolProp>
      <boolProp name="TestPlan.tearDown_on_shutdown">true</boolProp>
      <elementProp name="TestPlan.user_defined_variables" elementType="Arguments" guiclass="ArgumentsPanel" testclass="Arguments" enabled="true">
        <collectionProp name="Arguments.arguments">
          <elementProp name="SEARCH_KEYWORD" elementType="Argument">
            <stringProp name="Argument.name">SEARCH_KEYWORD</stringProp>
            <stringProp name="Argument.value">IJ</stringProp>
            <stringProp name="Argument.metadata">=</stringProp>
          </elementProp>
        </collectionProp>
      </elementProp>
    </TestPlan>
    <hashTree>
      <HeaderManager guiclass="HeaderPanel" testclass="HeaderManager" testname="HTTP Header Manager" enabled="true">
        <collectionProp name="HeaderManager.headers">
          <elementProp name="" elementType="Header">
            <stringProp name="Header.name">User-Agent</stringProp>
            <stringProp name="Header.value">Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36</stringProp>
          </elementProp>
          <elementProp name="" elementType="Header">
            <stringProp name="Header.name">Accept</stringProp>
            <stringProp name="Header.value">text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8</stringProp>
          </elementProp>
          <elementProp name="" elementType="Header">
            <stringProp name="Header.name">Accept-Language</stringProp>
            <stringProp name="Header.value">id-ID,id;q=0.9,en;q=0.8</stringProp>
          </elementProp>
        </collectionProp>
      </HeaderManager>
      <hashTree/>

      <!-- $tcLocal LOCAL ($lusr user, ramp ${lramp}s, loop $lloop) -->
      <ThreadGroup guiclass="ThreadGroupGui" testclass="ThreadGroup"
                   testname="$tcLocal - LOCAL ($lusr User, Ramp-up ${lramp}s, Loop $lloop)"
                   enabled="$lLocalEnabled">
        <stringProp name="ThreadGroup.on_sample_error">continue</stringProp>
        <elementProp name="ThreadGroup.main_controller" elementType="LoopController" guiclass="LoopControlPanel" testclass="LoopController">
          <boolProp name="LoopController.continue_forever">false</boolProp>
          <stringProp name="LoopController.loops">$lloop</stringProp>
        </elementProp>
        <stringProp name="ThreadGroup.num_threads">$lusr</stringProp>
        <stringProp name="ThreadGroup.ramp_time">$lramp</stringProp>
        <boolProp name="ThreadGroup.same_user_on_next_iteration">false</boolProp>
        <boolProp name="ThreadGroup.scheduler">false</boolProp>
      </ThreadGroup>
      <hashTree>
$lCM
$lHD
$lSteps
$lLst
      </hashTree>

      <!-- $tcDeploy DEPLOY ($dusr user, ramp ${dramp}s, loop $dloop) -->
      <ThreadGroup guiclass="ThreadGroupGui" testclass="ThreadGroup"
                   testname="$tcDeploy - DEPLOY ($dusr User, Ramp-up ${dramp}s, Loop $dloop)"
                   enabled="$lDeployEnabled">
        <stringProp name="ThreadGroup.on_sample_error">continue</stringProp>
        <elementProp name="ThreadGroup.main_controller" elementType="LoopController" guiclass="LoopControlPanel" testclass="LoopController">
          <boolProp name="LoopController.continue_forever">false</boolProp>
          <stringProp name="LoopController.loops">$dloop</stringProp>
        </elementProp>
        <stringProp name="ThreadGroup.num_threads">$dusr</stringProp>
        <stringProp name="ThreadGroup.ramp_time">$dramp</stringProp>
        <boolProp name="ThreadGroup.same_user_on_next_iteration">false</boolProp>
        <boolProp name="ThreadGroup.scheduler">false</boolProp>
      </ThreadGroup>
      <hashTree>
$dCM
$dHD
$dSteps
$dLst
      </hashTree>
    </hashTree>
  </hashTree>
</jmeterTestPlan>
"@
    [System.IO.File]::WriteAllText($outFile, $jmx, [System.Text.Encoding]::UTF8)
    $sz = [int]([System.IO.File]::ReadAllBytes($outFile).Length/1024)
    Write-Host "CREATED: $(Split-Path $outFile -Leaf)  ($sz KB)"
}

# ============================================================
# Generate semua JMX dengan Pre-Auth Session
# ============================================================

# LOAD TEST
New-PreAuthJmx `
  -outFile  "$basePath\RFD_Transaksi_Load_Test.jmx" `
  -planName "RFD Laundry - Transaksi LOAD Test (TC-TRX-01 and TC-TRX-02)" `
  -jenis    "LOAD" `
  -tcLocal  "TC-TRX-01" -lusr 200 -lramp 30 -lloop 1 -lLocalEnabled "true" `
  -tcDeploy "TC-TRX-02" -dusr 10  -dramp 60 -dloop 1 -lDeployEnabled "false" `
  -komentar "TC-TRX-01 LOCAL : 200 user | Ramp-up 30s | Loop 1 | localhost:8000`nTC-TRX-02 DEPLOY: 10 user  | Ramp-up 60s | Loop 1 | laundry.aksaralearning.com"

# SPIKE TEST
New-PreAuthJmx `
  -outFile  "$basePath\RFD_Transaksi_Spike_Test.jmx" `
  -planName "RFD Laundry - Transaksi SPIKE Test (TC-SPK-01 and TC-SPK-02)" `
  -jenis    "SPIKE" `
  -tcLocal  "TC-SPK-01" -lusr 200 -lramp 5  -lloop 1 -lLocalEnabled "true" `
  -tcDeploy "TC-SPK-02" -dusr 50  -dramp 5  -dloop 1 -lDeployEnabled "false" `
  -localThink "200" `
  -komentar "TC-SPK-01 LOCAL : 200 user | Ramp-up 5s  | Loop 1 | localhost:8000`nTC-SPK-02 DEPLOY: 50 user  | Ramp-up 5s  | Loop 1 | laundry.aksaralearning.com"

# ENDURANCE TEST
New-PreAuthJmx `
  -outFile  "$basePath\RFD_Transaksi_Endurance_Test.jmx" `
  -planName "RFD Laundry - Transaksi ENDURANCE Test (TC-END-01 and TC-END-02)" `
  -jenis    "ENDURANCE" `
  -tcLocal  "TC-END-01" -lusr 50  -lramp 30 -lloop 5 -lLocalEnabled "true" `
  -tcDeploy "TC-END-02" -dusr 10  -dramp 30 -dloop 5 -lDeployEnabled "false" `
  -komentar "TC-END-01 LOCAL : 50 user  | Ramp-up 30s | Loop 5 | localhost:8000`nTC-END-02 DEPLOY: 10 user  | Ramp-up 30s | Loop 5 | laundry.aksaralearning.com"

# VOLUME TEST
New-PreAuthJmx `
  -outFile  "$basePath\RFD_Transaksi_Volume_Test.jmx" `
  -planName "RFD Laundry - Transaksi VOLUME Test (TC-VOL-01 and TC-VOL-02)" `
  -jenis    "VOLUME" `
  -tcLocal  "TC-VOL-01" -lusr 500 -lramp 60 -lloop 3 -lLocalEnabled "true" `
  -tcDeploy "TC-VOL-02" -dusr 50  -dramp 60 -dloop 3 -lDeployEnabled "false" `
  -komentar "TC-VOL-01 LOCAL : 500 user | Ramp-up 60s | Loop 3 | localhost:8000`nTC-VOL-02 DEPLOY: 50 user  | Ramp-up 60s | Loop 3 | laundry.aksaralearning.com"

Write-Host ""
Write-Host "======================================================"
Write-Host "SELESAI! 4 file JMX dibuat dengan Pre-Auth Session"
Write-Host "======================================================"
Write-Host ""
Write-Host "LOCAL  laravel_session: $($LocalSession.Substring(0,30))..."
Write-Host "DEPLOY laravel_session: $($DeploySession.Substring(0,30))..."
Write-Host ""
Write-Host "CATATAN: session expired setelah ~2 jam."
Write-Host "Jalankan ulang script ini dengan -LocalSession dan -DeploySession baru jika perlu."
