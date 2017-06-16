# SurveyCake Webhook

[繁中](https://github.com/SurveyCake/webhook/blob/master/README.md) ｜ English

First, enter the url to receive the request from our service.

![webhook url](./docs/en/webhook_url.jpg)

Once a new submit happens, SurveyCake calls your webhook url with a POST request (`url`).  

The _url_ we provide is an encrypted string, you need to decrypt to get the answer JSON.

![key](./docs/en/keys.jpg)

At your webhook file, you have to decrypt the data with the Hash and IV keys, we encrypt it by `AES-128-CBC` (zero-padding), here are samples:

- [PHP](https://github.com/SurveyCake/webhook/blob/master/decrypt.php)
- [NodeJs](https://github.com/SurveyCake/webhook/blob/master/decrypt.js)
- [Javascript](https://github.com/SurveyCake/webhook/blob/master/decrypt.html)
- [Swift](https://github.com/SurveyCake/webhook/blob/master/Decrypt.swift)
- [Java](https://github.com/SurveyCake/webhook/blob/master/Decrypt.java)

After decrypting, it's your time ~ :kissing_closed_eyes:

### Answer JSON

~~~javascript
{
	"svid": "SURVEY_ID",
	"title": "survey title",
	"submitTime": "2016-12-25 00:00:00",
	"result": [
		// ....
	]
}
~~~

`result` array contains all questions, like this:

~~~javascript
"result": [
	{
		"subject": "Do you like SurveyCake",
		"type": "TXTSHORT",
		"answer": [
			"Of course."
		]
	},
	{
		"subject": "Any suggestion ?",
		"type": "TXTSHORT",
		"answer": [
		]
	}
]
~~~
