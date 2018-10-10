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

$arch = isset($_GET['arch']) ? $_GET['arch'] : 'amd64';
$ring = isset($_GET['ring']) ? $_GET['ring'] : 'WIF';
$flight = isset($_GET['flight']) ? $_GET['flight'] : 'Active';
$build = isset($_GET['build']) ? $_GET['build'] : 16251;
$minor = isset($_GET['minor']) ? $_GET['minor'] : 0;
$sku = isset($_GET['sku']) ? $_GET['sku'] : 48;

require_once 'api/fetchupd.php';
require_once 'shared/style.php';

$cacheHash = hash('sha1', strtolower("fetch-$arch-$ring-$flight-$build-$minor-$sku"));
$cached = 0;

if(file_exists('cache/'.$cacheHash.'.json')) {
    $fetchUpd = @file_get_contents('cache/'.$cacheHash.'.json');
    $fetchUpd = json_decode($fetchUpd, 1);

    if(isset($fetchUpd['error'])) {
        fancyError($fetchUpd['error'], 'downloads');
        die();
    }

    if(!empty($fetchUpd['content']['updateId']) && ($fetchUpd['expires'] > time())) {
        $cached = 1;
        $fetchUpd = $fetchUpd['content'];
    } else {
        $cached = 0;
        unset($fetchUpd);
    }
}

if(!$cached) {
    $fetchUpd = uupFetchUpd($arch, $ring, $flight, $build, $minor, $sku);
    if(isset($fetchUpd['error'])) {
        fancyError($fetchUpd['error'], 'downloads');
        die();
    }

    $cache = array(
        'expires' => time()+90,
        'content' => $fetchUpd,
    );

    @file_put_contents('cache/'.$cacheHash.'.json', json_encode($cache)."\n");
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
            <input type="text" readonly value="<?php echo $fetchUpd['updateTitle']; ?>">
        </div>
        <div class="field">
            <label>Architecture</label>
            <input type="text" readonly value="<?php echo $fetchUpd['arch']; ?>">
        </div>
        <div class="field">
            <label>Build number</label>
            <input type="text" readonly value="<?php echo $fetchUpd['foundBuild']; ?>">
        </div>
        <div class="field">
            <label>Update ID</label>
            <input type="text" readonly value="<?php echo $fetchUpd['updateId']; ?>">
            <input type="hidden" name="id" value="<?php echo $fetchUpd['updateId']; ?>">
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
