<?php

/*
|--------------------------------------------------------------------------
| تنظیمات اتصال به دیتابیس
|--------------------------------------------------------------------------
*/

$servername = "localhost";
$username = "root";
$password = "";
$database = "mydb";

/*
|--------------------------------------------------------------------------
| گزینه‌های اتصال PDO
|--------------------------------------------------------------------------
*/

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

/*
|--------------------------------------------------------------------------
| ایجاد اتصال
|--------------------------------------------------------------------------
*/

try {

    $conn = new PDO(
        "mysql:host={$servername};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        $options
    );

} catch (PDOException $e) {

    /*
     * جزئیات خطا فقط در لاگ سرور ثبت می‌شود.
     * اطلاعات داخلی دیتابیس به کاربر نمایش داده نمی‌شود.
     */
    error_log(
        "Roma database connection error: " . $e->getMessage()
    );

    http_response_code(500);

    exit("در حال حاضر اتصال به پایگاه داده امکان‌پذیر نیست.");
}