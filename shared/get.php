<?php
/*
Copyright 2018 UUP dump authors

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

function sortBySize($a, $b) {
    global $files;

    if ($files[$a]['size'] == $files[$b]['size']) {
        return 0;
    }

    return ($files[$a]['size'] < $files[$b]['size']) ? -1 : 1;
}

//Create aria2 download package with conversion script
function createUupConvertPackage($url, $archiveName) {
    $currDir = dirname(__FILE__).'/..';
    $cmdScript = '@echo off
cd /d "%~dp0"

if NOT "%cd%"=="%cd: =%" (
    echo Current directory contains spaces in its path.
    echo Please move or rename the directory to one not containing spaces.
    echo.
    pause
    goto :EOF
)

NET SESSION >NUL 2>&1
IF %ERRORLEVEL% EQU 0 goto :START_PROCESS

set "command="""%~f0""" %*"
set "command=%command:\'=\'\'%"

powershell Start-Process -FilePath \'%COMSPEC%\' -ArgumentList \'/c """%command%"""\' -Verb RunAs 2>NUL
IF %ERRORLEVEL% GTR 0 (
    echo =====================================================
    echo This script needs to be executed as an administrator.
    echo =====================================================
    echo.
    pause
)

goto :EOF

:START_PROCESS
set "aria2=files\aria2c.exe"
set "a7z=files\7za.exe"
set "uupConv=files\uup-converter-wimlib.7z"
set "aria2Script=files\aria2_script.txt"
set "destDir=UUPs"

if NOT EXIST %aria2% goto :NO_ARIA2_ERROR
if NOT EXIST %a7z% goto :NO_FILE_ERROR
if NOT EXIST %uupConv% goto :NO_FILE_ERROR

echo Extracting UUP converter...
"%a7z%" -y x "%uupConv%" >NUL
echo.

echo Retrieving updated aria2 script...
"%aria2%" -o"%aria2Script%" --allow-overwrite=true --auto-file-renaming=false "'.$url.'"
if %ERRORLEVEL% GTR 0 goto DOWNLOAD_ERROR
echo.

echo Starting download of files...
"%aria2%" -x16 -s16 -j5 -c -R -d"%destDir%" -i"%aria2Script%"
if %ERRORLEVEL% GTR 0 goto DOWNLOAD_ERROR

if EXIST convert-UUP.cmd goto :START_CONVERT
pause
goto :EOF

:START_CONVERT
call convert-UUP.cmd
goto :EOF

:NO_ARIA2_ERROR
echo We couldn\'t find %aria2% in current directory.
echo.
echo You can download aria2 from:
echo https://aria2.github.io/
echo.
pause
goto :EOF

:NO_FILE_ERROR
echo We couldn\'t find one of needed files for this script.
pause
goto :EOF

:DOWNLOAD_ERROR
echo We have encountered an error while downloading files.
pause
goto :EOF

:EOF
';

    $zip = new ZipArchive;
    $archive = @tempnam($currDir.'/tmp', 'zip');
    $open = $zip->open($archive, ZipArchive::CREATE+ZipArchive::OVERWRITE);

    if(!file_exists($currDir.'/autodl_files/aria2c.exe')) {
        die('aria2c.exe does not exist');
    }

    if(!file_exists($currDir.'/autodl_files/7za.exe')) {
        die('7za.exe does not exist');
    }

    if(!file_exists($currDir.'/autodl_files/uup-converter-wimlib.7z')) {
        die('uup-converter-wimlib.7z does not exist');
    }

    if($open === TRUE) {
        $zip->addFromString('aria2_download.cmd', $cmdScript);
        $zip->addFile($currDir.'/autodl_files/aria2c.exe', 'files/aria2c.exe');
        $zip->addFile($currDir.'/autodl_files/7za.exe', 'files/7za.exe');
        $zip->addFile($currDir.'/autodl_files/uup-converter-wimlib.7z', 'files/uup-converter-wimlib.7z');
        $zip->close();
    } else {
        echo 'Failed to create archive.';
        die();
    }

    header('Content-Type: archive/zip');
    header('Content-Disposition: attachment; filename="'.$archiveName.'_convert.zip"');
    header('Content-Length: '.filesize($archive));

    $content = file_get_contents($archive);
    unlink($archive);

    echo $content;
}

//Create aria2 download package only
function createAria2Package($url, $archiveName) {
    $currDir = dirname(__FILE__).'/..';
    $cmdScript = '@echo off
cd /d "%~dp0"

set "aria2=files\aria2c.exe"
set "aria2Script=files\aria2_script.txt"
set "destDir=UUPs"

if NOT EXIST %aria2% goto :NO_ARIA2_ERROR

echo Retrieving updated aria2 script...
"%aria2%" -o"%aria2Script%" "'.$url.'"
if %ERRORLEVEL% GTR 0 goto DOWNLOAD_ERROR

echo Starting download of files...
"%aria2%" -x16 -s16 -j5 -c -R -d"%destDir%" -i"%aria2Script%"
if %ERRORLEVEL% GTR 0 goto DOWNLOAD_ERROR

erase /q /s "%aria2Script%" >NUL 2>&1
pause
goto EOF

:NO_ARIA2_ERROR
echo We couldn\'t find %aria2% in current directory.
echo.
echo You can download aria2 from:
echo https://aria2.github.io/
echo.
pause
goto EOF

:DOWNLOAD_ERROR
echo We have encountered an error while downloading files.
pause
goto EOF

:EOF
';

    $zip = new ZipArchive;
    $archive = @tempnam($currDir.'/tmp', 'zip');
    $open = $zip->open($archive, ZipArchive::CREATE+ZipArchive::OVERWRITE);

    if(!file_exists($currDir.'/autodl_files/aria2c.exe')) {
        die('aria2c.exe does not exist');
    }

    if($open === TRUE) {
        $zip->addFromString('aria2_download.cmd', $cmdScript);
        $zip->addFile($currDir.'/autodl_files/aria2c.exe', 'files/aria2c.exe');
        $zip->close();
    } else {
        echo 'Failed to create archive.';
        die();
    }

    header('Content-Type: archive/zip');
    header('Content-Disposition: attachment; filename="'.$archiveName.'.zip"');
    header('Content-Length: '.filesize($archive));

    $content = file_get_contents($archive);
    unlink($archive);

    echo $content;
}
?>
