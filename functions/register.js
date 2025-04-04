const fetch = require('node-fetch');

const BOT_TOKEN = 'YOUR_BOT_TOKEN';
const CHAT_ID = 'YOUR_CHAT_ID';
const PASSWORD = 'YOUR_PASSWORD';

exports.handler = async (event, context) => {
    // CORS headers
    const headers = {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Content-Type',
        'Access-Control-Allow-Methods': 'POST, OPTIONS',
        'Content-Type': 'application/json'
    };

    // Handle OPTIONS request
    if (event.httpMethod === 'OPTIONS') {
        return {
            statusCode: 200,
            headers
        };
    }

    // Only allow POST
    if (event.httpMethod !== 'POST') {
        return {
            statusCode: 405,
            body: JSON.stringify({ error: 'Method Not Allowed' }),
            headers
        };
    }

    try {
        const data = JSON.parse(event.body);
        
        // Validate input
        if (!data.name || !data.phone || !data.password) {
            return {
                statusCode: 400,
                body: JSON.stringify({ error: 'لطفا تمام فیلدها را پر کنید' }),
                headers
            };
        }

        // Check password
        if (data.password !== PASSWORD) {
            return {
                statusCode: 401,
                body: JSON.stringify({ error: 'رمز عبور اشتباه است' }),
                headers
            };
        }

        // Send message to Telegram
        const message = `ثبت نام جدید:\nنام: ${data.name}\nشماره تماس: ${data.phone}`;
        const telegramUrl = `https://api.telegram.org/bot${BOT_TOKEN}/sendMessage`;
        
        const telegramResponse = await fetch(telegramUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                chat_id: CHAT_ID,
                text: message,
                parse_mode: 'HTML'
            })
        });

        if (!telegramResponse.ok) {
            throw new Error('خطا در ارسال پیام به تلگرام');
        }

        return {
            statusCode: 200,
            body: JSON.stringify({ 
                success: true,
                message: 'ثبت نام با موفقیت انجام شد',
                redirect: '/chat.html'
            }),
            headers
        };

    } catch (error) {
        console.error('Error:', error);
        return {
            statusCode: 500,
            body: JSON.stringify({ error: 'خطا در ثبت نام' }),
            headers
        };
    }
}; 