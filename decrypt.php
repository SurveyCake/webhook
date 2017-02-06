<?php

$key = 'HASH_KEY';
$iv = 'IV_KEY';

$dat = file_get_contents($_POST['url']);

$json = openssl_decrypt(
	$dat,
	'AES-128-CBC',
	$key,
	false,
	$iv
);

// user answer json
print_r( json_decode($json) );
