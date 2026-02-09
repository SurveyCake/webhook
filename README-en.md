# SurveyCake Webhook

[繁中](./README.md) | [English](./README-en.md)

- [Introduction](#introduction)
- [Process](#process)
- [Testing Tools](#testing-tools)
- [Q & A](#q--a)


## Introduction

With the SurveyCake Webhook, you can develop your own API to trigger specific actions based on the responses collected from a survey. This document outlines the integration process and the method for decrypting answers.

SurveyCake Webhook delivers data using a FIFO (First-In, First-Out) mechanism. In cases of high response volume, there may be delivery delays that affect real-time responsiveness. However, rest assured that data accuracy and integrity will not be affected.
If your use case requires real-time processing, we recommend evaluating your scenario in advance or contacting us for assistance in finding the most suitable solution.


## Process

- [**Step 1. URL setup**](#step-1-url-setup)
- [**Step 2. API Visit**](#step-2-api-visit)
- [**Step 3. Answer inquiry**](#step-3-answer-inquiry)
- [**Step 4. Answer decryption**](#step-4-answer-decryption)
- [**Step 5. Use of Information**](#step-5-use-of-information)

![surveycake webhook flow](./docs/webhook_flow.png)

---

### Step 1. URL setup

SurveyCake offers two types of URL settings that facilitate behavior trigger based on survey response: `Notification > Webhook` and `Custom Thank You`：

- `Notification > Webhook`
	- After survey answer is entered, SurveyCake will use **POST** request to access specified Webhook URL.
	- Applicable to backend API.
- `Custom Thank You`
	- After survey answer is entered, SurveyCake will use **GET** request to import necessary variables and redirect to your designated thank you page.
	- Applicable to frontend script


The following introduction is based on `Webhook URL`. For Custom Thank You, please go to section on [Testing Tools](#testing-tools) .

First, set up a `Webhook URL` in backend data system to receive our notifications.

![webhook url](./docs/en/webhook_url.jpg)

---

### Step 2. API Visit

- Whenever new answer is entered in survey, we will use POST request with `svid` and `hash` variables and send to specified Webhook URL.
- It is necessary to use `Webhook Query API` with `svid` and `hash` combination in the following format:
	- <https://{SURVEYCAKE_DOMAIN}/webhook/{VERSION}/{SVID}/{HASH}>
- Please use `v0` for VERSION as of now.

##### 👉 Webhook Query API example 👈

- POST svid: `yPZQe`
- POST hash: `5fd521e89436c471155f39de9c05bf4c`

~~~
https://{SURVEYCAKE_DOMAIN}/webhook/v0/yPZQe/5fd521e89436c471155f39de9c05bf4c
~~~

---

### Step 3. Answer inquiry

Access combined `Webhook Query API` to obtain `encrypted answers`.

##### 👉 Encrypted answers example 👈

~~~
C8jl3+0MLRWZAQtvzcbMJfMdE9F/CkH3qeQd93CdWntbFMk+mWOvSSsE65g5U4Sj/26btUWunpV1Gk9uM1Ltyk+RpqFC+Ve2d8uExGFortYHUuZ32NMeJd1h1DqDJpJy/1epiYMXSDFOEyJUIE1X8zamJAi6D0R5IwADXLVw315PW6B7t+IejkKJNrjlL6cgtI8B1PCAh58oMUQydrJd73zRY4f9O4yC5ZNdg4nloVR4qYWyFkFZOOCE6yExtnMzV/gg4e9gnlYAPb31Wlb3Scjl2akaiO8G78OBWa0r5cmN3MmLQ0NcahViUqOdJ+8v+jPwzh1wIflIuho+JyrgoQ==
~~~

---

### Step 4. Answer decryption

`Encrypted answers` must first be decrypted with `Hash key` and `IV key` for results to be readable JSON. Hash key and IV Key can be found in backend data system of SurveyCake. Screenshots are as follows.

![key](./docs/en/keys.jpg)

We use `AES-128-CBC` (PKCS#7 padding) for encryption, therefore please use `AES-128-CBC` (PKCS#7 padding) for decryption. Other methods will not generate correct information. The following are decryption examples in several languages:

- [Javascript](./decrypt/decrypt.html)
	- Use [crypto-js](https://github.com/brix/crypto-js)
	- We also provide [Javascript ES5 Example](./decrypt/decrypt-es5.html)
- [PHP](./decrypt/decrypt.php)
	- Use  [openssl_decrypt](http://php.net/manual/en/function.openssl-decrypt.php)
- [NodeJs](./decrypt/decrypt.js)
	- Use  [crypto](https://nodejs.org/api/crypto.html)
- [Swift](./decrypt/Decrypt.swift)
	- Use  `CommonCrypto` library
- [Java](./decrypt/Decrypt.java)
	- Use   [javax.crypto](https://developer.android.com/reference/javax/crypto/package-summary)

##### 👉 Decrypted answers example 👈

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
			"label": "",
			"alias": "",
			"answer": [
				"SurveyCake Marketing"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": null
		},
		{
			"subject": "Gender",
			"type": "CHOICEONE",
			"sn": 1,
			"label": "",
			"alias": "",
			"answer": [
				"Male"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"column": 2,
				"column_m": 1
			}
		}
	]
}
~~~


### Step 5. Use of Information

After decryption is complete, it is possible to write Webhook logic and trigger other behavior. For example:

- Write additional database
- Send email
- Go from Webhook to integrate another service (e.g. slack)
- Google Spreadsheet

Examples can be found in [examples](./examples/) folder.


## Testing Tools

SurveyCake offers Webhook Answer Preview testing tool that uses `Custom Thank You` settings for easy access to answer format.

- Github Repo: https://github.com/SurveyCake/webhook-answer-preview
- Demo: https://surveycake.github.io/webhook-answer-preview/


## Q & A

### 1. What format are answers in?

Each decrypted answer is in JSON format and contains `Survey Id`, `Survey Title`, `Submit Time` and `Result`.

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

`Result` uses an array to include all questions and answers. See the following table for details:

| Key | Definition | Notes |
| -- | -- | -- |
| subject | Question title | |
| type | Question type | |
| sn | Question serial number | |
| label | Question label | For labeled questions |
| alias | Question alias | For aliased questions |
| answer | Answers | |
| otherAnswer | Other answers | For constant sum questions |
| answerLabel | Answer labels | For labeled answers |
| answerAlias | Answer aliases | For aliased answers |
| extras | Extra question info | |


Format example:


~~~javascript
"result": [
		{
			"subject": "Rich Text",
			"type": "QUOTE",
			"sn": 0,
			"label": "",
			"alias": "",
			"answer": [],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": []
		},
		{
			"subject": "Section Title",
			"type": "STATEMENT",
			"sn": 1,
			"label": "",
			"alias": "",
			"answer": [],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": []
		},
		{
			"subject": "Divider",
			"type": "DIVIDER",
			"sn": 2,
			"label": "",
			"alias": "",
			"answer": [],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": []
		},
		{
			"subject": "Single Line Text",
			"type": "TXTSHORT",
			"sn": 3,
			"label": "tag_text_short",
			"alias": "text_short",
			"answer": [
				"Single line text"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": null
		},
		{
			"subject": "Paragraph Text",
			"type": "TXTLONG",
			"sn": 4,
			"label": "tag_text_long",
			"alias": "text_long",
			"answer": [
				"Paragraph text\nParagraph text\nParagraph text"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": null
		},
		{
			"subject": "Encrypted Question",
			"type": "TXTSHORT",
			"sn": 5,
			"label": "tag_text_short_encrypt",
			"alias": "text_short_encrypt",
			"answer": [
				"Encrypted content"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"isPersonal": true
			}
		},
		{
			"subject": "Number",
			"type": "DIGITINPUT",
			"sn": 6,
			"label": "digit_input",
			"alias": "tag_digit_input",
			"answer": [
				"50"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": null
		},
		{
			"subject": "Constant Sum",
			"type": "CONSTANTSUM",
			"sn": 7,
			"label": "tag_constant_sum",
			"alias": "constant_sum",
			"answer": [
				"Constant sum content"
			],
			"otherAnswer": [
				"150"
			],
			"answerLabel": [
				""
			],
			"answerAlias": [
				""
			],
			"extras": {
				"showTotal": false,
				"totalLimit": null
			}
		},
		{
			"subject": "Multiple Choice",
			"type": "CHOICEONE",
			"sn": 8,
			"label": "tag_choice_one",
			"alias": "choice_one",
			"answer": [
				"Option 1"
			],
			"otherAnswer": [],
			"answerLabel": [
				"tag_option_1"
			],
			"answerAlias": [
				"option_1"
			],
			"extras": {
				"column": 2,
				"column_m": 1
			}
		},
		{
			"subject": "Checkboxes",
			"type": "CHOICEMULTI",
			"sn": 9,
			"label": "tag_choice_multi",
			"alias": "choice_multi",
			"answer": [
				"Option 1",
				"Option 2"
			],
			"otherAnswer": [],
			"answerLabel": [
				"tag_choice_multi_option_1",
				"tag_choice_multi_option_2"
			],
			"answerAlias": [
				"choice_multi_option_1",
				"choice_multi_option_2"
			],
			"extras": {
				"column": 2,
				"column_m": 1
			}
		},
		{
			"subject": "Single Choice Matrix",
			"type": "NEST",
			"sn": 10,
			"label": "tag_nest",
			"alias": "nest",
			"answer": [],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": []
		},
		{
			"subject": "Sub-question 1",
			"type": "NESTCHILD",
			"sn": 11,
			"label": "tag_sub_nest_1",
			"alias": "sub_nest_1",
			"answer": [
				"Neutral"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"sbj_opt_pair": null
			}
		},
		{
			"subject": "Sub-question 2",
			"type": "NESTCHILD",
			"sn": 13,
			"label": "tag_sub_nest_1",
			"alias": "sub_nest_2",
			"answer": [
				"Satisfied"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"sbj_opt_pair": null
			}
		},
		{
			"subject": "Checkbox Matrix",
			"type": "NEST_MULTI",
			"sn": 14,
			"label": "tag_nest_multi",
			"alias": "nest_multi",
			"answer": [],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": []
		},
		{
			"subject": "Sub-question 1",
			"type": "NESTCHILD_MULTI",
			"sn": 15,
			"label": "tag_sub_nest_multi_1",
			"alias": "sub_nest_multi_1",
			"answer": [
				"Neutral",
				"Agree"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"sbj_opt_pair": null
			}
		},
		{
			"subject": "Sub-question 2",
			"type": "NESTCHILD_MULTI",
			"sn": 16,
			"label": "tag_sub_nest_multi_2",
			"alias": "sub_nest_multi_2",
			"answer": [
				"Strongly Agree",
				"Agree"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"sbj_opt_pair": null
			}
		},
		{
			"subject": "Selection Based",
			"type": "PICKFROM",
			"sn": 17,
			"label": "tag_pick_from",
			"alias": "pick_from",
			"answer": [
				"Option 1"
			],
			"otherAnswer": [],
			"answerLabel": [
				"tag_option_1"
			],
			"answerAlias": [
				"option_1"
			],
			"extras": {
				"column": 2,
				"column_m": 1,
				"opt_action": "1",
				"unanswered_opts": false
			}
		},
		{
			"subject": "Date",
			"type": "DATEPICKER",
			"sn": 18,
			"label": "tag_date_picker",
			"alias": "date_picker",
			"answer": [
				"2026-02-09"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"date_ini": "2026-01-01",
				"date_end": "2026-12-31"
			}
		},
		{
			"subject": "Nested Dropdown",
			"type": "NESTED_DROPDOWN",
			"sn": 19,
			"label": "tag_nest_dropdown",
			"alias": "nest_dropdown",
			"answer": [
				"ASTON MARTIN,DB11 5.2 V12,DB11,2018,Gasoline"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"use_general_source": false
			}
		},
		{
			"subject": "Ranking",
			"type": "ITEMSORT",
			"sn": 20,
			"label": "tag_item_sort",
			"alias": "item_sort",
			"answer": [
				"Option 3",
				"Option 2",
				"Option 1"
			],
			"otherAnswer": [],
			"answerLabel": [
				"tag_item_sort_option_3",
				"tag_item_sort_option_2",
				"tag_item_sort_option_1"
			],
			"answerAlias": [
				"item_sort_option_3",
				"item_sort_option_2",
				"item_sort_option_1"
			],
			"extras": null
		},
		{
			"subject": "Slider",
			"type": "DIGITSLIDE",
			"sn": 21,
			"label": "tag_digit_slide",
			"alias": "digit_slide",
			"answer": [
				"70"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": null
		},
		{
			"subject": "Rating",
			"type": "RATINGBAR",
			"sn": 22,
			"label": "tag_rating_bar",
			"alias": "rating_bar",
			"answer": [
				"3"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"shape": "STAR"
			}
		},
		{
			"subject": "NPS",
			"type": "NPS",
			"sn": 23,
			"label": "tag_nps",
			"alias": "nps",
			"answer": [
				"8"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"leftTxt": "Not at all likely",
				"rightTxt": "Extremely likely",
				"isColorEnabled": false
			}
		},
		{
			"subject": "File Upload",
			"type": "FILEUPLOAD",
			"sn": 24,
			"label": "tag_file_upload",
			"alias": "file_upload",
			"answer": [
				"example.csv",
				"https://drive.google.com/uc?id=xxxxx&export=download"
			],
			"otherAnswer": [],
			"answerLabel": [],
			"answerAlias": [],
			"extras": {
				"file_type": [
					"spreadsheet"
				]
			}
		},
		{
			"subject": "Advanced Selection Based",
			"type": "ADVANCED_SELECTION_BASED",
			"sn": 25,
			"label": "",
			"alias": "",
			"answer": [
				"Option 1",
				"Other option 1",
				"Other option 2"
			],
			"otherAnswer": [],
			"answerLabel": [
				"tag_advanced_selection_option_1",
				"tag_advanced_selection_other_option_1",
				"tag_advanced_selection_other_option_2"
			],
			"answerAlias": [
				"advanced_selection_option_1",
				"advanced_selection_other_option_1",
				"advanced_selection_other_option_2"
			],
			"extras": {
				"column": 2,
				"column_m": 1,
				"opt_action": "1",
				"unanswered_opts": false,
				"advanced_selection_sbj_ref": [
					8
				],
				"advanced_selection_showing_source_image": false
			}
		}
	]
~~~

Subject Type：

#### Multiple choice
| Type | Question Types |
| -- | -- |
| CHOICEONE | Multiple Choice |
| CHOICEMULTI | Checkboxes |
| NEST | Single Choice Matrix |
| NESTCHILD | Single Choice Matrix (Sub-question) |
| NEST_MULTI | Checkbox Matrix |
| NESTCHILD_MULTI | Checkbox Matrix (Sub-question) |
| PICKFROM | Selection Based |
| DATEPICKER | Date |
| NESTED_DROPDOWN | Nested Dropdown |

#### Text input
| Type | Question Types |
| -- | -- |
| TXTSHORT | Single Line Text |
| TXTLONG | Paragraph Text |
| DIGITINPUT | Number |
| CONSTANTSUM | Constant Sum |

#### Rating
| Type | Question Types |
| -- | -- |
| ITEMSORT | Ranking |
| DIGITSLIDE | Slider |
| RATINGBAR | Rating |
| NPS | NPS (Net Promoter Score) |

#### Content & style
| Type | Question Types |
| -- | -- |
| QUOTE | Rich Text |
| STATEMENT | Section Title |
| DIVIDER | Divider |

#### File upload
| Type | Question Types |
| -- | -- |
| FILEUPLOAD | File Upload |

#### Enterprise-Only
| Type | Question Types |
| -- | -- |
| ADVANCED_SELECTION_BASED | Advanced Selection Based |


### 2. After editing survey, is it necessary to modify Webhook URL?

When writing Webhook URL, we recommend not using Webhook logic with answer array sequence. It is better to use sn as the basis for comparison.

When modifying survey titles and subject orders, the answer array sequence also changes. It might become necessary to adjust Webhook logic. In every survey, sn does not duplicate serial numbers. Therefore, sn remains the same in each subject regardless of sequence editing.


### 3. Will deleted subjects still appear in answers?

No, subjects will not appear in answers after deletion. We recommend to make sure data exists before starting.
