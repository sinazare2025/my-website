from flask import Flask, request, jsonify
import requests
import os

app = Flask(__name__)

# توکن ربات تلگرام باید در متغیرهای محیطی تنظیم شود
TELEGRAM_BOT_TOKEN = os.getenv('TELEGRAM_BOT_TOKEN')
TELEGRAM_CHAT_ID = os.getenv('TELEGRAM_CHAT_ID')

@app.route('/submit-contact', methods=['POST'])
def submit_contact():
    data = request.json
    name = data.get('name')
    phone = data.get('phone')
    message = data.get('message')

    if not all([name, phone, message]):
        return jsonify({'success': False, 'message': 'لطفاً تمام فیلدها را پر کنید!'})

    try:
        # ارسال اطلاعات به ربات تلگرام
        message_text = f"پیام جدید از وبسایت:\nنام: {name}\nتلفن: {phone}\nپیام: {message}"
        url = f"https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}/sendMessage"
        payload = {
            'chat_id': TELEGRAM_CHAT_ID,
            'text': message_text
        }
        response = requests.post(url, json=payload)

        if response.status_code == 200:
            return jsonify({'success': True, 'message': 'پیام شما با موفقیت ارسال شد!'})
        else:
            return jsonify({'success': False, 'message': 'خطا در ارسال پیام به تلگرام!'})
    except Exception as e:
        return jsonify('success': False, 'message': f'خطای سرور: {str(e)}'})

if __name__ == '__main__':
    app.run(debug=True)