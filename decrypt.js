const crypto = require('crypto');
const fetch = require('node-fetch');

const key = 'HASH_KEY';
const iv = 'IV_KEY';

fetch('POST_URL_PATH')
	.then((res) => res.text())
	.then((dat) => {

		const decipher = crypto.createDecipheriv(
			'aes-128-cbc',
			key,
			iv
		);

		decipher.setAutoPadding(false);

		const json = decipher.update(
			Buffer.from(dat, 'base64'),
			'binary',
			'utf8'
		);

		// user answer json
		console.log(json);

	})
	.catch((err) => console.log(err));
