
# ============================================================
# Generator semua JMX Performance Test untuk Fitur Transaksi
# Spike, Endurance, Volume - LOCAL dan DEPLOY
# ============================================================

$basePath  = "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance"
$sessionValue = "eyJpdiI6ImlDZEJFSWNha3FxZTc0WWc4YXBZZlE9PSIsInZhbHVlIjoiTVNJeXlpVUdzL2kzdVdxcStobmd2MlR6MjJ6Uk5lTTc5QjlFZ3V4TVN0dEUrNkY0L1VGQmhPRklzYVJvU1JWTkkwcWZTNGFldjlsVDkwUUJ3blRFemRyb1pRTys5N0x3SUdITTZOT1M2cXZVUFErcVdnSXJaMlRHVGxIb2JTM3ciLCJtYWMiOiI0NzI0ZWQwOTU5YzI2NzMyOWZiZDdhNjI0NTRmZDJiZTM0YjBlMjg3N2I2NzY4MDc4NWE2NGI2MjQyNmUxN2Q1IiwidGFnIjoiIn0="
$wssValue     = "3529613bcfa9c87f06e0889d2f37efc03b7c48aa.1781257098.1"

# Helper: buat blok Cookie Manager DEPLOY (pre-auth)
function Get-DeployCookieManager {
    return @"
        <CookieManager guiclass="CookiePanel" testclass="CookieManager"
                       testname="Cookie Manager DEPLOY - Pre-Auth Session" enabled="true">
          <collectionProp name="CookieManager.cookies">
            <elementProp name="laravel_session" elementType="Cookie" testname="laravel_session">
              <stringProp name="Cookie.value">$sessionValue</stringProp>
              <stringProp name="Cookie.domain">laundry.aksaralearning.com</stringProp>
              <stringProp name="Cookie.path">/</stringProp>
              <boolProp name="Cookie.secure">true</boolProp>
              <longProp name="Cookie.expires">0</longProp>
              <boolProp name="Cookie.path_specified">true</boolProp>
              <boolProp name="Cookie.domain_specified">true</boolProp>
            </elementProp>
            <elementProp name="wssplashchk" elementType="Cookie" testname="wssplashchk">
              <stringProp name="Cookie.value">$wssValue</stringProp>
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

# Helper: buat HTTP Defaults DEPLOY
function Get-DeployHttpDefaults {
    return @"
        <ConfigTestElement guiclass="HttpDefaultsGui" testclass="ConfigTestElement"
                           testname="HTTP Defaults DEPLOY" enabled="true">
          <stringProp name="HTTPSampler.domain">laundry.aksaralearning.com</stringProp>
          <stringProp name="HTTPSampler.port">443</stringProp>
          <stringProp name="HTTPSampler.protocol">https</stringProp>
          <stringProp name="HTTPSampler.contentEncoding">UTF-8</stringProp>
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments" enabled="true">
            <collectionProp name="Arguments.arguments"/>
          </elementProp>
        </ConfigTestElement>
        <hashTree/>
"@
}

# Helper: buat HTTP Defaults LOCAL
function Get-LocalHttpDefaults {
    return @"
        <ConfigTestElement guiclass="HttpDefaultsGui" testclass="ConfigTestElement"
                           testname="HTTP Defaults LOCAL" enabled="true">
          <stringProp name="HTTPSampler.domain">localhost</stringProp>
          <stringProp name="HTTPSampler.port">8000</stringProp>
          <stringProp name="HTTPSampler.protocol">http</stringProp>
          <stringProp name="HTTPSampler.contentEncoding">UTF-8</stringProp>
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments" enabled="true">
            <collectionProp name="Arguments.arguments"/>
          </elementProp>
        </ConfigTestElement>
        <hashTree/>
"@
}

# Helper: buat step login LOCAL
function Get-LocalLoginSteps {
    param([string]$prefix)
    return @"
        <!-- Step 1: GET /login -->
        <HTTPSamplerProxy guiclass="HttpTestSampleGui" testclass="HTTPSamplerProxy"
                          testname="[$prefix] Step 1: GET /login - Ambil CSRF Token" enabled="true">
          <stringProp name="HTTPSampler.path">/login</stringProp>
          <stringProp name="HTTPSampler.method">GET</stringProp>
          <boolProp name="HTTPSampler.follow_redirects">true</boolProp>
          <boolProp name="HTTPSampler.use_keepalive">true</boolProp>
          <intProp name="HTTPSampler.connect_timeout">10000</intProp>
          <intProp name="HTTPSampler.response_timeout">30000</intProp>
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments" enabled="true">
            <collectionProp name="Arguments.arguments"/>
          </elementProp>
        </HTTPSamplerProxy>
        <hashTree>
          <RegexExtractor guiclass="RegexExtractorGui" testclass="RegexExtractor"
                          testname="Ekstrak CSRF Token" enabled="true">
            <stringProp name="RegexExtractor.useHeaders">false</stringProp>
            <stringProp name="RegexExtractor.refname">csrf_token</stringProp>
            <stringProp name="RegexExtractor.regex">name="_token" value="([^"]+)"</stringProp>
            <stringProp name="RegexExtractor.template">`$1</stringProp>
            <stringProp name="RegexExtractor.default">TOKEN_NOT_FOUND</stringProp>
            <stringProp name="RegexExtractor.match_number">1</stringProp>
            <boolProp name="RegexExtractor.default_empty_value">false</boolProp>
            <stringProp name="RegexExtractor.scope">body</stringProp>
          </RegexExtractor>
          <hashTree/>
          <ResponseAssertion guiclass="AssertionGui" testclass="ResponseAssertion"
                             testname="Assert HTTP 200" enabled="true">
            <collectionProp name="Asserion.test_strings"><stringProp name="49586">200</stringProp></collectionProp>
            <stringProp name="Assertion.test_field">Assertion.response_code</stringProp>
            <boolProp name="Assertion.assume_success">false</boolProp>
            <intProp name="Assertion.test_type">8</intProp>
          </ResponseAssertion>
          <hashTree/>
          <ConstantTimer guiclass="ConstantTimerGui" testclass="ConstantTimer" testname="Think Time 500ms" enabled="true">
            <stringProp name="ConstantTimer.delay">500</stringProp>
          </ConstantTimer>
          <hashTree/>
        </hashTree>
        <!-- Step 2: POST /login -->
        <HTTPSamplerProxy guiclass="HttpTestSampleGui" testclass="HTTPSamplerProxy"
                          testname="[$prefix] Step 2: POST /login - Autentikasi" enabled="true">
          <stringProp name="HTTPSampler.path">/login</stringProp>
          <stringProp name="HTTPSampler.method">POST</stringProp>
          <boolProp name="HTTPSampler.follow_redirects">true</boolProp>
          <boolProp name="HTTPSampler.use_keepalive">true</boolProp>
          <intProp name="HTTPSampler.connect_timeout">10000</intProp>
          <intProp name="HTTPSampler.response_timeout">30000</intProp>
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments" enabled="true">
            <collectionProp name="Arguments.arguments">
              <elementProp name="_token" elementType="HTTPArgument">
                <boolProp name="HTTPArgument.always_encode">true</boolProp>
                <stringProp name="Argument.name">_token</stringProp>
                <stringProp name="Argument.value">`${csrf_token}</stringProp>
                <stringProp name="Argument.metadata">=</stringProp>
              </elementProp>
              <elementProp name="username" elementType="HTTPArgument">
                <boolProp name="HTTPArgument.always_encode">true</boolProp>
                <stringProp name="Argument.name">username</stringProp>
                <stringProp name="Argument.value">1</stringProp>
                <stringProp name="Argument.metadata">=</stringProp>
              </elementProp>
              <elementProp name="password" elementType="HTTPArgument">
                <boolProp name="HTTPArgument.always_encode">true</boolProp>
                <stringProp name="Argument.name">password</stringProp>
                <stringProp name="Argument.value">12345678</stringProp>
                <stringProp name="Argument.metadata">=</stringProp>
              </elementProp>
            </collectionProp>
          </elementProp>
        </HTTPSamplerProxy>
        <hashTree>
          <ResponseAssertion guiclass="AssertionGui" testclass="ResponseAssertion"
                             testname="Assert HTTP 200" enabled="true">
            <collectionProp name="Asserion.test_strings"><stringProp name="49586">200</stringProp></collectionProp>
            <stringProp name="Assertion.test_field">Assertion.response_code</stringProp>
            <boolProp name="Assertion.assume_success">false</boolProp>
            <intProp name="Assertion.test_type">8</intProp>
          </ResponseAssertion>
          <hashTree/>
          <ConstantTimer guiclass="ConstantTimerGui" testclass="ConstantTimer" testname="Think Time 1000ms" enabled="true">
            <stringProp name="ConstantTimer.delay">1000</stringProp>
          </ConstantTimer>
          <hashTree/>
        </hashTree>
"@
}

# Helper: buat step transaksi (dipakai LOCAL dan DEPLOY)
function Get-TransaksiSteps {
    param([string]$prefix)
    return @"
        <!-- GET /transaksi -->
        <HTTPSamplerProxy guiclass="HttpTestSampleGui" testclass="HTTPSamplerProxy"
                          testname="[$prefix] GET /transaksi - Daftar Transaksi" enabled="true">
          <stringProp name="HTTPSampler.path">/transaksi</stringProp>
          <stringProp name="HTTPSampler.method">GET</stringProp>
          <boolProp name="HTTPSampler.follow_redirects">true</boolProp>
          <boolProp name="HTTPSampler.use_keepalive">true</boolProp>
          <intProp name="HTTPSampler.connect_timeout">10000</intProp>
          <intProp name="HTTPSampler.response_timeout">30000</intProp>
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments" enabled="true">
            <collectionProp name="Arguments.arguments"/>
          </elementProp>
        </HTTPSamplerProxy>
        <hashTree>
          <ResponseAssertion guiclass="AssertionGui" testclass="ResponseAssertion"
                             testname="Assert HTTP 200" enabled="true">
            <collectionProp name="Asserion.test_strings"><stringProp name="49586">200</stringProp></collectionProp>
            <stringProp name="Assertion.custom_message">[$prefix] /transaksi gagal - cek session</stringProp>
            <stringProp name="Assertion.test_field">Assertion.response_code</stringProp>
            <boolProp name="Assertion.assume_success">false</boolProp>
            <intProp name="Assertion.test_type">8</intProp>
          </ResponseAssertion>
          <hashTree/>
          <DurationAssertion guiclass="DurationAssertionGui" testclass="DurationAssertion"
                             testname="Assert Response Time &lt; 5000ms" enabled="true">
            <stringProp name="DurationAssertion.duration">5000</stringProp>
          </DurationAssertion>
          <hashTree/>
          <ConstantTimer guiclass="ConstantTimerGui" testclass="ConstantTimer" testname="Think Time 500ms" enabled="true">
            <stringProp name="ConstantTimer.delay">500</stringProp>
          </ConstantTimer>
          <hashTree/>
        </hashTree>
        <!-- GET /transaksi?search -->
        <HTTPSamplerProxy guiclass="HttpTestSampleGui" testclass="HTTPSamplerProxy"
                          testname="[$prefix] GET /transaksi?search - Pencarian" enabled="true">
          <stringProp name="HTTPSampler.path">/transaksi</stringProp>
          <stringProp name="HTTPSampler.method">GET</stringProp>
          <boolProp name="HTTPSampler.follow_redirects">true</boolProp>
          <boolProp name="HTTPSampler.use_keepalive">true</boolProp>
          <intProp name="HTTPSampler.connect_timeout">10000</intProp>
          <intProp name="HTTPSampler.response_timeout">30000</intProp>
          <elementProp name="HTTPsampler.Arguments" elementType="Arguments" guiclass="HTTPArgumentsPanel" testclass="Arguments" enabled="true">
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
          <ResponseAssertion guiclass="AssertionGui" testclass="ResponseAssertion"
                             testname="Assert HTTP 200" enabled="true">
            <collectionProp name="Asserion.test_strings"><stringProp name="49586">200</stringProp></collectionProp>
            <stringProp name="Assertion.test_field">Assertion.response_code</stringProp>
            <boolProp name="Assertion.assume_success">false</boolProp>
            <intProp name="Assertion.test_type">8</intProp>
          </ResponseAssertion>
          <hashTree/>
          <DurationAssertion guiclass="DurationAssertionGui" testclass="DurationAssertion"
                             testname="Assert Response Time &lt; 5000ms" enabled="true">
            <stringProp name="DurationAssertion.duration">5000</stringProp>
          </DurationAssertion>
          <hashTree/>
        </hashTree>
"@
}

# Helper: buat Listeners
function Get-Listeners {
    param([string]$prefix, [string]$csvName)
    return @"
        <ResultCollector guiclass="SummaryReport" testclass="ResultCollector"
                         testname="[$prefix] Summary Report" enabled="true">
          <boolProp name="ResultCollector.error_logging">false</boolProp>
          <objProp><name>saveConfig</name><value class="SampleSaveConfiguration">
            <time>true</time><latency>true</latency><timestamp>true</timestamp>
            <success>true</success><label>true</label><code>true</code>
            <message>true</message><threadName>true</threadName>
            <dataType>true</dataType><encoding>false</encoding>
            <assertions>true</assertions><subresults>true</subresults>
            <responseData>false</responseData><samplerData>false</samplerData>
            <xml>false</xml><fieldNames>true</fieldNames>
            <responseHeaders>false</responseHeaders><requestHeaders>false</requestHeaders>
            <responseDataOnError>false</responseDataOnError>
            <saveAssertionResultsFailureMessage>true</saveAssertionResultsFailureMessage>
            <bytes>true</bytes><sentBytes>true</sentBytes><url>true</url>
            <threadCounts>true</threadCounts><idleTime>true</idleTime><connectTime>true</connectTime>
          </value></objProp>
          <stringProp name="filename">tests/performance/results/Transaksi/${csvName}_summary.csv</stringProp>
        </ResultCollector>
        <hashTree/>
        <ResultCollector guiclass="StatVisualizer" testclass="ResultCollector"
                         testname="[$prefix] Aggregate Report" enabled="true">
          <boolProp name="ResultCollector.error_logging">false</boolProp>
          <objProp><name>saveConfig</name><value class="SampleSaveConfiguration">
            <time>true</time><latency>true</latency><timestamp>true</timestamp>
            <success>true</success><label>true</label><code>true</code>
            <message>true</message><threadName>true</threadName>
            <dataType>true</dataType><encoding>false</encoding>
            <assertions>true</assertions><subresults>true</subresults>
            <responseData>false</responseData><samplerData>false</samplerData>
            <xml>false</xml><fieldNames>true</fieldNames>
            <responseHeaders>false</responseHeaders><requestHeaders>false</requestHeaders>
            <responseDataOnError>false</responseDataOnError>
            <saveAssertionResultsFailureMessage>true</saveAssertionResultsFailureMessage>
            <bytes>true</bytes><sentBytes>true</sentBytes><url>true</url>
            <threadCounts>true</threadCounts><idleTime>true</idleTime><connectTime>true</connectTime>
          </value></objProp>
          <stringProp name="filename">tests/performance/results/Transaksi/${csvName}_aggregate.csv</stringProp>
        </ResultCollector>
        <hashTree/>
        <ResultCollector guiclass="ViewResultsFullVisualizer" testclass="ResultCollector"
                         testname="[$prefix] View Results Tree" enabled="true">
          <boolProp name="ResultCollector.error_logging">false</boolProp>
          <objProp><name>saveConfig</name><value class="SampleSaveConfiguration">
            <time>true</time><latency>true</latency><timestamp>true</timestamp>
            <success>true</success><label>true</label><code>true</code>
            <message>true</message><threadName>true</threadName>
            <dataType>true</dataType><encoding>false</encoding>
            <assertions>true</assertions><subresults>true</subresults>
            <responseData>false</responseData><samplerData>false</samplerData>
            <xml>false</xml><fieldNames>true</fieldNames>
            <responseHeaders>false</responseHeaders><requestHeaders>false</requestHeaders>
            <responseDataOnError>true</responseDataOnError>
            <saveAssertionResultsFailureMessage>true</saveAssertionResultsFailureMessage>
            <bytes>true</bytes><sentBytes>true</sentBytes><url>true</url>
            <threadCounts>true</threadCounts><idleTime>true</idleTime><connectTime>true</connectTime>
          </value></objProp>
          <stringProp name="filename"></stringProp>
        </ResultCollector>
        <hashTree/>
"@
}

# Helper: buat JMX lengkap
function New-JmxFile {
    param(
        [string]$filePath,
        [string]$testName,
        [string]$testType,       # SPIKE / ENDURANCE / VOLUME
        [string]$tcLocal,        # e.g. TC-SPK-01
        [string]$tcDeploy,       # e.g. TC-SPK-02
        [int]$localUsers,
        [int]$localRampup,
        [int]$localLoops,
        [int]$deployUsers,
        [int]$deployRampup,
        [int]$deployLoops,
        [string]$localEnabledStr,
        [string]$deployEnabledStr,
        [string]$description
    )

    $localSteps   = (Get-LocalLoginSteps -prefix $tcLocal) + (Get-TransaksiSteps -prefix $tcLocal)
    $localCM      = @"
        <CookieManager guiclass="CookiePanel" testclass="CookieManager"
                       testname="Cookie Manager LOCAL" enabled="true">
          <collectionProp name="CookieManager.cookies"/>
          <boolProp name="CookieManager.clearEachIteration">true</boolProp>
          <boolProp name="CookieManager.controlledByThreadGroup">false</boolProp>
        </CookieManager>
        <hashTree/>
"@
    $deploySteps  = Get-TransaksiSteps -prefix $tcDeploy
    $deployCM     = Get-DeployCookieManager
    $deployHD     = Get-DeployHttpDefaults
    $localHD      = Get-LocalHttpDefaults
    $listenersL   = Get-Listeners -prefix $tcLocal  -csvName "${tcLocal}_LOCAL"
    $listenersD   = Get-Listeners -prefix $tcDeploy -csvName "${tcDeploy}_DEPLOY"

    $jmx = @"
<?xml version="1.0" encoding="UTF-8"?>
<jmeterTestPlan version="1.2" properties="5.0" jmeter="5.6.3">
  <hashTree>
    <TestPlan guiclass="TestPlanGui" testclass="TestPlan"
              testname="RFD Laundry - Transaksi $testType Test ($tcLocal and $tcDeploy)"
              enabled="true">
      <stringProp name="TestPlan.comments">
====================================================================
JENIS TEST : $testType TESTING
FITUR      : MENGELOLA DATA TRANSAKSI
TESTER     : FAI
$description
====================================================================
      </stringProp>
      <boolProp name="TestPlan.functional_mode">false</boolProp>
      <boolProp name="TestPlan.serialize_threadgroups">false</boolProp>
      <boolProp name="TestPlan.tearDown_on_shutdown">true</boolProp>
      <elementProp name="TestPlan.user_defined_variables" elementType="Arguments"
                   guiclass="ArgumentsPanel" testclass="Arguments" enabled="true">
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
      <HeaderManager guiclass="HeaderPanel" testclass="HeaderManager"
                     testname="HTTP Header Manager" enabled="true">
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

      <!-- $tcLocal - LOCAL ($localUsers User, Ramp-up ${localRampup}s, Loop $localLoops) -->
      <ThreadGroup guiclass="ThreadGroupGui" testclass="ThreadGroup"
                   testname="$tcLocal - LOCAL ($localUsers User, Ramp-up ${localRampup}s, Loop $localLoops)"
                   enabled="$localEnabledStr">
        <stringProp name="ThreadGroup.on_sample_error">continue</stringProp>
        <elementProp name="ThreadGroup.main_controller" elementType="LoopController"
                     guiclass="LoopControlPanel" testclass="LoopController" enabled="true">
          <boolProp name="LoopController.continue_forever">false</boolProp>
          <stringProp name="LoopController.loops">$localLoops</stringProp>
        </elementProp>
        <stringProp name="ThreadGroup.num_threads">$localUsers</stringProp>
        <stringProp name="ThreadGroup.ramp_time">$localRampup</stringProp>
        <boolProp name="ThreadGroup.same_user_on_next_iteration">false</boolProp>
        <boolProp name="ThreadGroup.scheduler">false</boolProp>
      </ThreadGroup>
      <hashTree>
$localCM
$localHD
$localSteps
$listenersL
      </hashTree>

      <!-- $tcDeploy - DEPLOY ($deployUsers User, Ramp-up ${deployRampup}s, Loop $deployLoops) Pre-Auth -->
      <ThreadGroup guiclass="ThreadGroupGui" testclass="ThreadGroup"
                   testname="$tcDeploy - DEPLOY ($deployUsers User, Ramp-up ${deployRampup}s, Loop $deployLoops)"
                   enabled="$deployEnabledStr">
        <stringProp name="ThreadGroup.on_sample_error">continue</stringProp>
        <elementProp name="ThreadGroup.main_controller" elementType="LoopController"
                     guiclass="LoopControlPanel" testclass="LoopController" enabled="true">
          <boolProp name="LoopController.continue_forever">false</boolProp>
          <stringProp name="LoopController.loops">$deployLoops</stringProp>
        </elementProp>
        <stringProp name="ThreadGroup.num_threads">$deployUsers</stringProp>
        <stringProp name="ThreadGroup.ramp_time">$deployRampup</stringProp>
        <boolProp name="ThreadGroup.same_user_on_next_iteration">false</boolProp>
        <boolProp name="ThreadGroup.scheduler">false</boolProp>
      </ThreadGroup>
      <hashTree>
$deployCM
$deployHD
$deploySteps
$listenersD
      </hashTree>

    </hashTree>
  </hashTree>
</jmeterTestPlan>
"@
    [System.IO.File]::WriteAllText($filePath, $jmx, [System.Text.Encoding]::UTF8)
    Write-Host "CREATED: $(Split-Path $filePath -Leaf) ($([int]([System.IO.File]::ReadAllBytes($filePath).Length/1024)) KB)"
}

# ============================================================
# 1. SPIKE TEST
# Tujuan: Uji kemampuan sistem saat terjadi lonjakan trafik mendadak
# LOCAL : 500 user, ramp-up 5s (sangat cepat), 1 loop
# DEPLOY: 50 user,  ramp-up 5s,                1 loop
# ============================================================
New-JmxFile `
  -filePath        "$basePath\RFD_Transaksi_Spike_Test.jmx" `
  -testName        "Spike" `
  -testType        "SPIKE" `
  -tcLocal         "TC-SPK-01" `
  -tcDeploy        "TC-SPK-02" `
  -localUsers      500 `
  -localRampup     5 `
  -localLoops      1 `
  -deployUsers     50 `
  -deployRampup    5 `
  -deployLoops     1 `
  -localEnabledStr "true" `
  -deployEnabledStr "false" `
  -description     @"
TC-SPK-01 LOCAL : 500 user | Ramp-up 5s  | Loop 1 | http://localhost:8000
TC-SPK-02 DEPLOY: 50 user  | Ramp-up 5s  | Loop 1 | https://laundry.aksaralearning.com

Spike test mensimulasikan lonjakan trafik mendadak (flash crowd) seperti
promo, event, atau siaran langsung. Sistem diuji apakah tetap merespons
dalam batas normal atau mengalami error saat beban naik tiba-tiba.
"@

# ============================================================
# 2. ENDURANCE TEST (Soak Test)
# Tujuan: Uji stabilitas sistem dalam jangka waktu panjang
# LOCAL : 50 user,  ramp-up 30s, 5 loop
# DEPLOY: 10 user,  ramp-up 30s, 5 loop
# ============================================================
New-JmxFile `
  -filePath        "$basePath\RFD_Transaksi_Endurance_Test.jmx" `
  -testName        "Endurance" `
  -testType        "ENDURANCE" `
  -tcLocal         "TC-END-01" `
  -tcDeploy        "TC-END-02" `
  -localUsers      50 `
  -localRampup     30 `
  -localLoops      5 `
  -deployUsers     10 `
  -deployRampup    30 `
  -deployLoops     5 `
  -localEnabledStr "true" `
  -deployEnabledStr "false" `
  -description     @"
TC-END-01 LOCAL : 50 user  | Ramp-up 30s | Loop 5 | http://localhost:8000
TC-END-02 DEPLOY: 10 user  | Ramp-up 30s | Loop 5 | https://laundry.aksaralearning.com

Endurance (Soak) test mensimulasikan beban berkelanjutan dalam jangka
panjang untuk mendeteksi memory leak, degradasi performa, atau
kegagalan koneksi database seiring waktu.
"@

# ============================================================
# 3. VOLUME TEST
# Tujuan: Uji kemampuan sistem menangani volume data/request besar
# LOCAL : 500 user, ramp-up 60s, 3 loop
# DEPLOY: 50 user,  ramp-up 60s, 3 loop
# ============================================================
New-JmxFile `
  -filePath        "$basePath\RFD_Transaksi_Volume_Test.jmx" `
  -testName        "Volume" `
  -testType        "VOLUME" `
  -tcLocal         "TC-VOL-01" `
  -tcDeploy        "TC-VOL-02" `
  -localUsers      500 `
  -localRampup     60 `
  -localLoops      3 `
  -deployUsers     50 `
  -deployRampup    60 `
  -deployLoops     3 `
  -localEnabledStr "true" `
  -deployEnabledStr "false" `
  -description     @"
TC-VOL-01 LOCAL : 500 user | Ramp-up 60s | Loop 3 | http://localhost:8000
TC-VOL-02 DEPLOY: 50 user  | Ramp-up 60s | Loop 3 | https://laundry.aksaralearning.com

Volume test mensimulasikan jumlah request yang sangat besar untuk menguji
kapasitas maksimum sistem dalam memproses data transaksi dalam skala besar.
Berbeda dengan load test, volume test fokus pada total volume request.
"@

Write-Host ""
Write-Host "=============================="
Write-Host "SEMUA JMX BERHASIL DIBUAT!"
Write-Host "=============================="
Write-Host ""
Write-Host "Ringkasan Test Parameters:"
Write-Host ""
Write-Host "SPIKE    LOCAL  TC-SPK-01: 500 user | ramp 5s   | 1 loop"
Write-Host "SPIKE    DEPLOY TC-SPK-02:  50 user | ramp 5s   | 1 loop  (pre-auth)"
Write-Host "ENDURANCE LOCAL  TC-END-01:  50 user | ramp 30s  | 5 loop"
Write-Host "ENDURANCE DEPLOY TC-END-02:  10 user | ramp 30s  | 5 loop  (pre-auth)"
Write-Host "VOLUME   LOCAL  TC-VOL-01: 500 user | ramp 60s  | 3 loop"
Write-Host "VOLUME   DEPLOY TC-VOL-02:  50 user | ramp 60s  | 3 loop  (pre-auth)"
Write-Host ""
Write-Host "File tersimpan di: $basePath"
