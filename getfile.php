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
$file = isset($_GET['file']) ? strtolower($_GET['file']) : null;
$aria2 = isset($_GET['aria2']) ? $_GET['aria2'] : 0;

if(empty($updateId)) die('Unspecified update id');
if(empty($file)) die('Unspecified file');

require_once 'api/get.php';

$cacheHash = hash('sha1', strtolower("upd-$updateId-0-0"));
$cached = 0;

if(file_exists('cache/'.$cacheHash.'.json')) {
    $files = @file_get_contents('cache/'.$cacheHash.'.json');
    $files = json_decode($files, 1);

    if(isset($files['content']['error'])) {
        fancyError($files['error'], 'downloads');
        die();
    }

    if(!empty($files['content']['files']) && ($files['expires'] > time())) {
        $cached = 1;
        $files = $files['content'];
    } else {
        $cached = 0;
        unset($files);
    }
}

if(!$cached) {
    $files = uupGetFiles($updateId, 0, 0);
    if(isset($files['error'])) {
        fancyError($files['error'], 'downloads');
        die();
    }

    $cache = array(
        'expires' => time()+90,
        'content' => $files,
    );

    @file_put_contents('cache/'.$cacheHash.'.json', json_encode($cache)."\n");
}

$files = $files['files'];
$filesKeys = array_keys($files);

if(!isset($files[$file]['url'])) {
    die('We couldn\'t find file '.$file);
}

if($aria2) {
    header('Content-Type: text/plain');
    echo $files[$file]['url']."\n";
    echo '  out='.$file."\n";
    echo '  checksum=sha-1='.$files[$file]['sha1']."\n\n";
    die();
}

$url = $files[$file]['url'];
header('Location: '.$url);
echo '<h1>Moved to <a href="'.$url.'">here</a>.';
?>
