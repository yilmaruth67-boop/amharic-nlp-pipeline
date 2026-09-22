import requests

url = "http://localhost/amharic_nlp_pipeline/index.php"
payload = {"corpus": ["አዲስ አበባ", "መድኃኒት ቤት"]}
headers = {
    "Content-Type": "application/json",
    "Authorization": "Bearer amharic_secret_token_abc123"
}

response = requests.post(url, json=payload, headers=headers)
print(response.json())