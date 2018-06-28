const crypto = require('crypto');
const fetch = require('node-fetch');

const key = 'HASH_KEY';
const iv = 'IV_KEY';

fetch('WEBHOOK_QUERY_API')
	.then((res) => res.text())
	.then((dat) => {

		const decipher = crypto.createDecipheriv(
			'AES-128-CBC',
			key,
			iv
		);

		let json = decipher.update(
			dat,
			'base64',
			'utf8'
		);

		json += decipher.final('utf8');

		// user answer json
		console.log(JSON.stringify(JSON.parse(json)));

	})
	.catch((err) => console.log(err));
