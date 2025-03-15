import requests
from flask import Flask, request, jsonify

app = Flask(__name__)

# توکن ربات تلگرام شما
TELEGRAM_BOT_TOKEN = '7364742519:AAEwsu7IFsQdpMuVMQKphiTvYbx_RpdY1VA'
# شناسه چت شما (ایدی عددی شما در تلگرام)
TELEGRAM_CHAT_ID = 'YOUR_CHAT_ID'

@app.route('/submit-contact', methods=['POST'])
def submit_contact():
    data = request.json
    name = data.get('name')
    phone = data.get('phone')
    password = data.get('password')

    if password != 'sos':
        return jsonify({'success': False, 'message': 'رمز اشتباه است!'})

    # ارسال اطلاعات به ربات تلگرام
    message = f"اطلاعات جدید:\nنام: {name}\nشماره تلفن: {phone}"
    url = f"https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}/sendMessage"
    payload = {
        'chat_id': TELEGRAM_CHAT_ID,
        'text': message
    }
    response = requests.post(url, json=payload)

    if response.status_code == 200:
        return jsonify({'success': True, 'message': 'اطلاعات شما با موفقیت ارسال شد!'})
    else:
        return jsonify({'success': False, 'message': 'خطا در ارسال اطلاعات!'})

if __name__ == '__main__':
    app.run(debug=True)