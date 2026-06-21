Add-Type -AssemblyName System.Drawing

$outputPath = "C:\Users\ashar\Documents\1. Kuliah\RFD Laundry Management System\RFD-Laundry\tests\performance\results\Laporan_Performance_Test_Transaksi.xlsx"
New-Item -ItemType Directory -Force -Path (Split-Path $outputPath) | Out-Null

# Helper: ubah RGB ke angka OLE color (format BGR)
function OleRGB([int]$r, [int]$g, [int]$b) { return [long]($r + ($g * 256) + ($b * 65536)) }

# Warna tema
$clrHeader   = OleRGB 31  73  125   # Biru tua
$clrSubHdr   = OleRGB 189 215 238   # Biru muda
$clrWhite    = OleRGB 255 255 255
$clrGray     = OleRGB 220 220 220

$clrLoad     = OleRGB 255 192 203   # Pink
$clrSpike    = OleRGB 216 191 216   # Ungu
$clrEndur    = OleRGB 173 216 230   # Biru muda
$clrVolume   = OleRGB 144 238 144   # Hijau muda

$clrRowLoad1 = OleRGB 255 242 242
$clrRowSpk1  = OleRGB 245 245 255
$clrRowEnd1  = OleRGB 242 255 242
$clrRowVol1  = OleRGB 255 250 240

# Mulai Excel
$excel = New-Object -ComObject Excel.Application
$excel.Visible = $false
$excel.DisplayAlerts = $false

$wb = $excel.Workbooks.Add()

# ============================================================
# SHEET 1: Laporan Utama
# ============================================================
$ws = $wb.Worksheets.Item(1)
$ws.Name = "Performance Test Transaksi"

# Judul
$ws.Cells.Item(1,1).Value2 = "LAPORAN HASIL PENGUJIAN PERFORMA - FITUR MENGELOLA DATA TRANSAKSI"
$ws.Range("A1:I1").Merge()
$ws.Range("A1:I1").Font.Bold = $true
$ws.Range("A1:I1").Font.Size = 13
$ws.Range("A1:I1").Font.Color = $clrWhite
$ws.Range("A1:I1").Interior.Color = $clrHeader
$ws.Range("A1:I1").HorizontalAlignment = -4108

$tgl = (Get-Date).ToString('dd MMMM yyyy', [System.Globalization.CultureInfo]::GetCultureInfo('id-ID'))
$ws.Cells.Item(2,1).Value2 = "Tester: FAI  |  Tanggal: $tgl  |  Tools: Apache JMeter 5.6.3  |  Target: Response time < 5.000 ms, Error Rate = 0%"
$ws.Range("A2:I2").Merge()
$ws.Range("A2:I2").Font.Italic = $true
$ws.Range("A2:I2").Font.Size = 10
$ws.Range("A2:I2").Interior.Color = $clrSubHdr
$ws.Range("A2:I2").HorizontalAlignment = -4108

# Header kolom
$hdrs = @("No","FEATURE","TESTER","TEST DESCRIPTION","TEST CASE ID","TEST INPUT DATA","EXPECTED RESULT","ACTUAL RESULT","TYPE TESTING")
for ($i=0; $i -lt $hdrs.Count; $i++) {
    $c = $ws.Cells.Item(3, $i+1)
    $c.Value2 = $hdrs[$i]
    $c.Font.Bold = $true
    $c.Font.Color = $clrWhite
    $c.Interior.Color = $clrHeader
    $c.HorizontalAlignment = -4108
    $c.VerticalAlignment  = -4108
}
$ws.Range("A3:I3").Borders.LineStyle = 1
$ws.Range("A3:I3").Borders.Weight = 2
$ws.Rows.Item(3).RowHeight = 30

# Data rows
$rows = @(
  @{n=1;feat="TRANSAKSI";tc="TC-TRX-01";typ="Load Testing";typc=$clrLoad;rowc=$clrRowLoad1;
    desc="Mengukur waktu respons endpoint GET /transaksi dan GET /transaksi?search dengan 200 virtual users dalam waktu 30 detik`n`nLOCAL"
    inp ="Number of Threads (users): 200`nRamp-up period (seconds): 30`nLoop Count: 1`nURL: http://localhost:8000"
    exp ="Response time average < 5.000 ms (5 detik)`nError rate = 0%"
    act ="GET /transaksi: Avg 1850 ms`nGET /transaksi?search: Avg 1812 ms`nOverall Avg: 2378 ms (2,4 detik)`nMax: 4586 ms`nError: 0%"},
  @{n=2;feat="TRANSAKSI";tc="TC-TRX-02";typ="Load Testing";typc=$clrLoad;rowc=$clrWhite;
    desc="Mengukur waktu respons endpoint GET /transaksi dan GET /transaksi?search dengan 10 virtual users dalam waktu 60 detik sesuai kebutuhan website`n`nDEPLOY"
    inp ="Number of Threads (users): 10`nRamp-up period (seconds): 60`nLoop Count: 1`nURL: https://laundry.aksaralearning.com"
    exp ="Response time average < 5.000 ms (5 detik)`nError rate = 0%"
    act ="GET /transaksi: Avg 151 ms`nGET /transaksi?search: Avg 22 ms`nOverall Avg: 87 ms`nMax: 753 ms`nError: 0%"},
  @{n=3;feat="TRANSAKSI";tc="TC-SPK-01";typ="Spike Testing";typc=$clrSpike;rowc=$clrRowSpk1;
    desc="Mengukur kemampuan sistem saat terjadi lonjakan trafik mendadak (flash crowd) dengan 200 virtual users dalam waktu 5 detik`n`nLOCAL"
    inp ="Number of Threads (users): 200`nRamp-up period (seconds): 5`nLoop Count: 1`nURL: http://localhost:8000"
    exp ="Response time average < 5.000 ms (5 detik)`nError rate = 0%"
    act ="GET /transaksi: Avg 2644 ms`nGET /transaksi?search: Avg 3422 ms`nOverall Avg: 3033 ms (3,0 detik)`nMax: 4609 ms`nError: 0%"},
  @{n=4;feat="TRANSAKSI";tc="TC-SPK-02";typ="Spike Testing";typc=$clrSpike;rowc=$clrWhite;
    desc="Mengukur kemampuan sistem saat terjadi lonjakan trafik mendadak (flash crowd) dengan 50 virtual users dalam waktu 5 detik sesuai kebutuhan website`n`nDEPLOY"
    inp ="Number of Threads (users): 50`nRamp-up period (seconds): 5`nLoop Count: 1`nURL: https://laundry.aksaralearning.com"
    exp ="Response time average < 5.000 ms (5 detik)`nError rate = 0%"
    act ="GET /transaksi: Avg 104 ms`nGET /transaksi?search: Avg 23 ms`nOverall Avg: 64 ms`nMax: 381 ms`nError: 0%"},
  @{n=5;feat="TRANSAKSI";tc="TC-END-01";typ="Endurance Testing";typc=$clrEndur;rowc=$clrRowEnd1;
    desc="Mengukur stabilitas sistem saat menanggung beban berkelanjutan dengan 50 virtual users selama 5 iterasi`n`nLOCAL"
    inp ="Number of Threads (users): 50`nRamp-up period (seconds): 30`nLoop Count: 5`nURL: http://localhost:8000"
    exp ="Response time average < 5.000 ms (5 detik)`nError rate = 0%"
    act ="[ Belum dijalankan ]`nJalankan Endurance Test`nuntuk mengisi hasil ini"},
  @{n=6;feat="TRANSAKSI";tc="TC-END-02";typ="Endurance Testing";typc=$clrEndur;rowc=$clrWhite;
    desc="Mengukur stabilitas sistem saat menanggung beban berkelanjutan dengan 10 virtual users selama 5 iterasi sesuai kebutuhan website`n`nDEPLOY"
    inp ="Number of Threads (users): 10`nRamp-up period (seconds): 30`nLoop Count: 5`nURL: https://laundry.aksaralearning.com"
    exp ="Response time average < 5.000 ms (5 detik)`nError rate = 0%"
    act ="[ Belum dijalankan ]`nJalankan Endurance Test`nuntuk mengisi hasil ini"},
  @{n=7;feat="TRANSAKSI";tc="TC-VOL-01";typ="Volume Testing";typc=$clrVolume;rowc=$clrRowVol1;
    desc="Mengukur kemampuan sistem menangani volume request besar dengan 500 virtual users selama 3 iterasi`n`nLOCAL"
    inp ="Number of Threads (users): 500`nRamp-up period (seconds): 60`nLoop Count: 3`nURL: http://localhost:8000"
    exp ="Response time average < 5.000 ms (5 detik)`nError rate = 0%"
    act ="[ Belum dijalankan ]`nJalankan Volume Test`nuntuk mengisi hasil ini"},
  @{n=8;feat="TRANSAKSI";tc="TC-VOL-02";typ="Volume Testing";typc=$clrVolume;rowc=$clrWhite;
    desc="Mengukur kemampuan sistem menangani volume request besar dengan 50 virtual users selama 3 iterasi sesuai kebutuhan website`n`nDEPLOY"
    inp ="Number of Threads (users): 50`nRamp-up period (seconds): 60`nLoop Count: 3`nURL: https://laundry.aksaralearning.com"
    exp ="Response time average < 5.000 ms (5 detik)`nError rate = 0%"
    act ="[ Belum dijalankan ]`nJalankan Volume Test`nuntuk mengisi hasil ini"}
)

foreach ($row in $rows) {
    $r = 3 + $row.n
    $ws.Cells.Item($r,1).Value2 = "$($row.n)"
    $ws.Cells.Item($r,2).Value2 = $row.feat
    $ws.Cells.Item($r,3).Value2 = "FAI"
    $ws.Cells.Item($r,4).Value2 = $row.desc
    $ws.Cells.Item($r,5).Value2 = $row.tc
    $ws.Cells.Item($r,6).Value2 = $row.inp
    $ws.Cells.Item($r,7).Value2 = $row.exp
    $ws.Cells.Item($r,8).Value2 = $row.act
    $ws.Cells.Item($r,9).Value2 = $row.typ

    $ws.Range("A${r}:H${r}").Interior.Color = $row.rowc
    $ws.Cells.Item($r,9).Interior.Color = $row.typc
    $ws.Cells.Item($r,9).Font.Bold = $true
    $ws.Cells.Item($r,9).HorizontalAlignment = -4108
    $ws.Range("A${r}:I${r}").WrapText = $true
    $ws.Range("A${r}:I${r}").VerticalAlignment = -4160   # xlTop
    $ws.Cells.Item($r,1).HorizontalAlignment = -4108
    $ws.Cells.Item($r,2).HorizontalAlignment = -4108
    $ws.Cells.Item($r,3).HorizontalAlignment = -4108
    $ws.Cells.Item($r,5).HorizontalAlignment = -4108
    $ws.Range("A${r}:I${r}").Borders.LineStyle = 1
    $ws.Range("A${r}:I${r}").Borders.Weight    = 2
    $ws.Rows.Item($r).RowHeight = 90
}

# Lebar kolom Sheet 1
$ws.Columns.Item(1).ColumnWidth = 5
$ws.Columns.Item(2).ColumnWidth = 14
$ws.Columns.Item(3).ColumnWidth = 10
$ws.Columns.Item(4).ColumnWidth = 34
$ws.Columns.Item(5).ColumnWidth = 13
$ws.Columns.Item(6).ColumnWidth = 28
$ws.Columns.Item(7).ColumnWidth = 28
$ws.Columns.Item(8).ColumnWidth = 32
$ws.Columns.Item(9).ColumnWidth = 19
$ws.Rows.Item(1).RowHeight = 28
$ws.Rows.Item(2).RowHeight = 18

$ws.Application.ActiveWindow.SplitRow = 3
$ws.Application.ActiveWindow.FreezePanes = $true
Write-Host "Sheet 1 selesai."

# ============================================================
# SHEET 2: Aggregate Data JMeter
# ============================================================
$ws2 = $wb.Worksheets.Add()
$ws2.Name = "Aggregate Summary"

$ah = @("Test Case","Endpoint","# Samples","Average (ms)","Min (ms)","Max (ms)","Std. Dev.","Error %","Throughput","Type Testing")
for ($i=0; $i -lt $ah.Count; $i++) {
    $c = $ws2.Cells.Item(1, $i+1)
    $c.Value2 = $ah[$i]
    $c.Font.Bold = $true
    $c.Font.Color = $clrWhite
    $c.Interior.Color = $clrHeader
    $c.HorizontalAlignment = -4108
}
$ws2.Range("A1:J1").Borders.LineStyle = 1
$ws2.Range("A1:J1").Borders.Weight = 2
$ws2.Rows.Item(1).RowHeight = 25

# Data agregat — semua nilai numeric disimpan sebagai angka
$agg = @(
  @("TC-TRX-01 LOCAL",  "GET /transaksi",        200, 1850, 537,  2338, 428.82,  "0.00%", "6.8/sec",  "Load Testing",  $clrRowLoad1, $clrLoad),
  @("TC-TRX-01 LOCAL",  "GET /transaksi?search",  200, 1812, 285,  2342, 565.42,  "0.00%", "7.1/sec",  "Load Testing",  $clrRowLoad1, $clrLoad),
  @("TC-TRX-01 LOCAL",  "TOTAL",                  800, 2378, 12,   4586, 1040.11, "0.00%", "21.4/sec", "Load Testing",  $clrGray,     $clrLoad),
  @("TC-TRX-02 DEPLOY", "GET /transaksi",          10,  151, 80,    753, 200.38,  "0.00%", "11.1/min", "Load Testing",  $clrWhite,    $clrLoad),
  @("TC-TRX-02 DEPLOY", "GET /transaksi?search",   10,   22, 21,     26,   1.64, "0.00%",  "11.2/min", "Load Testing",  $clrWhite,    $clrLoad),
  @("TC-TRX-02 DEPLOY", "TOTAL",                   20,   87, 21,    753, 155.69,  "0.00%", "22.2/min", "Load Testing",  $clrGray,     $clrLoad),
  @("TC-SPK-01 LOCAL",  "GET /transaksi",          200, 2644, 826, 4609, 1106.51, "0.00%", "20.9/sec", "Spike Testing", $clrRowSpk1,  $clrSpike),
  @("TC-SPK-01 LOCAL",  "GET /transaksi?search",   200, 3422, 1085,4608, 885.29,  "0.00%", "17.2/sec", "Spike Testing", $clrRowSpk1,  $clrSpike),
  @("TC-SPK-01 LOCAL",  "TOTAL",                   400, 3033, 826, 4609, 1074.78, "0.00%", "32.2/sec", "Spike Testing", $clrGray,     $clrSpike),
  @("TC-SPK-02 DEPLOY", "GET /transaksi",           50,  104, 77,   381,  70.45,  "0.00%", "10.0/sec", "Spike Testing", $clrWhite,    $clrSpike),
  @("TC-SPK-02 DEPLOY", "GET /transaksi?search",    50,   23, 19,    35,   3.07,  "0.00%", "10.7/sec", "Spike Testing", $clrWhite,    $clrSpike),
  @("TC-SPK-02 DEPLOY", "TOTAL",                   100,   64, 19,   381,  64.26,  "0.00%", "19.9/sec", "Spike Testing", $clrGray,     $clrSpike)
)

$ar = 2
foreach ($a in $agg) {
    $ws2.Cells.Item($ar, 1).Value2  = "$($a[0])"
    $ws2.Cells.Item($ar, 2).Value2  = "$($a[1])"
    $ws2.Cells.Item($ar, 3).Value2  = [int]$a[2]
    $ws2.Cells.Item($ar, 4).Value2  = [int]$a[3]
    $ws2.Cells.Item($ar, 5).Value2  = [int]$a[4]
    $ws2.Cells.Item($ar, 6).Value2  = [int]$a[5]
    $ws2.Cells.Item($ar, 7).Value2  = [double]$a[6]
    $ws2.Cells.Item($ar, 8).Value2  = "$($a[7])"
    $ws2.Cells.Item($ar, 9).Value2  = "$($a[8])"
    $ws2.Cells.Item($ar, 10).Value2 = "$($a[9])"

    $rowBg  = [long]$a[10]
    $typeBg = [long]$a[11]

    $ws2.Range("A${ar}:I${ar}").Interior.Color = $rowBg
    $ws2.Cells.Item($ar, 10).Interior.Color    = $typeBg
    $ws2.Cells.Item($ar, 10).Font.Bold         = $true
    $ws2.Cells.Item($ar, 10).HorizontalAlignment = -4108

    if ($a[1] -eq "TOTAL") {
        $ws2.Range("A${ar}:J${ar}").Font.Bold = $true
    }

    $ws2.Range("A${ar}:J${ar}").Borders.LineStyle = 1
    $ws2.Range("A${ar}:J${ar}").HorizontalAlignment = -4108
    $ar++
}

# Auto-fit numerik
$ws2.Columns.Item(1).ColumnWidth  = 18
$ws2.Columns.Item(2).ColumnWidth  = 30
$ws2.Columns.Item(3).ColumnWidth  = 11
$ws2.Columns.Item(4).ColumnWidth  = 14
$ws2.Columns.Item(5).ColumnWidth  = 10
$ws2.Columns.Item(6).ColumnWidth  = 10
$ws2.Columns.Item(7).ColumnWidth  = 11
$ws2.Columns.Item(8).ColumnWidth  = 10
$ws2.Columns.Item(9).ColumnWidth  = 13
$ws2.Columns.Item(10).ColumnWidth = 18
Write-Host "Sheet 2 selesai."

# Aktifkan Sheet 1 saat dibuka
$ws.Activate()

# Simpan
$wb.SaveAs($outputPath)
$wb.Close($false)
$excel.Quit()
[System.Runtime.InteropServices.Marshal]::ReleaseComObject($excel) | Out-Null

Write-Host ""
Write-Host "======================================"
Write-Host "File Excel berhasil dibuat!"
Write-Host "======================================"
Write-Host "Lokasi: $outputPath"
