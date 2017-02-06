const crypto = require('crypto');
const fetch = require('node-fetch');

const key = 'HASH_KEY';
const iv = 'IV_KEY';

fetch('URL_PATH')
	.then((res) => res.text())
	.then((dat) => {

		const decipher = crypto.createDecipheriv(
			'AES-128-CBC',
			key,
			iv
		);

		decipher.setAutoPadding(false);

		const json = decipher.update(
			dat,
			'base64',
			'utf8'
		);

		// user answer json
		console.log(json);

	})
	.catch((err) => console.log(err));
