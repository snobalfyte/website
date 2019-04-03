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

$updateId = isset($_GET['id']) ? $_GET['id'] : 0;
$selectedLang = isset($_GET['pack']) ? $_GET['pack'] : 0;

require_once 'api/listlangs.php';
require_once 'api/listeditions.php';
require_once 'api/updateinfo.php';
require_once 'shared/style.php';

if(!$updateId) {
    fancyError('UNSPECIFIED_UPDATE', 'downloads');
    die();
}

if(!preg_match('/^[\da-fA-F]{8}-([\da-fA-F]{4}-){3}[\da-fA-F]{12}(_rev\.\d+)?$/', $updateId)) {
    fancyError('INCORRECT_ID', 'downloads');
    die();
}

$updateInfo = uupUpdateInfo($updateId);
$updateInfo = isset($updateInfo['info']) ? $updateInfo['info'] : array();

$updateTitle = uupParseUpdateInfo($updateInfo, 'title');
if(isset($updateTitle['error'])) {
    $updateTitle = 'Unknown update: '.$updateId;
} else {
    $updateTitle = $updateTitle['info'];
}

$updateArch = uupParseUpdateInfo($updateInfo, 'arch');
if(isset($updateArch['error'])) {
    $updateArch = '';
} else {
    $updateArch = $updateArch['info'];
}

$updateTitle = $updateTitle.' '.$updateArch;

if($selectedLang) {
    $langs = uupListLangs($updateId);
    $langs = $langs['langFancyNames'];

    $selectedLangName = $langs[strtolower($selectedLang)];

    $editions = uupListEditions($selectedLang, $updateId);
    if(isset($editions['error'])) {
        fancyError($editions['error'], 'downloads');
        die();
    }
    $editions = $editions['editionFancyNames'];
    asort($editions);
} else {
    $editions = array();
    $selectedLangName = 'All languages';
}

styleUpper('downloads', "Select edition for $updateTitle, $selectedLangName");
?>

<div class="ui horizontal divider">
    <h3><i class="archive icon"></i>Choose edition</h3>
</div>

<?php
if(!file_exists('packs/'.$updateId.'.json.gz')) {
    styleNoPackWarn();
}
?>

<div class="ui top attached segment">
    <form class="ui form" action="./download.php" method="get">
        <div class="field">
            <label>Update</label>
            <input type="text" disabled value="<?php echo $updateTitle; ?>">
            <input type="hidden" name="id" value="<?php echo $updateId; ?>">
        </div>

        <div class="field">
            <label>Language</label>
            <input type="text" disabled value="<?php echo $selectedLangName; ?>">
            <input type="hidden" name="pack" value="<?php echo $selectedLang; ?>">
        </div>

        <div class="field">
            <label>Edition</label>
            <select class="ui search dropdown" name="edition">
                <option value="0">All editions</option>
<?php
foreach($editions as $key => $val) {
    echo '<option value="'.$key.'">'.$val."</option>\n";
}
?>
            </select>
        </div>
        <button class="ui fluid right labeled icon primary button" type="submit">
            <i class="right arrow icon"></i>
            Next
        </button>
    </form>
</div>
<div class="ui bottom attached info message">
    <i class="info icon"></i>
    Click <i>Next</i> button to open summary page of your selection.
</div>

<div class="ui fluid tiny three steps">
      <div class="completed step">
            <i class="world icon"></i>
            <div class="content">
                  <div class="title">Choose language</div>
                  <div class="description">Choose your desired language</div>
            </div>
      </div>

      <div class="active step">
            <i class="archive icon"></i>
            <div class="content">
                  <div class="title">Choose edition</div>
                  <div class="description">Choose your desired edition</div>
            </div>
      </div>

      <div class="step">
            <i class="briefcase icon"></i>
            <div class="content">
                  <div class="title">Summary</div>
                  <div class="description">Review your selection and choose download method</div>
            </div>
      </div>
</div>

<script>$('select.dropdown').dropdown();</script>

<?php
styleLower();
?>
