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
$usePack = isset($_GET['pack']) ? $_GET['pack'] : 0;
$desiredEdition = isset($_GET['edition']) ? $_GET['edition'] : 0;
$url = "./get.php?id=$updateId&pack=$usePack&edition=$desiredEdition";

if(!$usePack || $desiredEdition == 'updateOnly' || $desiredEdition == 'wubFile') {
    header("Location: $url");
    echo "<h1>Moved to <a href=\"$url\">here</a>.";
    die();
}

require_once 'api/get.php';
require_once 'api/listlangs.php';
require_once 'api/listeditions.php';
require_once 'shared/style.php';

$files = uupGetFiles($updateId, $usePack, $desiredEdition, 2);
if(isset($files['error'])) {
    fancyError($files['error'], 'downloads');
    die();
}

$updates = uupGetFiles($updateId, 0, 'updateOnly', 2);
if(isset($updates['error'])) {
    $hasUpdates = 0;
} else {
    $hasUpdates = 1;
}

$build = explode('.', $files['build']);
$build = @$build[0];
if($build < 17107) {
    $disableVE = 'disabled';
} else {
    $disableVE = '';
}

$updateTitle = "{$files['updateName']} {$files['arch']}";
$files = $files['files'];

$totalSize = 0;
foreach($files as $file) {
    $totalSize += $file['size'];
}

$prefixes = array('', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi');
foreach($prefixes as $prefix) {
    if($totalSize < 1024) break;
    $totalSize = $totalSize / 1024;
}
$totalSize = round($totalSize, 2);
$totalSize = "$totalSize {$prefix}B";

if($usePack) {
    $langs = uupListLangs($updateId);
    $langs = $langs['langFancyNames'];

    $selectedLangName = $langs[strtolower($usePack)];
} else {
    $selectedLangName = 'All languages';
}

if($usePack && $desiredEdition) {
    $editions = uupListEditions($usePack, $updateId);
    $editions = $editions['editionFancyNames'];

    $selectedEditionName = $editions[strtoupper($desiredEdition)];
} else {
    $selectedEditionName = 'All editions';
}

styleUpper('downloads', "Summary for $updateTitle, $selectedLangName, $selectedEditionName");
?>

<div class="ui horizontal divider">
    <h3><i class="briefcase icon"></i>Summary of your selection</h3>
</div>

<?php
if(!file_exists('packs/'.$updateId.'.json.gz')) {
    styleNoPackWarn();
}
?>

<div class="ui two columns mobile reversed stackable centered grid">
    <div class="column">
        <a class="ui top attached fluid labeled icon large button" href="<?php echo $url; ?>">
            <i class="list icon"></i>
            Browse a list of files
        </a>
        <div class="ui bottom attached segment">
            Opens a page with list of files in UUP set for manual download.
        </div>

        <a class="ui top attached fluid labeled icon large button" href="<?php echo $url; ?>&autodl=1">
            <i class="archive icon"></i>
            Download using aria2
        </a>
        <div class="ui bottom attached segment">
            Easily download the selected UUP set using aria2.
        </div>

        <a class="ui top attached fluid labeled icon large blue button" href="<?php echo $url; ?>&autodl=2">
            <i class="archive icon"></i>
            Download using aria2 and convert
        </a>
        <div class="ui bottom attached segment">
            Easily download the selected UUP set using aria2 and convert it to ISO.
        </div>

        <a class="ui top attached fluid labeled icon large <?php echo $disableVE; ?> button" href="<?php echo $url; ?>&autodl=3">
            <i class="archive icon"></i>
            Download using aria2, convert and create virtual editions
        </a>
        <div class="ui bottom attached segment">
            Easily download the selected UUP set using aria2, create virtual
            editions and convert it to ISO. Creation process of virtual editions
            takes a lot of time and is only supported on Windows.
        </div>
    </div>

    <div class="column">
        <h4>Update</h4>
        <p><?php echo $updateTitle; ?></p>

        <h4>Language</h4>
        <p><?php echo $selectedLangName; ?></p>

        <h4>Edition</h4>
        <p><?php echo $selectedEditionName; ?></p>

        <h4>Total download size</h4>
        <p><?php echo $totalSize; ?></p>

<?php
if($hasUpdates) {
    echo <<<INFO
<h4>Additional updates</h4>
<p>This UUP set contains additional updates which will be integrated during
the conversion process significantly increasing the creation time.</p>

<a class="ui tiny labeled icon button" href="./get.php?id=$updateId&pack=0&edition=updateOnly">
    <i class="folder open icon"></i>
    Browse the list of updates
</a>
INFO;
}
?>
    </div>
</div>

<div class="ui positive message">
    <div class="header">
        Download using aria2 options notice
    </div>
    <p>Download using aria2 options create an archive which needs to be downloaded.
    The downloaded archive contains all needed files to achieve the selected task.</p>

    <p><b>To start the download process use a script for your platform:</b><br>
    - Windows: <code>aria2_download_windows.cmd</code></br>
    - Linux: <code>aria2_download_linux.sh</code></br>
    </p>

    <p>Aria2 is an open source project. You can find it here:
    <a href="https://aria2.github.io/">https://aria2.github.io/</a>.
    <br>UUP Conversion script (Windows version) has been created by
    <a href="https://forums.mydigitallife.net/members/abbodi1406.204274/">abbodi1406</a>.
    <br>UUP Conversion script (Linux version) is open source. You can find it here:
    <a href="https://gitlab.com/uup-dump/converter">https://gitlab.com/uup-dump/converter</a>.
    </p>
</div>


<div class="ui fluid tiny three steps">
      <div class="completed step">
            <i class="world icon"></i>
            <div class="content">
                  <div class="title">Choose language</div>
                  <div class="description">Choose your desired language</div>
            </div>
      </div>

      <div class="completed step">
            <i class="archive icon"></i>
            <div class="content">
                  <div class="title">Choose edition</div>
                  <div class="description">Choose your desired edition</div>
            </div>
      </div>

      <div class="active step">
            <i class="briefcase icon"></i>
            <div class="content">
                  <div class="title">Summary</div>
                  <div class="description">Review your selection and choose download method</div>
            </div>
      </div>
</div>


<?php
styleLower();
?>
