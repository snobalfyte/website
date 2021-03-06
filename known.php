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

$search = isset($_GET['q']) ? $_GET['q'] : null;

require_once 'api/listid.php';
require_once 'shared/style.php';
$ids = uupListIds($search);
if(isset($ids['error'])) {
    fancyError($ids['error'], 'downloads');
    die();
}

$ids = $ids['builds'];

if(empty($ids)) {
    fancyError('NO_BUILDS_IN_FILEINFO', 'downloads');
    die();
}

styleUpper('downloads', 'Browse known builds');
?>
<div class="ui horizontal divider">
    <h3><i class="cubes icon"></i>Choose build</h3>
</div>

<div class="ui top attached segment">
    <form class="ui form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
        <div class="field">
            <div class="ui big action input">
                <input type="text" name="q" value="<?php echo htmlentities($search); ?>" placeholder="Search for builds...">
                <button class="ui big blue icon button" type="submit"><i class="search icon"></i></button>
            </div>
        </div>
    </form>
</div>
<div class="ui bottom attached success message">
    <i class="search icon"></i>
    We have found <b><?php echo count($ids); ?></b> builds for your query.
</div>

<table class="ui celled striped table">
    <thead>
        <tr>
            <th>Build</th>
            <th>Architecture</th>
            <th>Update ID</th>
        </tr>
    </thead>
<?php
foreach($ids as $val) {
    $arch = $val['arch'];
    if($arch == 'amd64') $arch = 'x64';

    echo '<tr><td>';
    echo '<i class="windows icon"></i>';
    echo '<a href="./selectlang.php?id='.$val['uuid'].'">'
         .$val['title'].' '.$val['arch']."</a>";
    echo '</td><td>';
    echo $arch;
    echo '</td><td>';
    echo '<code>'.$val['uuid'].'</code>';
    echo "</td></tr>\n";
}
?>
</table>

<?php
styleLower();
?>
