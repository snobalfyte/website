<?php
/*
Copyright 2017 UUP dump authors

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

// Change this to disable aria2 local downloads support
$aria2SupportEnabled = 1;

function sortBySize($a, $b) {
    global $files;

    if ($files[$a]['size'] == $files[$b]['size']) {
        return 0;
    }

    return ($files[$a]['size'] < $files[$b]['size']) ? -1 : 1;
}

function sendToAria2($url, $name, $sha1, $dir) {
    global $aria2SupportEnabled;

    if(!$aria2SupportEnabled) {
        fancyError('ARIA2_SUPPORT_NOT_ENABLED', 'downloads');
        die();
    }

    $data = array(
        'jsonrpc' => '2.0',
        'id' => null,
        'method' => 'aria2.addUri',
        'params' => array(
            'token:MfR3lC7EvOM5Ji1RhDIgPexj81B71BvJ',
            array(
                $url,
            ),
            array(
                'dir' => $dir,
                'out' => $name,
                'checksum' => 'sha-1='.$sha1,
            ),
        ),
    );

    $postData = json_encode($data);
    $req = curl_init('http://127.0.0.1:24701/jsonrpc');

    curl_setopt($req, CURLOPT_HEADER, 0);
    curl_setopt($req, CURLOPT_POST, 1);
    curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($req, CURLOPT_POSTFIELDS, $postData);

    $out = curl_exec($req);
    curl_close($req);

    if(empty($out)) {
        fancyError('ARIA2_CONNECT_FAIL', 'downloads');
        die();
    }

    $out = json_decode($out, true);
    if(isset($out['error'])) {
        $errorMsg = '<br><i>'.$out['error']['message'].'</i>';
        fancyError('ARIA2_RPC_ERROR', 'downloads', $errorMsg);
        die();
    }
}

function createAria2Script($filesKeys, $files) {
    $aria2Script = '';
    foreach($filesKeys as $val) {
        $url = $files[$val]['url'];
        $aria2Script .= $url."\n";
        $aria2Script .= '  out='.$val."\n";
        $aria2Script .= '  checksum=sha-1='.$files[$val]['sha1']."\n";
        $aria2Script .= "\n";
    }

    return $aria2Script;
}

//Create aria2 download package with conversion script
function createUupConvertPackage($filesKeys, $files, $archiveName) {
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

set "aria2=files\aria2c.exe"
set "a7z=files\7za.exe"
set "uupConv=files\uup-converter-wimlib.7z"
set "aria2Script=files\aria2_script.txt"
set "destDir=UUPs"

if NOT EXIST %aria2% goto :NO_ARIA2_ERROR
if NOT EXIST %aria2Script% goto :NO_FILE_ERROR
if NOT EXIST %a7z% goto :NO_FILE_ERROR
if NOT EXIST %uupConv% goto :NO_FILE_ERROR

echo Extracting UUP converter...
"%a7z%" -y x "%uupConv%" >NUL
echo.

echo Starting download of files...
"%aria2%" -x16 -s16 -j5 -c -R -d"%destDir%" -i"%aria2Script%"
if %ERRORLEVEL% GTR 0 goto DOWNLOAD_ERROR

if EXIST convert-UUP.cmd goto :START_CONVERT_QUESTION
pause
goto :EOF

:START_CONVERT_QUESTION
echo.
set userConvert=y
set /p userConvert="Do you want to start conversion process of downloaded files? [Y/n] "
if /i "%userConvert%"=="y" call convert-UUP.cmd && exit /b
if /i "%userConvert%"=="n" goto :EOF
goto :START_CONVERT_QUESTION

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

    $aria2Script = createAria2Script($filesKeys, $files);

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
        $zip->addFromString('files/aria2_script.txt', $aria2Script);
        $zip->addFromString('aria2_download.cmd', $cmdScript);
        $zip->addFile($currDir.'/autodl_files/aria2c.exe', 'files/aria2c.exe');
        $zip->addFile($currDir.'/autodl_files/7za.exe', 'files/7za.exe');
        $zip->addFile($currDir.'/autodl_files/uup-converter-wimlib.7z', 'files/uup-converter-wimlib.7z');
        $zip->close();
    } else {
        echo 'Failed to create archive.';
        die();
    }

    $content = file_get_contents($archive);
    unlink($archive);

    header('Content-Type: archive/zip');
    header('Content-Disposition: attachment; filename="'.$archiveName.'_convert.zip"');

    echo $content;
}

//Create aria2 download package only
function createAria2Package($filesKeys, $files, $archiveName) {
    $currDir = dirname(__FILE__).'/..';
    $cmdScript = '@echo off
cd /d "%~dp0"

set "aria2=files\aria2c.exe"
set "aria2Script=files\aria2_script.txt"
set "destDir=UUPs"

if NOT EXIST %aria2% goto :NO_ARIA2_ERROR
if NOT EXIST %aria2Script% goto :NO_ARIA2_SCRIPT_ERROR

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

:NO_ARIA2_SCRIPT_ERROR
echo We couldn\'t find %aria2Script% in current directory.
pause
goto EOF

:DOWNLOAD_ERROR
echo We have encountered an error while downloading files.
pause
goto EOF

:EOF
';

    $aria2Script = createAria2Script($filesKeys, $files);

    $zip = new ZipArchive;
    $archive = @tempnam($currDir.'/tmp', 'zip');
    $open = $zip->open($archive, ZipArchive::CREATE+ZipArchive::OVERWRITE);

    if(!file_exists($currDir.'/autodl_files/aria2c.exe')) {
        die('aria2c.exe does not exist');
    }

    if($open === TRUE) {
        $zip->addFromString('files/aria2_script.txt', $aria2Script);
        $zip->addFromString('aria2_download.cmd', $cmdScript);
        $zip->addFile($currDir.'/autodl_files/aria2c.exe', 'files/aria2c.exe');
        $zip->close();
    } else {
        echo 'Failed to create archive.';
        die();
    }

    $content = file_get_contents($archive);
    unlink($archive);

    header('Content-Type: archive/zip');
    header('Content-Disposition: attachment; filename="'.$archiveName.'.zip"');

    echo $content;
}
?>
