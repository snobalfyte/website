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

require_once 'shared/style.php';
styleUpper('home');
?>
<div class="ui horizontal divider">
    <h3><i class="mouse pointer icon"></i>Choose action</h3>
</div>

<div class="ui segment">
    <h3>Download the latest build</h3>
    <p>Retrieve latest build information from Windows Update servers and download it.</p>
    <a class="ui fluid labeled icon large blue button" href="./latest.php">
        <i class="checkmark icon"></i>
        Latest build
    </a>
</div>

<div class="ui segment">
    <h3>Download known build</h3>
    <p>Choose build that is known in the local database and download it.</p>
    <a class="ui fluid labeled icon large blue button" href="./known.php">
        <i class="server icon"></i>
        Known build
    </a>
</div>
<?php
styleLower();
?>
