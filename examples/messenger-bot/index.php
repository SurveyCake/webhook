<?php

$decrypt = include __DIR__.'/../decrypt_config.php';
$config = include __DIR__.'/config.php';

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

/**
 * verify Fabebook Messenger
 */
if( !empty($_GET['hub_mode']) && $_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === $config['verify_token'] ) {

	echo $_REQUEST['hub_challenge'];

}
else {

	/**
	 * send a message
	 */
	exec(
		('curl -X POST -H "Content-Type: application/json" -d \'{
			"recipient": {
				"id": "USER_ID"
			},
			"message": {
				"text": "Here comes a new submit !"
			}
		}\' "https://graph.facebook.com/v2.6/me/messages?access_token='.$config['verify_token'].'"')
	);

}
