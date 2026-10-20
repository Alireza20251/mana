<?php

require_once "DB.php";
require_once "helpers.php";


/*
|--------------------------------------------------------------------------
| ایجاد مقاله
|--------------------------------------------------------------------------
*/

function createArticle($title, $body, $user_id, $category_id, $status, $image)
{
    global $conn;

    // آپلود تصویر مقاله
    $new_image = uploadImage($image, 'articles');

    // اگر آپلود ناموفق بوده، مقاله ثبت نشود.
    if ($new_image === false) {
        return false;
    }

    $sql = "
        INSERT INTO articles
            (title, body, user_id, category_id, status, image, created_at)
        VALUES
            (?, ?, ?, ?, ?, ?, NOW())
    ";

    $stmt = $conn->prepare($sql);

    return $stmt->execute([
        $title,
        $body,
        $user_id,
        $category_id,
        $status,
        $new_image
    ]);
}


/*
|--------------------------------------------------------------------------
| دریافت مقاله بر اساس شناسه
|--------------------------------------------------------------------------
*/

function getArticleById($id)
{
    global $conn;

    $sql = "SELECT * FROM articles WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch();
}


/*
|--------------------------------------------------------------------------
| دریافت تمام مقالات
|--------------------------------------------------------------------------
*/

function getAllArticles()
{
    global $conn;

    $sql = "SELECT * FROM articles";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}


/*
|--------------------------------------------------------------------------
| دریافت چهار مقاله آخر
|--------------------------------------------------------------------------
*/

function getLastArticles()
{
    global $conn;

    $sql = "
        SELECT *
        FROM articles
        ORDER BY created_at DESC
        LIMIT 4
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}


/*
|--------------------------------------------------------------------------
| ویرایش مقاله
|--------------------------------------------------------------------------
*/

function updateArticle($id, $title, $body, $category_id, $status, $image)
{
    global $conn;

    // ابتدا مقاله فعلی را دریافت می‌کنیم.
    $article = getArticleById($id);

    if (!$article) {
        return false;
    }

    // تصویر فعلی به‌صورت پیش‌فرض حفظ می‌شود.
    $imageName = $article->image;

    /*
     * فقط زمانی تصویر را جایگزین می‌کنیم که آپلود جدیدی
     * انجام شده باشد. انتخاب نکردن تصویر، مشکلی ایجاد نمی‌کند.
     */
    $hasUploadAttempt = false;

    if (is_array($image)) {

        if (
            isset($image['error'])
            && (int) $image['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $hasUploadAttempt = true;

        } elseif (
            !isset($image['error'])
            && !empty($image['name'])
        ) {
            $hasUploadAttempt = true;
        }
    }

    if ($hasUploadAttempt) {

        $uploadedImage = uploadImage($image, 'articles');

        // در صورت خطای آپلود، مقاله و تصویر قبلی حفظ شوند.
        if ($uploadedImage === false) {
            return false;
        }

        // فقط نام فایل معتبر جدید پذیرفته می‌شود.
        if (is_string($uploadedImage) && $uploadedImage !== '') {
            $imageName = $uploadedImage;
        }
    }

    $sql = "
        UPDATE articles
        SET
            title = ?,
            body = ?,
            category_id = ?,
            status = ?,
            image = ?,
            updated_at = NOW()
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);

    return $stmt->execute([
        $title,
        $body,
        $category_id,
        $status,
        $imageName,
        $id
    ]);
}


/*
|--------------------------------------------------------------------------
| حذف مقاله بر اساس شناسه
|--------------------------------------------------------------------------
*/

function deleteArticleById($id)
{
    global $conn;

    $sql = "DELETE FROM articles WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
}


/*
|--------------------------------------------------------------------------
| تبدیل وضعیت مقاله به فارسی
|--------------------------------------------------------------------------
*/

function status($status)
{
    $fa_status = "";

    switch ($status) {

        case "draft":
            $fa_status = "پیش‌نویس";
            break;

        case "published":
            $fa_status = "منتشر شده";
            break;

        case "archived":
            $fa_status = "بایگانی";
            break;
    }

    return $fa_status;
}