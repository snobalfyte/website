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

require_once 'shared/style.php';
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
        <a class="ui top attached fluid labeled icon large button" href="./latest.php">
            <i class="checkmark icon"></i>
            Fetch latest build
        </a>
        <div class="ui bottom attached segment">
            Retrieve latest build information from Windows Update servers and download it.
        </div>
    </div>

    <div class="column">
        <a class="ui top attached fluid labeled icon large blue button" href="./known.php">
            <i class="server icon"></i>
            Browse known builds
        </a>
        <div class="ui bottom attached segment">
            Choose build that is known in the local database and download it.
        </div>
    </div>
</div>

<div class="ui hidden divider"></div>
<?php
styleLower();
?>
