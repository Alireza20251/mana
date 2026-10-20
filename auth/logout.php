<?php

session_start();

/* پاک‌کردن اطلاعات نشست */
$_SESSION = [];

/* حذف کوکی نشست از مرورگر */
if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

/* نابودکردن نشست */
session_destroy();

/* بازگشت به صفحه ورود مدیر */
header('Location: login.php');
exit;