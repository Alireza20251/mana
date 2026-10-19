
<?php

declare(strict_types=1);

/**
 * MANA - Application Entry Point
 *
 * تمام درخواست‌های عمومی سایت از این فایل وارد برنامه می‌شوند.
 * راه‌اندازی هسته و مسیریاب را در مرحله بعد اضافه می‌کنیم.
 */

use Mana\Core\Application;

define('BASE_PATH', dirname(__DIR__));

// در صورت وجود، تنظیمات محیطی و کلاس‌های پروژه در مراحل بعد بارگذاری می‌شوند.
header('Content-Type: text/html; charset=UTF-8');

http_response_code(200);

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مانا | خرید آسان، انتخاب هوشمندانه</title>
</head>
<body>
    <main>
        <h1>مانا</h1>
        <p>خرید آسان، انتخاب هوشمندانه</p>
        <p>هستهٔ اجرایی فروشگاه در حال آماده‌سازی است.</p>
    </main>
</body>
</html>
