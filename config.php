<?php
// تنظیمات پایه
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// تنظیمات دیتابیس
define('DB_FILE', 'chat.db');

// تنظیمات ربات تلگرام
define('BOT_TOKEN', '7809044163:AAGgXroRwArowKb6mjmaRPRzgwSZulKyFGI');
define('ADMIN_ID', '6655509748');

// تنظیمات CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// تابع ارسال پاسخ JSON
function sendJsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}
?> 