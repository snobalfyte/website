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

$updateId = isset($_GET['id']) ? $_GET['id'] : null;
$file = isset($_GET['file']) ? $_GET['file'] : null;
$aria2 = isset($_GET['aria2']) ? $_GET['aria2'] : 0;

if(empty($updateId)) die('Unspecified update id');
if(empty($file)) die('Unspecified file');

require_once 'api/get.php';

$files = uupGetFiles($updateId, 0, 0);
if(isset($files['error'])) {
    die($files['error']);
}

$files = $files['files'];
$filesKeys = array_keys($files);

if(!isset($files[$file]['url'])) {
    die('We couldn\'t find file '.$file);
}

if($aria2) {
    echo $files[$file]['url']."\n";
    echo '  out='.$file."\n";
    echo '  checksum=sha-1='.$files[$file]['sha1']."\n\n";
}

$url = $files[$file]['url'];
header('Location: '.$url);
echo '<h1>Moved to <a href="'.$url.'">here</a>.';
?>
