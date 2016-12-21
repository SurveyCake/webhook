<?php

$key = 'HASH_KEY';
$iv = 'IV_KEY';

$dat = file_get_contents($_POST['url']);

$json = openssl_decrypt(
	base64_decode($dat),
	'aes-128-cbc',
	$key,
	true,
	$iv
);

// user answer json
print_r($json);
