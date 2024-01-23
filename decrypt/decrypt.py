import requests
import base64
from Crypto.Cipher import AES

WEBHOOK_QUERY_API = 'WEBHOOK_QUERY_API'
KEY = b'KEY'
IV = b'IV'

response = requests.get(WEBHOOK_QUERY_API)
data = response.content

encrypted = base64.b64decode(data)
cipher = AES.new(KEY, AES.MODE_CBC, IV)
decrypted_data = cipher.decrypt(encrypted)

# user answer json
print(decrypted_data.decode())