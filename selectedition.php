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

$updateId = isset($_GET['id']) ? $_GET['id'] : 0;
$selectedLang = isset($_GET['pack']) ? $_GET['pack'] : 0;

if(!$updateId) {
    die('Update ID is not specified');
}

require_once 'api/listlangs.php';
require_once 'api/listeditions.php';
require_once 'api/updateinfo.php';
require_once 'shared/style.php';

$updateTitle = uupUpdateInfo($updateId, 'title');
if(isset($updateTitle['error'])) {
    $updateTitle = 'Unknown update: '.$updateId;
} else {
    $updateTitle = $updateTitle['info'];
}

if($selectedLang) {
    $langs = uupListLangs();
    $langs = $langs['langFancyNames'];

    $selectedLangName = $langs[strtolower($selectedLang)];

    $editions = uupListEditions($selectedLang);
    if(isset($editions['error'])) {
        die($editions['error']);
    }
    $editions = $editions['editionFancyNames'];
    asort($editions);
} else {
    $selectedLangName = 'All languages';
}

styleUpper('downloads');
?>

<div class="ui horizontal divider">
    <h3><i class="archive icon"></i>Choose edition</h3>
</div>

<div class="ui top attached segment">
    <form class="ui form" action="./get.php" method="get">
        <div class="field">
            <label>Update</label>
            <input type="text" disabled value="<?php echo $updateTitle ?>">
            <input type="hidden" name="id" value="<?php echo $updateId ?>">
        </div>

        <div class="field">
            <label>Language</label>
            <input type="text" disabled value="<?php echo $selectedLangName ?>">
            <input type="hidden" name="pack" value="<?php echo $selectedLang ?>">
        </div>

        <div class="field">
            <label>Edition</label>
            <select class="ui search dropdown" name="edition">
                <option value="0">All editions</option>
<?php
if($selectedLang) {
    foreach($editions as $key => $val) {
        echo '<option value="'.$key.'">'.$val."</option>\n";
    }
}
?>
            </select>
        </div>
        <button class="ui fluid right labeled icon red button" type="submit">
            <i class="right arrow icon"></i>
            Next
        </button>
    </form>
</div>
<div class="ui bottom attached warning message">
    <i class="warning icon"></i>
    Clicking <i>Next</i> button will send your request to Windows Update servers.
</div>

<script>$('select.dropdown').dropdown();</script>

<?php
styleLower();
?>
