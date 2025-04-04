<?php
session_start();

// بررسی ورود کاربر
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.html');
    exit;
}

// اتصال به دیتابیس
$db = new SQLite3('chat.db');

// ایجاد جدول پیام‌ها اگر وجود نداشته باشد
$db->exec('CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id TEXT,
    username TEXT,
    message TEXT,
    file_url TEXT,
    file_type TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)');

// ایجاد جدول کاربران اگر وجود نداشته باشد
$db->exec('CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id TEXT UNIQUE,
    username TEXT,
    profile_image TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)');

// دریافت پیام‌ها
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $messages = $db->query('SELECT * FROM messages ORDER BY created_at DESC LIMIT 50');
    $result = [];
    while ($row = $messages->fetchArray(SQLITE3_ASSOC)) {
        $result[] = $row;
    }
    echo json_encode($result);
}

// ارسال پیام جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['message'])) {
        $stmt = $db->prepare('INSERT INTO messages (user_id, username, message) VALUES (:user_id, :username, :message)');
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->bindValue(':username', $_SESSION['username']);
        $stmt->bindValue(':message', $data['message']);
        $stmt->execute();
    }
    
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $fileType = $file['type'];
        $fileName = uniqid() . '_' . $file['name'];
        $uploadDir = 'uploads/';
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            $stmt = $db->prepare('INSERT INTO messages (user_id, username, file_url, file_type) VALUES (:user_id, :username, :file_url, :file_type)');
            $stmt->bindValue(':user_id', $_SESSION['user_id']);
            $stmt->bindValue(':username', $_SESSION['username']);
            $stmt->bindValue(':file_url', $uploadDir . $fileName);
            $stmt->bindValue(':file_type', $fileType);
            $stmt->execute();
        }
    }
    
    echo json_encode(['success' => true]);
}

// آپدیت عکس پروفایل
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['profile_image'])) {
        $stmt = $db->prepare('UPDATE users SET profile_image = :profile_image WHERE user_id = :user_id');
        $stmt->bindValue(':profile_image', $data['profile_image']);
        $stmt->bindValue(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
    }
}
?> 