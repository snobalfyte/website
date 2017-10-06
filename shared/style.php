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

$websiteVersion = '3.0.0-beta.5';

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

function fancyError($errorCode = 'ERROR', $pageType = 'home', $moreText = 0) {
    switch ($errorCode) {
        case 'ERROR':
            $errorFancy = 'Generic error.';
            break;
        case 'NO_FILEINFO_DIR':
            $errorFancy = 'The <i>fileinfo</i> directory does not exist.';
            break;
        case 'UNKNOWN_ARCH':
            $errorFancy = 'Unknown processor architecture.';
            break;
        case 'UNKNOWN_RING':
            $errorFancy = 'Unknown ring.';
            break;
        case 'UNKNOWN_FLIGHT':
            $errorFancy = 'Unknown flight.';
            break;
        case 'UNKNOWN_COMBINATION':
            $errorFancy = 'The flight and ring combination is not correct. Skip ahead is only supported for Insider Fast ring.';
            break;
        case 'ILLEGAL_BUILD':
            $errorFancy = 'Specified build number is less than 15063 or larger than 65536.';
            break;
        case 'EMPTY_FILELIST':
            $errorFancy = 'Server has returned an empty list of files.';
            break;
        case 'UNSUPPORTED_LANG':
            $errorFancy = 'Specified language is not supported.';
            break;
        case 'UNSPECIFIED_LANG':
            $errorFancy = 'Language was not specified.';
            break;
        case 'UNSUPPORTED_EDITION':
            $errorFancy = 'Specified edition is not supported.';
            break;
        case 'UNSUPPORTED_COMBINATION':
            $errorFancy = 'The language and edition combination is not correct.';
            break;
        case 'UPDATE_INFORMATION_NOT_EXISTS':
            $errorFancy = 'Information about specified update doest not exist in database.';
            break;
        case 'KEY_NOT_EXISTS':
            $errorFancy = 'Specified key does not exist in update information';
            break;
        case 'UNSPECIFIED_UPDATE':
            $errorFancy = 'Update ID was not specified.';
            break;
        case 'ARIA2_SUPPORT_NOT_ENABLED':
            $errorFancy = 'Support of aria2 has been disabled.';
            break;
        case 'ARIA2_CONNECT_FAIL':
            $errorFancy = 'Could not connect to aria2 RPC.';
            break;
        case 'ARIA2_RPC_ERROR':
            $errorFancy = 'Aria2 RPC has returned an error.';
            break;
        default:
            $errorFancy = '<i>Error message is not available.</i><br><br>'.$errorCode;
            break;
    }

    if($moreText) {
        $errorFancy = $errorFancy.'<br>'.$moreText;
    }

    styleUpper($pageType);

    echo '<div class="ui horizontal divider">
    <h3><i class="warning icon"></i>Request not successful</h3>
</div>
<div class="ui negative icon message">
    <i class="remove circle icon"></i>
    <div class="content">
        <div class="header">An error has occurred</div>
        <p>We have encountered an error while processing your request.<br>
        '.$errorFancy.'</p>
    </div>
</div>';

    styleLower();
}
?>
