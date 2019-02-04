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

// Website information
$websiteVersion = '3.18.0';
$requiredApi = '1.19.0';

require_once dirname(__FILE__).'/../api/shared/main.php';
function checkApi() {
    global $requiredApi;
    $apiVer = parseSemVer(uupApiVersion());
    $reqApi = parseSemVer($requiredApi);

    if($apiVer['major'] != $reqApi['major']) {
        fancyError('UNSUPPORTED_API');
        die();
    }

    if($apiVer['minor'] < $reqApi['minor']) {
        fancyError('UNSUPPORTED_API');
        die();
    }

    if($apiVer['minor'] == $reqApi['minor']) {
        if($apiVer['patch'] < $reqApi['patch']) {
            fancyError('UNSUPPORTED_API');
            die();
        }
    }
}

function parseSemVer($version) {
    $patchArray = explode('-', $version);
    $versionArray = explode('.', $patchArray[0]);
    if(isset($patchArray[1])) {
        $metadataArray = explode('+', $patchArray[1]);
    }

    $major = $versionArray[0];
    $minor = $versionArray[1];
    $patch = $versionArray[2];

    if(isset($metadataArray[0])) {
        $prerelease = $metadataArray[0];
    } else {
        $prerelease = null;
    }

    if(isset($metadataArray[1])) {
        $metadata = $metadataArray[1];
    } else {
        $metadata = null;
    }

    return array(
        'major' => $major,
        'minor' => $minor,
        'patch' => $patch,
        'prerelease' => $prerelease,
        'metadata' => $metadata,
    );
}

// Do check of API
checkApi();
?>
