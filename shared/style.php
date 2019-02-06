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

require_once dirname(__FILE__).'/main.php';

function styleUpper($pageType = 'home', $subtitle = '') {
    global $websiteVersion;
    
    if($subtitle) {
        $title = "UUP dump: $subtitle";
    } else {
        $title = 'UUP dump';
    }

    $enableDarkMode = 0;
    if(isset($_COOKIE['Dark-Mode'])) {
        if($_COOKIE['Dark-Mode'] == 1) {
            $enableDarkMode = 1;
            setcookie('Dark-Mode', 1, time()+2592000);
        }
    }

    if(isset($_GET['dark'])) {
        if($_GET['dark'] == 1) {
            setcookie('Dark-Mode', 1, time()+2592000);
            $enableDarkMode = 1;
        } elseif($_GET['dark'] == 0) {
            setcookie('Dark-Mode');
            $enableDarkMode = 0;
        }
    }

    $baseUrl = '';
    if(isset($_SERVER['HTTPS'])) {
        $baseUrl .= 'https://';
    } else {
        $baseUrl .= 'http://';
    }

    $baseUrl .=  $_SERVER['HTTP_HOST'];

    $params = '';
    $separator = '?';
    foreach($_GET as $key => $val) {
        if($key == 'dark') continue;
        $params .= $separator.$key.'='.$val;
        $separator = '&';
    }
    $params .= $separator;

    $shelf = explode('?', $_SERVER['REQUEST_URI']);
    $url = $baseUrl.$shelf[0].$params;
    unset($key, $val, $index, $params, $shelf);

    if($enableDarkMode) {
        $darkMode = '<link rel="stylesheet" href="shared/darkmode.css">'."\n";
        $darkSwitch = '<a class="item" href="'.$url.'dark=0"><i class="eye slash icon"></i>Light mode</a>';
    } else {
        $darkMode = '';
        $darkSwitch = '<a class="item" href="'.$url.'dark=1"><i class="eye icon"></i>Dark mode</a>';
    }


    switch ($pageType) {
        case 'home':
            $navbarLink = '<a class="active item" href="./"><i class="home icon"></i>Home</a>'.
                          '<a class="item" href="./known.php"><i class="download icon"></i>Downloads</a>';
            break;
        case 'downloads':
            $navbarLink = '<a class="item" href="./"><i class="home icon"></i>Home</a>'.
                          '<a class="active item"><i class="download icon"></i>Downloads</a>';
            break;
        default:
            $navbarLink = '<a class="active item" href="./">Home</a>';
            break;
    }

    $navbarRight = $darkSwitch.'<a class="item" href="https://gitlab.com/uup-dump"><i class="code icon"></i>Source code</a>';

    echo <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta property="og:title" content="$title">
        <meta property="og:type" content="website">
        <meta property="og:description" content="Download UUP files from Windows Update servers with ease. This project is not affiliated with Microsoft Corporation.">
        <meta property="og:image" content="$baseUrl/shared/img/icon.png">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2/dist/semantic.min.css">
        <link rel="stylesheet" href="shared/style.css">
        $darkMode
        <script src="https://cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2/dist/semantic.min.js"></script>

        <title>$title</title>

        <script>
            function sidebar() {
                $('.ui.sidebar').sidebar('toggle');
            }
        </script>
    </head>
    <body>
        <div class="ui sidebar inverted vertical menu">
            <div class="ui container">
                $navbarLink $navbarRight
            </div>
        </div>
        <div class="pusher">
            <div class="page-header">
                <div class="ui title container">
                    <h1>
                        <img src="shared/img/logo.svg" class="logo">UUP dump
                        <p class="version">
                            v$websiteVersion
                        </p>
                    </h1>
                </div>

                <div class="ui one column grid">
                    <div class="ui attached secondary inverted menu tablet computer only column">
                        <div class="ui container">
                            $navbarLink
                            <div class="right menu">
                                $navbarRight
                            </div>
                        </div>
                    </div>
                    <div class="ui attached secondary inverted menu mobile only column">
                        <div class="ui container">
                            <a class="item" href="javascript:void(0)" onClick="sidebar();"><i class="bars icon"></i>Menu</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui container">
HTML;
}

function styleLower() {
    global $websiteVersion;
    $api = uupApiVersion();
    $year = date('Y');
    
    echo <<<HTML
                <div class="footer">
                    <div class="ui divider"></div>
                    <p><i>
                        <b>UUP dump</b> v$websiteVersion
                        (<b>API</b> v$api)
                        &copy; $year UUP dump authors.

                        <span class="info">
                            This project is not affiliated with Microsoft Corporation.
                            Windows is a registered trademark of Microsoft Corporation.
                        </span>
                    </i></p>
                </div>
            </div>
        </div>
    </body>
</html>
HTML;
}

function fancyError($errorCode = 'ERROR', $pageType = 'home', $moreText = 0) {
    switch ($errorCode) {
        case 'ERROR':
            $errorFancy = 'Generic error.';
            break;
        case 'UNSUPPORTED_API':
            $errorFancy = 'Installed API version is not compatible with this version of UUP dump.';
            break;
        case 'NO_FILEINFO_DIR':
            $errorFancy = 'The <i>fileinfo</i> directory does not exist.';
            break;
        case 'NO_BUILDS_IN_FILEINFO':
            $errorFancy = 'The <i>fileinfo</i> database does not contain any build.';
            break;
        case 'SEARCH_NO_RESULTS':
            $errorFancy = 'No builds could be found for specified query.';
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
            $errorFancy = 'Specified build number is less than 9841 or larger than '. (PHP_INT_MAX-1) .'.';
            break;
        case 'ILLEGAL_MINOR':
            $errorFancy = 'Specified build minor is incorrect.';
            break;
        case 'NO_UPDATE_FOUND':
            $errorFancy = 'Server did not return any updates.';
            break;
        case 'XML_PARSE_ERROR':
            $errorFancy = 'Parsing of response XML has failed. This may indicate a temporary problem with Microsoft servers. Try again later.';
            break;
        case 'EMPTY_FILELIST':
            $errorFancy = 'Server has returned an empty list of files.';
            break;
        case 'NO_FILES':
            $errorFancy = 'There are no files available for your selection.';
            break;
        case 'NO_METADATA_ESD':
            $errorFancy = 'There are no metadata ESD files available for your selection.';
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
        case 'NOT_CUMULATIVE_UPDATE':
            $errorFancy = 'Selected update does not contain Cumulative Update.';
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
        case 'INCORRECT_ID':
            $errorFancy = 'Specified Update ID is not a correct Update ID. Please make sure that Update ID is a correct Update ID.';
            break;
        default:
            $errorFancy = '<i>Error message is not available.</i><br><br>'.$errorCode;
            break;
    }

    if($moreText) {
        $errorFancy = $errorFancy.'<br>'.$moreText;
    }

    http_response_code(500);
    styleUpper($pageType, 'Error');

    echo <<<ERROR
<div class="ui horizontal divider">
    <h3><i class="warning icon"></i>Request not successful</h3>
</div>
<div class="ui negative icon message">
    <i class="remove circle icon"></i>
    <div class="content">
        <div class="header">An error has occurred</div>
        <p>We have encountered an error while processing your request.<br>
        $errorFancy</p>
    </div>
</div>
ERROR;

    styleLower();
}

function styleNoPackWarn() {
    echo <<<INFO
<div class="ui icon warning message">
    <i class="warning circle icon"></i>
    <div class="content">
        <div class="header">Generated pack not available</div>
        <p>The update you are attempting to download does not have a generated
        pack that provides full information about available languages, editions
        and files. The fallback pack will be used that may not provide the
        correct information. If download fails because of this, please wait for
        the automatically generated pack to become available.</p>
    </div>
</div>

INFO;
}
