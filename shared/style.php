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

$websiteVersion = '3.0.0-beta.2';

function styleUpper($pageType = 'home') {
    global $websiteVersion;

    switch ($pageType) {
        case 'home':
            $navbarLink = '<a class="active item" href="./index.php">Home</a>';
            break;
        case 'downloads':
            $navbarLink = '<a class="item" href="./index.php">Home</a>'.
                          '<a class="active item">Downloads</a>';
            break;
        default:
            $navbarLink = '<a class="active item" href="./index.php">Home</a>';
            break;
    }

    echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <meta property="og:title" content="UUP dump">
        <meta property="og:type" content="website">
        <meta property="og:description" content="On this website you can easily download UUP files from Windows Update servers.">
        <meta property="og:image" content="https://i.imgur.com/pGs1s3q.png">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2/dist/semantic.min.css">
        <link rel="stylesheet" href="shared/style.css">

        <script src="https://cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2/dist/semantic.min.js"></script>

        <title>UUP dump</title>
    </head>
    <body>
        <div class="ui attached black segment">
            <div class="ui container">
                <h1>UUP dump
                    <p class="ui left pointing black version label">
                        v'.$websiteVersion.'
                    </p>
                </h1>
            </div>
        </div>

        <div class="ui attached stackable menu">
            <div class="ui container">'.$navbarLink.'</div>
        </div>

        <div class="ui container">';
}

function styleLower() {
    global $websiteVersion;

    echo '<div class="footer">
                <div class="ui divider"></div>
                <p><i><b>UUP dump</b> v'.$websiteVersion.' &copy; '.date('Y').' UUP dump authors</i></p>
            </div>
        </div>
    </body>
</html>';
}
?>
