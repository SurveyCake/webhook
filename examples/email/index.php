<?php

$decrypt = include __DIR__.'/../decrypt_config.php';
$config = include __DIR__.'/config.php';

$version = 'v0';
$svid = $_POST['svid'];
$hash = $_POST['hash'];
$WEBHOOK_QUERY_API = 'https://www.surveycake.com/webhook/'. $version. '/'. $svid. '/'. $hash;

// Require the Composer autoloader.
require 'vendor/autoload.php';

use Aws\Ses\SesClient;
date_default_timezone_set('Asia/Taipei');

/**
 * get the encrypted data
 */
$dat = file_get_contents($WEBHOOK_QUERY_API);

$json = openssl_decrypt(
	$dat,
	'AES-128-CBC',
	$decrypt['HASH'],
	false,
	$decrypt['IV']
);

$answerJson = json_decode($json);

/**
 * DOM of questions
 */
$questionTemplate = file_get_contents('./question.html');

$questionGroup = '';

foreach( $answerJson->result as $key => $value ) {
	$qs = $questionTemplate;
	$qs = preg_replace('/\$_\{QUESTION\}_\$/', strip_tags($value->subject), $qs);
	/**
	 * answer
	 */
	$answer = '';
	if( count($value->answer) == 1 ) {
		$answer = $value->answer[0];
	}
	else if( count($value->answer) > 1 ) {
		$answer = join(', ', $value->answer);
	}
	$qs = preg_replace('/\$_\{ANSWER\}_\$/', $answer, $qs);

	$questionGroup .= $qs;
}

/**
 * mail CONTENT
 */
$html = file_get_contents('./mail.html');
$html = preg_replace('/\$_\{TITLE\}_\$/', $answerJson->title, $html);
$html = preg_replace('/\$_\{QUESTION_GROUP\}_\$/', $questionGroup, $html);

/**
 * mail TITLE
 */
$title = '您的問卷 $_{TITLE}_$ 收到一筆新的回覆';
$title = preg_replace('/\$_\{TITLE\}_\$/', $answerJson->title, $title);

/**
 * mail body is completed,
 * NEXT: send the mail
 */
$sesMailArr = array(
	'Source' => '=?utf-8?B?'.base64_encode($config['MAILER_NAME']).'?= <'.$config['MAILER'].'>',
	'Message' => array(
		'Subject' => array(
			'Data' => $title,
			'Charset' => 'UTF-8',
		),
		'Body' => array(
			'Html' => array(
				'Data' => $html,
				'Charset' => 'UTF-8',
			),
		),
	),
	'ReplyToAddresses' => array($config['MAILER']),
	'ReturnPath' => $config['MAILER'],
	'Destination' => array(
		'ToAddresses' => explode(',', $config['MAIL_TO']),
	),
);

/**
 * prepare instance and execute
 */
$sesClient = SesClient::factory(array(
	'credentials' => [
		'key' => $config['SES_KEY'],
		'secret' => $config['SES_SEC'],
	],
	'region' => $config['SES_REGION'],
	'version' => '2010-12-01',
));

try {
	$result = $sesClient->sendEmail($sesMailArr);
} catch (Exception $e) {
	echo('The email was not sent. Error message: '.$e->getMessage()."\n");
}
