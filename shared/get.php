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

// Change this to disable aria2 local downloads support
$aria2SupportEnabled = 1;

function sortBySize($a, $b) {
    global $files;

    if ($files[$a]['size'] == $files[$b]['size']) {
        return 0;
    }

    return ($files[$a]['size'] < $files[$b]['size']) ? -1 : 1;
}

function sendToAria2($url, $name, $sha1, $dir) {
    global $aria2SupportEnabled;

    if(!$aria2SupportEnabled) {
        fancyError('ARIA2_SUPPORT_NOT_ENABLED', 'downloads');
        die();
    }

    $data = array(
        'jsonrpc' => '2.0',
        'id' => null,
        'method' => 'aria2.addUri',
        'params' => array(
            'token:MfR3lC7EvOM5Ji1RhDIgPexj81B71BvJ',
            array(
                $url,
            ),
            array(
                'dir' => $dir,
                'out' => $name,
                'checksum' => 'sha-1='.$sha1,
            ),
        ),
    );

    $postData = json_encode($data);
    $req = curl_init('http://127.0.0.1:24701/jsonrpc');

    curl_setopt($req, CURLOPT_HEADER, 0);
    curl_setopt($req, CURLOPT_POST, 1);
    curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($req, CURLOPT_POSTFIELDS, $postData);

    $out = curl_exec($req);
    curl_close($req);

    if(empty($out)) {
        fancyError('ARIA2_CONNECT_FAIL', 'downloads');
        die();
    }

    $out = json_decode($out, true);
    if(isset($out['error'])) {
        $errorMsg = '<br><i>'.$out['error']['message'].'</i>';
        fancyError('ARIA2_RPC_ERROR', 'downloads', $errorMsg);
        die();
    }
}
?>
