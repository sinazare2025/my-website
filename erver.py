from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/submit-contact', methods=['POST'])
def submit_contact():
    data = request.json
    name = data.get('name')
    phone = data.get('phone')
    password = data.get('password')

    if password != 'sos':
        return jsonify({'success': False, 'message': 'رمز اشتباه است!'})

    # در اینجا می‌توانید اطلاعات را به ربات تلگرام ارسال کنید یا در دیتابیس ذخیره کنید.
    print(f"نام: {name}, شماره تلفن: {phone}")

    return jsonify({'success': True, 'message': 'اطلاعات شما با موفقیت ارسال شد!'})

if __name__ == '__main__':
    app.run(debug=True)