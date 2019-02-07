<?php
/*
TODO: PUT LICENSE
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
    return false;
}
