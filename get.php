<?php
/*
Copyright 2019 UUP dump authors

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

$updateId = isset($_GET['id']) ? $_GET['id'] : 'c2a1d787-647b-486d-b264-f90f3782cdc6';
$simple = isset($_GET['simple']) ? $_GET['simple'] : 0;
$aria2 = isset($_GET['aria2']) ? $_GET['aria2'] : 0;
$autoDl = isset($_GET['autodl']) ? $_GET['autodl'] : 0;
$usePack = isset($_GET['pack']) ? $_GET['pack'] : 0;
$desiredEdition = isset($_GET['edition']) ? $_GET['edition'] : 0;

require_once 'api/get.php';
require_once 'api/updateinfo.php';
require_once 'shared/get.php';
require_once 'shared/style.php';

if(!preg_match('/^[\da-fA-F]{8}-([\da-fA-F]{4}-){3}[\da-fA-F]{12}(_rev\.\d+)?$/', $updateId)) {
    fancyError('INCORRECT_ID', 'downloads');
    die();
}

if($autoDl) {
    $info = uupUpdateInfo($updateId);
    $info = @$info['info'];

    $updateBuild = isset($info['build']) ? $info['build'] : 'UNKNOWN';
    $updateArch = isset($info['arch']) ? $info['arch'] : 'UNKNOWN';

    $langDir = $usePack ? $usePack : 'all';

    $id = substr($updateId, 0, 8);
    $archiveName = $updateBuild.'_'.$updateArch.'_'.$langDir.'_'.$id;

    $url = '';
    if(isset($_SERVER['HTTPS'])) {
        $url .= 'https://';
    } else {
        $url .= 'http://';
    }

    $url .=  $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $url .= '?id='.$updateId.'&pack='.$usePack.'&edition='.$desiredEdition.'&aria2=1';

    if($autoDl == 1) {
        createAria2Package($url, $archiveName);
    }

    if($autoDl == 2) {
        createUupConvertPackage($url, $archiveName);
    }

    die();
}

$aria2ActionInfo = 'You can quickly download these files at once using aria2.
<br>Click one of buttons that can be found below to
generate and download archive with script that will download everyting
automatically and eventually convert it to ISO file.

<br><br>The archive will contain aria2c.exe application, and an
aria2_download_windows.cmd script that will start the download process.

<br>The archive will also contain an aria2_download_linux.sh script, which
can be used if you are using Linux. This script has the same functionality
as the Windows one.

<br><br>If you choose option with conversion, then archive will also include a
conversion script that will be run after successful download.

<br><br>Aria2 is an open source project. You can find it here:
<a href="https://aria2.github.io/">https://aria2.github.io/</a>.
<br>UUP Conversion script (Windows version) has been created by
<a href="https://forums.mydigitallife.net/members/abbodi1406.204274/">abbodi1406</a>.
<br>UUP Conversion script (Linux version) is open source. You can find it here:
<a href="https://gitlab.com/uup-dump/converter">https://gitlab.com/uup-dump/converter</a>.';

if(!$usePack) {
    $aria2ActionInfo = 'You have selected All languages option.<br>
Automatic aria2 download for this option is not supported.
<br><br>
If you want to download and convert UUP files automatically,
please go back, select language and edition/editions.';
}

$files = uupGetFiles($updateId, $usePack, $desiredEdition, 1);
if(isset($files['error'])) {
    fancyError($files['error'], 'downloads');
    die();
}

$updateName = $files['updateName'];
$updateBuild = $files['build'];
$updateArch = $files['arch'];
$files = $files['files'];
$filesKeys = array_keys($files);

$request = explode('?', $_SERVER['REQUEST_URI'], 2);
$loc = $request[0].'?';
foreach ($_GET as $key => $value) {
    $loc=$loc.$key.'='.$value.'&';
}

if($simple) {
    header('Content-Type: text/plain');
    usort($filesKeys, 'sortBySize');
    foreach($filesKeys as $val) {
        echo $val."|".$files[$val]['sha1']."|".$files[$val]['url']."\n";
    }
    die();
}

if($aria2) {
    header('Content-Type: text/plain');
    if($autoDl) {
        header('Content-Disposition: attachment; filename="aria2_script.txt"');
    }
    usort($filesKeys, 'sortBySize');
    foreach($filesKeys as $val) {
        echo $files[$val]['url']."\n";
        echo '  out='.$val."\n";
        echo '  checksum=sha-1='.$files[$val]['sha1']."\n\n";
    }
    die();
}

styleUpper('downloads');
?>

<div class="ui horizontal divider">
    <h3><i class="list icon"></i><?php echo $updateName.' '.$updateArch; ?></h3>
</div>

<div class="ui segment">
<h3>Download using aria2</h3>
<?php
echo '<p>'.$aria2ActionInfo.'</p>';

if($usePack) {
    echo '<div class="two ui buttons">
        <a class="ui labeled icon primary button" href="'.$loc.'autodl=2">
            <i class="archive icon"></i>
            Download using aria2 and then convert
        </a>
        <a class="ui right labeled icon button" href="'.$loc.'autodl=1">
            <i class="download icon"></i>
            Download using aria2
        </a>
    </div>';
}
?>
</div>

<table class="ui celled striped table">
    <thead>
        <tr>
            <th>File</th>
            <th>Expires</th>
            <th>SHA-1</th>
            <th>Size</th>
        </tr>
    </thead>
<?php
$totalSize = 0;
foreach($filesKeys as $val) {
    $totalSize = $totalSize + $files[$val]['size'];
    $size = $files[$val]['size'];

    $sizeType = '';
    if($size > 1024) {
        $size = $size / 1024;
        $sizeType = 'K';
        if($size > 1024) {
            $size = $size / 1024;
            $sizeType = 'M';
        }
    }

    $size = round($size).$sizeType.'B';
    echo '<tr><td><a href="'.$files[$val]['url'].'">'.$val.'</a></td><td>'.gmdate("Y-m-d H:i:s T", $files[$val]['expire']).'</td>';
    echo '<td><code>'.$files[$val]['sha1'].'</code></td><td>'.$size.'</td></tr>'."\n";
}

$sizeType = '';
if($totalSize > 1024) {
    $totalSize = $totalSize / 1024;
    $sizeType = 'K';
    if($totalSize > 1024) {
        $totalSize = $totalSize / 1024;
        $sizeType = 'M';
    }
}

$totalSize = round($totalSize).$sizeType.'B';

if(count($filesKeys)+3 > 30) {
    $filesRows = 30;
} else {
    $filesRows = count($filesKeys)+3;
}
?>
</table>
<div class="ui info message">
    <i class="info icon"></i>
    Total size of files: <?php echo $totalSize ?>
</div>

<div class="ui divider"></div>

<div class="ui icon positive message">
    <i class="terminal icon"></i>
    <div class="content">
        <div class="header">File renaming script</div>
        <p>The script that can be found below can be used to quickly rename downloaded files.<br>
        Simply copy contents of the form below to new file with <code>cmd</code> extension, put it in folder with downloaded files and run.</p>
    </div>
</div>

<div class="ui form">
    <div class="field">
        <textarea readonly rows="<?php echo $filesRows ?>" style="font-family: monospace;">
@echo off
cd /d "%~dp0"
<?php
foreach($filesKeys as $val) {
    echo 'rename "'.$files[$val]['uuid'].'" "'.$val."\"\n";
}
?>
</textarea>
    </div>
</div>

<div class="ui divider"></div>

<div class="ui icon positive message">
    <i class="check circle outline icon"></i>
    <div class="content">
        <div class="header">SHA-1 checksums file</div>
        <p>You can use this file to quickly verify that files were downloaded correctly.</p>
    </div>
</div>

<div class="ui form">
    <div class="field">
        <textarea readonly rows="<?php echo $filesRows ?>" style="font-family: monospace;">
<?php
foreach($filesKeys as $val) {
    echo $files[$val]['sha1'].' *'.$val."\n";
}
?>
</textarea>
    </div>
</div>

<?php
styleLower();
?>
