const fetch = require('node-fetch');

exports.handler = async (event, context) => {
  // فقط درخواست‌های POST را قبول کن
  if (event.httpMethod !== 'POST') {
    return {
      statusCode: 405,
      body: JSON.stringify({ success: false, message: 'درخواست نامعتبر' })
    };
  }

  try {
    const data = JSON.parse(event.body);
    const { name, phone, password } = data;

    // بررسی فیلدهای خالی
    if (!name || !phone || !password) {
      return {
        statusCode: 400,
        body: JSON.stringify({ success: false, message: 'لطفاً تمام فیلدها را پر کنید' })
      };
    }

    // بررسی رمز عبور
    if (password !== 'mavad28') {
      return {
        statusCode: 401,
        body: JSON.stringify({ success: false, message: 'رمز عبور اشتباه است' })
      };
    }

    // ارسال پیام به تلگرام
    const message = `🔔 ثبت نام جدید:\n\n👤 نام: ${name}\n📱 شماره تماس: ${phone}`;
    const telegramResponse = await fetch(
      `https://api.telegram.org/bot7809044163:AAGgXroRwArowKb6mjmaRPRzgwSZulKyFGI/sendMessage`,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          chat_id: '6655509748',
          text: message,
          parse_mode: 'HTML'
        })
      }
    );

    if (!telegramResponse.ok) {
      throw new Error('خطا در ارسال پیام به تلگرام');
    }

    return {
      statusCode: 200,
      body: JSON.stringify({ success: true, message: 'ثبت نام با موفقیت انجام شد' })
    };
  } catch (error) {
    console.error('خطا:', error);
    return {
      statusCode: 500,
      body: JSON.stringify({ success: false, message: 'خطا در ثبت نام' })
    };
  }
}; 