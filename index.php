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

require_once 'api/listid.php';
require_once 'shared/style.php';

$buildsAvailable = 1;
$ids = uupListIds(null, 1);

if(isset($ids['error'])) {
    $buildsAvailable = 0;
}

$ids = $ids['builds'];

if(empty($ids)) {
    $buildsAvailable = 0;
}

styleUpper('home');
?>

<div class="welcome-text">
    <p class="header">UUP dump</p>
    <p class="sub"><i>Download UUP files from Windows Update servers with ease.</i></p>
</div>

<form class="ui form" action="./known.php" method="get">
    <div class="field">
        <div class="ui big action input">
            <input type="text" name="q" placeholder="Search for builds...">
            <button class="ui big blue icon button" type="submit"><i class="search icon"></i></button>
        </div>
        </div>
</form>

<div class="ui horizontal section divider"><h3><i class="ui user md icon"></i>Advanced options</h3></div>

<div class="ui two columns stackable centered grid">
    <div class="column">
        <a class="ui top attached fluid labeled icon large blue button" href="./known.php">
            <i class="server icon"></i>
            Browse a full list of known builds
        </a>
        <div class="ui bottom attached segment">
            Choose a build that is already known in the local database and download it.
        </div>
    </div>

    <div class="column">
        <a class="ui top attached fluid labeled icon large button" href="./latest.php">
            <i class="fire icon"></i>
            Fetch the latest build
        </a>
        <div class="ui bottom attached segment">
            Retrieve the latest build information from Windows Update servers.
        </div>
    </div>
</div>
<?php
if($buildsAvailable) {
    echo <<<EOD
<div class="ui horizontal section divider"><h3><i class="ui star outline icon"></i>Newly added builds</h3></div>
<table class="ui celled striped table">
    <thead>
        <tr>
            <th>Build</th>
            <th>Architecture</th>
            <th>Date added</th>
        </tr>
    </thead>
EOD;

    $i = 0;
    foreach($ids as $val) {
        $i++;
        if($i > 10) break;

        $arch = $val['arch'];
        if($arch == 'amd64') $arch = 'x64';

        echo '<tr><td>';
        echo '<i class="windows icon"></i>';
        echo '<a href="./selectlang.php?id='.$val['uuid'].'">'
             .$val['title'].' '.$val['arch']."</a>";
        echo '</td><td>';
        echo $arch;
        echo '</td><td>';

        if($val['created'] == null) {
            echo 'Unknown';
        } else {
            echo gmdate("Y-m-d H:i:s T", $val['created']);
        }

        echo "</td></tr>\n";
    }
    echo '</table>';
}
?>

<div class="ui hidden divider"></div>
<?php
styleLower();
?>
