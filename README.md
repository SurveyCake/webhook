# SurveyCake Webhook

繁中 | [English](./README-en.md)

- [簡介](#簡介)
- [流程](#流程)
- [Q & A](#q--a)


## 簡介

透過 SurveyCake Webhook，你可以自行開發 API 去依據問卷蒐集到的答案進行個別的行為觸發。本文件將介紹 SurveyCake Webhook 串接流程及答案解密方法。


## 流程

- [**Step 1. 設定網址**](#step-1-設定網址)
- [**Step 2. 訪問 API**](#step-2-訪問-api)
- [**Step 3. 查詢答案**](#step-3-查詢答案)
- [**Step 4. 解密答案**](#step-4-解密答案)
- [**Step 5. 運用資料**](#step-5-運用資料)

![surveycake webhook flow](./docs/tw/webhook_flow.png)

---

### Step 1. 設定網址

SurveyCake 提供兩種網址設定，讓你可以針對填答的內容做額外的行為觸發；一為 `系統通知 > Webhook`，另一為 `自訂感謝頁`：

- `系統通知 > Webhook`
	- 使用者填答後，SurveyCake 將使用 **POST** request，訪問你所設定的 Webhook URL。
	- 適用於後端 API
- `自訂感謝頁`
	- 使用者填答後，SurveyCake 將使用 **GET** request 將所需參數帶入，並且跳轉至自訂感謝頁。
	- 適用於前端 Script

以下步驟將以 `Webhook URL` 做介紹，首先，請在後台設定一個 `Webhook URL` 來接收我們的通知。

![webhook url](./docs/tw/webhook_url.jpg)

---

### Step 2. 訪問 API

- 每當問卷有新的填答後，我們會使用 POST requrest 夾帶 `svid` & `hash` 參數送至你所設定的 Webhook URL 網址。
- 你必須使用取得的 `svid` & `hash` 組合成 `Webhook Query API`，格式如下：
	- <https://www.surveycake.com/webhook/{VERSION}/{SVID}/{HASH}>
- 版本號 (VERSION) 目前請使用 `v0`

##### 👉 Webhook Query API 範例 👈

- POST svid: `yPZQe`
- POST hash: `5fd521e89436c471155f39de9c05bf4c`

~~~
https://www.surveycake.com/webhook/v0/yPZQe/5fd521e89436c471155f39de9c05bf4c
~~~

---

### Step 3. 查詢答案

訪問組合好的 `Webhook Query API` 可以取得該次 `加密填答結果`。

##### 👉 加密填答結果 範例 👈

~~~
C8jl3+0MLRWZAQtvzcbMJfMdE9F/CkH3qeQd93CdWntbFMk+mWOvSSsE65g5U4Sj/26btUWunpV1Gk9uM1Ltyk+RpqFC+Ve2d8uExGFortYHUuZ32NMeJd1h1DqDJpJy/1epiYMXSDFOEyJUIE1X8zamJAi6D0R5IwADXLVw315PW6B7t+IejkKJNrjlL6cgtI8B1PCAh58oMUQydrJd73zRY4f9O4yC5ZNdg4nloVR4qYWyFkFZOOCE6yExtnMzV/gg4e9gnlYAPb31Wlb3Scjl2akaiO8G78OBWa0r5cmN3MmLQ0NcahViUqOdJ+8v+jPwzh1wIflIuho+JyrgoQ==
~~~

---

### Step 4. 解密答案

剛剛取得的 `加密填答結果`，必須透過`Hash key` & `IV key` 進行解密，才可以拿到可閱讀的填答結果 JSON。Hash key 及 IV Key 可以在 SurveyCake 後台找到，截圖如下。

![key](./docs/tw/keys.jpg)

我們使用 `AES-128-CBC` (zero-padding) 方式加密，所以請務必使用 `AES-128-CBC` (zero-padding) 進行解密，其他的解密方式，無法解出正確的資訊，以下是幾種語言的解密示範：

- [Javascript](./decrypt/decrypt.html)
	- 範例使用 [crypto-js](https://github.com/brix/crypto-js)
	- 我們也提供 [Javascript ES5 範例](./decrypt/decrypt-es5.html)
- [PHP](./decrypt/decrypt.php)
	- 範例使用 [openssl_decrypt](http://php.net/manual/en/function.openssl-decrypt.php)
- [NodeJs](./decrypt/decrypt.js)
	- 範例使用 [crypto](https://nodejs.org/api/crypto.html)
- [Swift](./decrypt/Decrypt.swift)
	- 範例使用 `CommonCrypto` library
- [Java](./decrypt/Decrypt.java)
	- 範例使用 [javax.crypto](https://developer.android.com/reference/javax/crypto/package-summary)

##### 👉 解密後答案 範例 👈

~~~json
{
	"svid": "yPZQe",
	"title": "Webhook Answer Demo",
	"submitTime": "2018-06-28 04:05:47",
	"result": [
		{
			"subject": "What's your name?",
			"type": "TXTSHORT",
			"sn": 0,
			"answer": ["SurveyCake Marketing"]
		},
		{
			"subject": "Gender",
			"type": "CHOICEONE",
			"sn": 1,
			"answer": ["Both"]
		}
	]
}
~~~


### Step 5. 運用資料

解密完成後，剩下的就交給你囉 :kissing_closed_eyes:


## Q & A

### 1. 填答結果會是什麼格式？

每份填答結果解密之後會是 JSON 格式，內容包含 `Survey Id`, `Survey Title`, `填答時間` 以及 `填答內容`。

~~~javascript
{
	"svid": "SURVEY ID",
	"title": "SURVEY TITLE",
	"submitTime": "2018-06-28 04:05:47",
	"result": [
		// ....
	]
}
~~~

`result` 是以陣列型態包含著所有的問題及答案，我們提供 `Subject Title`, `Subject Type`, `Subject Number`, `Answer`，格式範例如下：

~~~javascript
"result": [
	{
		"subject": "What's your name?",
		"type": "TXTSHORT",
		"sn": 0,
		"answer": ["SurveyCake Marketing"]
	},
	{
		"subject": "Gender",
		"type": "CHOICEONE",
		"sn": 1,
		"answer": ["Both"]
	}
]
~~~

### 2. 問卷如果編輯後，Webhook URL 是否也要跟著修改呢？

我們建議你在撰寫 Webhook URL 時，不要使用 answer 陣列順序撰寫 Webhook 邏輯，應該使用 sn 作為比對的依據較佳。

因為問卷修改了題目標題、題目順序後，answer 陣列的順序會變動，此時你可能就會需要調整 Webhook 邏輯，而 sn 為每個題目的在問卷內的不重複編號，所以無論題目怎麼編輯順序，同一個題目 sn 不會變動。


### 3. 刪除的題目還會出現在填答結果內嗎？

不會，刪除的題目，就不會出現在填答結果內了，所以撰寫邏輯時，建議先判斷資料是否存在後再使用。
