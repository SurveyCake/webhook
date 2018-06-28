<?php

$version = 'v0';
$svid = $_POST['svid'];
$hash = $_POST['hash'];
$WEBHOOK_QUERY_API = 'https://www.surveycake.com/webhook/'. $version. '/'. $svid. '/'. $hash;

$key = 'HASH_KEY';
$iv = 'IV_KEY';

$dat = file_get_contents($WEBHOOK_QUERY_API);

$json = openssl_decrypt(
    $dat,
    'AES-128-CBC',
    $key,
    false,
    $iv
);

// user answer json
print_r( json_decode($json) );
