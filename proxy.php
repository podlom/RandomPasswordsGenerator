<?php

/**
 * Created by PhpStorm
 * User: shtaras
 * Date: 10/29/19
 * Time: 19:29
 *
 * @author Taras Shkodenko <taras@shkodenko.com>
 */

function sendPostRequest($url, $data)
{
    error_log(__METHOD__ . ' +' . __LINE__ . ' $url: ' . var_export($url, true) . ', $data: ' . var_export($data, true));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // On dev server only!
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $resCode = curl_errno($ch);
    $result = curl_exec($ch);
    error_log(__FUNCTION__ . ' +' . __LINE__ . ' Request result: ' . var_export($result, true));
    if ($result === false) {
        if (curl_error($ch)) {
            error_log(__FUNCTION__ . ' +' . __LINE__ . ' Curl error: ' . var_export(curl_error($ch), true));
        }
        if ($resCode !== 0) {
            error_log(__FUNCTION__ . ' +' . __LINE__ . ' HTTP error code: ' . var_export($resCode, 1));
        }
    }
    return [
        'result' => $result,
        'code' => $resCode,
    ];
}

$type = '';
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $type = $_GET['type'];
    switch ($type) {
        case '1':
            $res = sendPostRequest('https://st-rndpwd.shkodenko.com/generate-password', [
                'HTTP_REFERER' => $_SERVER['HTTP_REFERER'],
                'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
                'REMOTE_ADDR' => base64_encode($_SERVER['REMOTE_ADDR']),
            ]);
            break;
        case '2':
            $res = sendPostRequest('https://st-rndpwd.shkodenko.com/copy-password', [
                'HTTP_REFERER' => $_SERVER['HTTP_REFERER'],
                'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
                'REMOTE_ADDR' => base64_encode($_SERVER['REMOTE_ADDR']),
            ]);
            break;
    }
    if (!empty($res)) {
        error_log( __FILE__ . ' +' . __LINE__ . ' result: ' . var_export($res, true) );
    }
}
