# SurveyCake Webhook

First, enter the url to receive the request from our service.

![webhook url](./docs/webhook_url.png)

Once a new submit happens, SurveyCake calls your webhook url with a POST request (`url`).  

The _url_ we provide is an encrypted string, you need to decrypt to get the answer JSON.

![key](./docs/key.png)

At your webhook file, you have to decrypt the data with the Hash and IV keys, we encrypt it by `AES-128-CBC`, here are [PHP](https://github.com/SurveyCake/webhook/blob/master/decrypt.php) and [NodeJs](https://github.com/SurveyCake/webhook/blob/master/decrypt.js) samples.

After decrypting, it's your time ~ :kissing_closed_eyes:
