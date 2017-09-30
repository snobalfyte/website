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

require_once 'api/listid.php';
require_once 'shared/style.php';
$ids = uupListIds();
if(isset($ids['error'])) {
    die($ids['error']);
}

$ids = $ids['builds'];
styleUpper('downloads');
?>
<div class="ui horizontal divider">
    <h3><i class="cubes icon"></i>Choose build</h3>
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
    echo '<tr><td>';
    echo '<i class="windows icon"></i>';
    echo '<a href="./selectlang.php?id='.$val['uuid'].'">'
         .$val['title'].' '.$val['arch']."</a>";
    echo '</td><td>';
    echo $val['arch'];
    echo '</td><td>';
    echo '<code>'.$val['uuid'].'</code>';
    echo "</td></tr>\n";
}
?>
</table>

<?php
styleLower();
?>
