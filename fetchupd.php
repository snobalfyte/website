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

$arch = isset($_GET['arch']) ? $_GET['arch'] : 'amd64';
$ring = isset($_GET['ring']) ? $_GET['ring'] : 'WIF';
$flight = isset($_GET['flight']) ? $_GET['flight'] : 'Active';
$build = isset($_GET['build']) ? $_GET['build'] : 16251;
$minor = isset($_GET['minor']) ? $_GET['minor'] : 0;

require_once 'api/fetchupd.php';
require_once 'shared/style.php';

$fetchedUpdate = uupFetchUpd($arch, $ring, $flight, $build, $minor);
if(isset($fetchedUpdate['error'])) {
    fancyError($fetchedUpdate['error'], 'downloads');
    die();
}

styleUpper('downloads');
?>

<div class="ui horizontal divider">
    <h3><i class="wizard icon"></i>Response from server</h3>
</div>

<div class="ui top attached segment">
    <form class="ui form" action="./selectlang.php" method="get">
        <div class="field">
            <label>Name of update</label>
            <input type="text" readonly value="<?php echo $fetchedUpdate['updateTitle']; ?>">
        </div>
        <div class="field">
            <label>Architecture</label>
            <input type="text" readonly value="<?php echo $fetchedUpdate['arch']; ?>">
        </div>
        <div class="field">
            <label>Build number</label>
            <input type="text" readonly value="<?php echo $fetchedUpdate['foundBuild']; ?>">
        </div>
        <div class="field">
            <label>Update ID</label>
            <input type="text" readonly value="<?php echo $fetchedUpdate['updateId']; ?>">
            <input type="hidden" name="id" value="<?php echo $fetchedUpdate['updateId']; ?>">
        </div>
        <button class="ui fluid right labeled icon blue button" type="submit">
            <i class="right arrow icon"></i>
            Next
        </button>
    </form>
</div>

<div class="ui bottom attached info message">
    <i class="info icon"></i>
    Click <i>Next</i> to select language and edition which you want to download.
</div>

<?php
styleLower();
?>
