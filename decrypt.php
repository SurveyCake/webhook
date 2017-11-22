<?php

$key = 'HASH_KEY';
$iv = 'IV_KEY';
$version = 'v0';
$svid = $_GET['svid'];
$hash = $_GET['hash'];
$url = 'https://www.surveycake.com/webhook/'. $version. '/'. $svid. '/'. $hash;

$dat = file_get_contents($url);

$json = openssl_decrypt(
    $dat,
    'AES-128-CBC',
    $key,
    false,
    $iv
);

// user answer json
print_r( json_decode($json) );
