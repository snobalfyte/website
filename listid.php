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

header('Content-Type: text/plain');

$userAgent = $_SERVER['HTTP_USER_AGENT'];
$bannedAgents = array(
    'UUP dump downloader/0.3.0-alpha',
    'UUP dump downloader/0.4.0-alpha',
    'UUP dump downloader/0.5.0-alpha',
    'UUP dump downloader/1.0.0-beta.1',
    'UUP dump downloader/1.0.0-beta.2',
    'UUP dump downloader/1.0.0-beta.3',
    'UUP dump downloader/1.0.0-beta.4',
    'UUP dump downloader/1.0.0-beta.5',
    'UUP dump downloader/1.0.0-beta.6',
    'UUP dump downloader/1.0.0-beta.7',
    'UUP dump downloader/1.0.0-rc.1',
    'UUP dump downloader/1.0.0',
    'UUP dump downloader/1.1.0-alpha.1',
    'UUP dump downloader/1.1.0-alpha.2',
    'UUP dump downloader/1.1.0-alpha.3',
    'UUP dump downloader/1.1.0-alpha.4',
    'UUP dump downloader/1.1.0-alpha.5',
    'UUP dump downloader/1.1.0-alpha.6',
    'UUP dump downloader/1.1.0-alpha.7',
);

if(in_array($userAgent, $bannedAgents)) {
    echo "0||00000000-0000-0000-0000-000000000000|Old version of tool! Please update: https://0x0.st/sBa\n";
    die();
}

require_once 'api/listid.php';

$ids = uupListIds();
if(isset($ids['error'])) {
    die($ids['error']);
}

foreach($ids['builds'] as $val) {
    echo $val['build'];
    echo '|';
    echo $val['arch'];
    echo '|';
    echo $val['uuid'];
    echo '|';
    echo $val['title'];
    echo "\n";
}
?>
