# SurveyCake Webhook

繁中 ｜ [English](https://github.com/SurveyCake/webhook/blob/master/README-en.md)

首先，請在後台設定一個網址來接收我們的通知。

![webhook url](./docs/tw/webhook_url.jpg)

每當問卷有新的填答後，我們會使用 POST requrest 夾帶 `svid` & `hash` 參數送至上面所指定的網址。

之後，你可以使用這兩個參數跟 <https://www.surveycake.com/webhook/{VERSION}/{SVID}/{HASH}> 查詢該次填答結果。

_版本號 (VERSION) 目前請使用 `v0`_

每一份填答結果是一段加密過後的字串，經過解密後可以拿到這份填答結果 JSON。

![key](./docs/tw/keys.jpg)

你必須使用這張圖中的 hash 及 IV 這兩組金鑰來進行解密，我們使用 `AES-128-CBC` (zero-padding) 方式加密，以下是幾種語言的示範：

- [PHP](https://github.com/SurveyCake/webhook/blob/master/decrypt.php)
- [NodeJs](https://github.com/SurveyCake/webhook/blob/master/decrypt.js)
- [Javascript](https://github.com/SurveyCake/webhook/blob/master/decrypt.html)
- [Swift](https://github.com/SurveyCake/webhook/blob/master/Decrypt.swift)
- [Java](https://github.com/SurveyCake/webhook/blob/master/Decrypt.java)

解密完成後，剩下的就交給你囉 :kissing_closed_eyes:

### 填答結果 JSON

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

`result` 是以陣列型態包含著所有的問題及答案，如下：

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
