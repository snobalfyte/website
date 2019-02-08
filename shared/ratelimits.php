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

function checkIfUserIsRateLimited($resource, $timeLimit = 10) {
    $clientIP = $_SERVER['REMOTE_ADDR'];
    $ipHash = hash('sha256', "ratelimits-$clientIP");

    $info = @file_get_contents('cache/'.$ipHash.'.json');
    $info = json_decode($info, 1);

    if(!isset($info['resource'])) {
        $info['resource'] = $resource;
    }

    if(!isset($info['lastAccess'])) {
        $info['lastAccess'] = 0;
    }

    $lastAccess = $info['lastAccess'];
    $accessedRes = $info['resource'];
    $blockAccessTime = $lastAccess + $timeLimit;

    if($blockAccessTime > time() && $accessedRes != $resource) {
        return true;
    }

    $info['lastAccess'] = time();
    $info['resource'] = $resource;

    @file_put_contents('cache/'.$ipHash.'.json', json_encode($info)."\n");

    if($lastAccess + 1 > time() && $accessedRes == $resource) {
        return true;
    }

    return false;
}
