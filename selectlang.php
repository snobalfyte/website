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

$updateId = isset($_GET['id']) ? $_GET['id'] : 0;

require_once 'api/listlangs.php';
require_once 'api/updateinfo.php';
require_once 'shared/style.php';

if(!$updateId) {
    fancyError('UNSPECIFIED_UPDATE', 'downloads');
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

$langs = uupListLangs($updateId);
$langs = $langs['langFancyNames'];
asort($langs);

styleUpper('downloads');
?>

<div class="ui horizontal divider">
    <h3><i class="world icon"></i>Choose language</h3>
</div>

<div class="ui top attached segment">
    <form class="ui form" action="./selectedition.php" method="get" id="langForm">
        <div class="field">
            <label>Update</label>
            <input type="text" disabled value="<?php echo $updateTitle; ?>">
            <input type="hidden" name="id" value="<?php echo $updateId; ?>">
        </div>

        <div class="field">
            <label>Language</label>
            <select class="ui search dropdown" name="pack" onchange="checkLanguage()">
                <option value="0">All languages</option>
<?php
foreach($langs as $key => $val) {
    echo '<option value="'.$key.'">'.$val."</option>\n";
}
?>
            </select>
        </div>
        <button class="ui fluid right labeled icon blue button" id="submitForm" type="submit">
            <i class="right arrow icon"></i>
            Next
        </button>
    </form>
</div>
<div class="ui bottom attached info message" id="userMessage">
    <i class="info icon"></i>
    <i>All languages</i> option does not support edition selection.
</div>

<script>
    $('select.dropdown').dropdown();

    function checkLanguage() {
        var form = document.getElementById('langForm');
        var btn = document.getElementById('submitForm');
        var msg = document.getElementById('userMessage');

        if(form.pack.value == 0) {
            form.action = './get.php';
            btn.className = "ui fluid right labeled icon red button";
            msg.className = "ui bottom attached warning message";
            msg.innerHTML = '<i class="warning icon"></i>' +
                            'Click <i>Next</i> button to send your request ' +
                            'to Windows Update servers.';
        } else {
            form.action = './selectedition.php';
            btn.className = "ui fluid right labeled icon blue button";
            msg.className = "ui bottom attached info message";
            msg.innerHTML = '<i class="info icon"></i>' +
                            'Click <i>Next</i> button to select edition you ' +
                            'want to download.';
        }
    }

    checkLanguage();
</script>

<?php
styleLower();
?>
