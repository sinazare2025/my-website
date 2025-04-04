<?php
require_once 'config.php';

// تابع ارسال پیام به ادمین
function sendToAdmin($message) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => ADMIN_ID,
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
    $result = @file_get_contents($url, false, $context);
    
    if ($result === false) {
        error_log('خطا در ارسال پیام به تلگرام: ' . error_get_last()['message']);
        return false;
    }
    
    return true;
}

// دریافت اطلاعات فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($name) || empty($phone) || empty($password)) {
            sendJsonResponse(['success' => false, 'message' => 'لطفاً تمام فیلدها را پر کنید'], 400);
        }
        
        if ($password === 'mavad28') {
            // ذخیره اطلاعات کاربر در دیتابیس
            $db = new SQLite3(DB_FILE);
            $stmt = $db->prepare('INSERT INTO users (username, phone) VALUES (:username, :phone)');
            $stmt->bindValue(':username', $name);
            $stmt->bindValue(':phone', $phone);
            $stmt->execute();
            
            $userId = $db->lastInsertRowID();
            
            // ارسال اطلاعات به ادمین
            $message = "🔔 ثبت نام جدید:\n\n";
            $message .= "👤 نام: {$name}\n";
            $message .= "📱 شماره تماس: {$phone}";
            
            if (sendToAdmin($message)) {
                // ایجاد سشن برای کاربر
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $name;
                
                sendJsonResponse(['success' => true, 'message' => 'ثبت نام با موفقیت انجام شد']);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'خطا در ارسال اطلاعات'], 500);
            }
        } else {
            sendJsonResponse(['success' => false, 'message' => 'رمز عبور اشتباه است'], 401);
        }
    } catch (Exception $e) {
        error_log('خطا در ثبت نام: ' . $e->getMessage());
        sendJsonResponse(['success' => false, 'message' => 'خطا در ثبت نام'], 500);
    }
} else {
    sendJsonResponse(['success' => false, 'message' => 'درخواست نامعتبر'], 405);
}
?> 