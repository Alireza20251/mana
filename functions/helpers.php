<?php

const BASE_URL = "http://localhost/codeyad%20blog/";

/*
|--------------------------------------------------------------------------
| آدرس فایل‌های ثابت مثل CSS، JavaScript و تصویر
|--------------------------------------------------------------------------
*/

function asset($file)
{
    return BASE_URL . $file;
}

/*
|--------------------------------------------------------------------------
| ساخت آدرس صفحات سایت
|--------------------------------------------------------------------------
*/

function url($url)
{
    return BASE_URL . $url;
}

/*
|--------------------------------------------------------------------------
| آپلود امن تصاویر
|--------------------------------------------------------------------------
| پوشه‌های مجاز:
| images/articles
| images/users
|
| مقدار برگشتی:
| نام فایل در صورت موفقیت
| null در صورت انتخاب نکردن فایل
| false در صورت نامعتبر بودن فایل یا خطای ذخیره‌سازی
|--------------------------------------------------------------------------
*/

function uploadImage($image, $table)
{
    // فقط مسیرهای مجاز پروژه
    $allowedDirectories = [
        'articles',
        'users'
    ];

    if (!in_array($table, $allowedDirectories, true)) {
        return false;
    }

    // بررسی ساختار فایل ارسالی
    if (!is_array($image) || !isset($image['error'])) {
        return false;
    }

    // انتخاب نکردن تصویر اختیاری است
    if ($image['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // بررسی خطاهای آپلود
    if ($image['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if (
        empty($image['tmp_name'])
        || !is_uploaded_file($image['tmp_name'])
    ) {
        return false;
    }

    // محدودیت حجم: ۵ مگابایت
    $maxSize = 5 * 1024 * 1024;

    $actualSize = filesize($image['tmp_name']);

    if (
        $actualSize === false
        || $actualSize <= 0
        || $actualSize > $maxSize
    ) {
        return false;
    }

    // بررسی محتوای واقعی تصویر
    $imageInfo = @getimagesize($image['tmp_name']);

    if ($imageInfo === false || empty($imageInfo['mime'])) {
        return false;
    }

    // فقط فرمت‌های تصویری مجاز
    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    $mimeType = $imageInfo['mime'];

    if (!isset($allowedMimeTypes[$mimeType])) {
        return false;
    }

    // مسیر مقصد از فهرست مجاز انتخاب می‌شود
    $directory = __DIR__ . '/../images/' . $table;

    if (
        !is_dir($directory)
        || !is_writable($directory)
    ) {
        return false;
    }

    // نام تصادفی برای جلوگیری از تداخل و استفاده از نام اصلی فایل
    try {
        $newName = bin2hex(random_bytes(16))
            . '.'
            . $allowedMimeTypes[$mimeType];
    } catch (Throwable $e) {
        error_log('Roma image upload error: ' . $e->getMessage());
        return false;
    }

    $destination = $directory . '/' . $newName;

    // ذخیره فایل
    if (!move_uploaded_file($image['tmp_name'], $destination)) {
        return false;
    }

    return $newName;
}