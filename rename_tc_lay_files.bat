@echo off
pushd "d:\Tel'U\Semester6\Proyek Tingkat 3\RFD-Laundry"
setlocal enabledelayedexpansion
for %%F in (tests\Feature\Layanan\TC-LAY-*.php) do (
    set "name=%%~nxF"
    for /f "tokens=3 delims=-." %%A in ("!name!") do (
        set "new=TC_LAY_%%A_Test.php"
    )
    echo Renaming %%~nxF to !new!
    ren "%%~F" "!new!"
)
endlocal
popd
