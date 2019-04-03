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

$updateId = isset($_GET['id']) ? $_GET['id'] : null;
$search = isset($_GET['q']) ? $_GET['q'] : null;

require_once 'api/get.php';
require_once 'api/updateinfo.php';
require_once 'shared/get.php';
require_once 'shared/style.php';

if(!$updateId) {
    fancyError('UNSPECIFIED_UPDATE', 'downloads');
    die();
}

if(!checkUpdateIdValidity($updateId)) {
    fancyError('INCORRECT_ID', 'downloads');
    die();
}

$files = uupGetFiles($updateId, 0, 0, 2);
if(isset($files['error'])) {
    fancyError($files['error'], 'downloads');
    die();
}

$updateName = $files['updateName'];
$updateBuild = $files['build'];
$updateArch = $files['arch'];
$files = $files['files'];
$filesKeys = array_keys($files);

if($search) {
    $searchSafe = preg_quote($search, '/');
    if(preg_match('/^".*"$/', $searchSafe)) {
        $searchSafe = preg_replace('/^"|"$/', '', $searchSafe);
    } else {
        $searchSafe = str_replace(' ', '.*', $searchSafe);
    }

    $removeKeys = preg_grep('/.*'.$searchSafe.'.*/i', $filesKeys, PREG_GREP_INVERT);

    foreach($removeKeys as $value) {
        unset($files[$value]);
    }

    if(empty($files)) {
        fancyError('SEARCH_NO_RESULTS', 'downloads');
        die();
    }

    unset($removeKeys);
    $filesKeys = array_keys($files);
}

$urlBase = "./getfile.php?id=$updateId";
styleUpper('downloads', "Find files in $updateName $updateArch");
?>

<div class="ui horizontal divider">
    <h3><i class="list icon"></i><?php echo $updateName.' '.$updateArch; ?></h3>
</div>

<div class="ui top attached segment">
    <form class="ui form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
        <div class="field">
            <div class="ui big action input">
                <input type="hidden" name="id" value="<?php echo htmlentities($updateId); ?>">
                <input type="text" name="q" value="<?php echo htmlentities($search); ?>" placeholder="Search for files...">
                <button class="ui big blue icon button" type="submit"><i class="search icon"></i></button>
            </div>
        </div>
    </form>
</div>
<div class="ui bottom attached success message">
    <i class="search icon"></i>
    We have found <b><?php echo count($files); ?></b> files for your query.
</div>

<table class="ui celled striped table">
    <thead>
        <tr>
            <th>File</th>
            <th>SHA-1</th>
            <th>Size</th>
        </tr>
    </thead>
<?php
$totalSize = 0;
$prefixes = array('', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi');

foreach($filesKeys as $val) {
    $totalSize = $totalSize + $files[$val]['size'];
    $size = $files[$val]['size'];

    foreach($prefixes as $prefix) {
        if($size < 1024) break;
        $size = $size / 1024;
    }
    $size = round($size, 2);
    $size = "$size {$prefix}B";

    echo "<tr><td><a href=\"$urlBase&file=$val\">$val</a></td>";
    echo '<td><code>'.$files[$val]['sha1'].'</code></td><td>'.$size.'</td></tr>'."\n";
}

foreach($prefixes as $prefix) {
    if($totalSize < 1024) break;
    $totalSize = $totalSize / 1024;
}
$totalSize = round($totalSize, 2);
$totalSize = "$totalSize {$prefix}B";

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

<?php
styleLower();
?>
