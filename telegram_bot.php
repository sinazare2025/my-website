<?php
$botToken = '7809044163:AAGgXroRwArowKb6mjmaRPRzgwSZulKyFGI';
$adminId = '6655509748';

// تابع ارسال پیام به ادمین
function sendToAdmin($message) {
    global $botToken, $adminId;
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $adminId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// دریافت اطلاعات فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($password === 'mavad28') {
        // ارسال اطلاعات به ادمین
        $message = "🔔 ثبت نام جدید:\n\n";
        $message .= "👤 نام: {$name}\n";
        $message .= "📱 شماره تماس: {$phone}";
        
        sendToAdmin($message);
        
        // پاسخ موفقیت‌آمیز
        echo json_encode(['success' => true, 'message' => 'ثبت نام با موفقیت انجام شد']);
    } else {
        // پاسخ خطا
        echo json_encode(['success' => false, 'message' => 'رمز عبور اشتباه است']);
    }
}
?> 