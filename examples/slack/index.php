<?php

$decrypt = include __DIR__.'/../decrypt_config.php';

/**
 * get the encrypted data
 */
$dat = file_get_contents($_POST['url']);

$json = openssl_decrypt(
	$dat,
	'AES-128-CBC',
	$decrypt['HASH'],
	false,
	$decrypt['IV']
);

$answerJson = json_decode($json);

exec(
	('curl -X POST --data-urlencode \'payload={
		"channel": "#YOUR_CHANNEL",
		"text": "Here comes a new submit !",
	}\' https://hooks.slack.com/...')
);
