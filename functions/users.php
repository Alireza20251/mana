<?php

require_once "DB.php";
require_once "helpers.php";


/*
|--------------------------------------------------------------------------
| ایجاد ادمین
|--------------------------------------------------------------------------
*/

function createUser($name, $email, $password, $image)
{
    global $conn;

    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $sql = "
        INSERT INTO users
            (name, email, password, image, created_at)
        VALUES
            (?, ?, ?, ?, NOW())
    ";

    $stmt = $conn->prepare($sql);

    return $stmt->execute([
        $name,
        $email,
        $hashed_password,
        $image
    ]);
}


/*
|--------------------------------------------------------------------------
| بررسی ادمین بر اساس ایمیل
|--------------------------------------------------------------------------
*/

function checkUser($email)
{
    global $conn;

    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);

    return $stmt->fetch();
}


/*
|--------------------------------------------------------------------------
| دریافت ادمین بر اساس شناسه
|--------------------------------------------------------------------------
*/

function getUserById($id)
{
    global $conn;

    $sql = "SELECT * FROM users WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch();
}


/*
|--------------------------------------------------------------------------
| بررسی حداقل طول رمز عبور
|--------------------------------------------------------------------------
*/

function checkPassword($password)
{
    return strlen($password) >= 6;
}


/*
|--------------------------------------------------------------------------
| دریافت تمام ادمین‌ها
|--------------------------------------------------------------------------
*/

function getAllUsers()
{
    global $conn;

    $sql = "SELECT * FROM users";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}


/*
|--------------------------------------------------------------------------
| ویرایش ادمین
|--------------------------------------------------------------------------
| - رمز خالی باشد، رمز قبلی حفظ می‌شود.
| - تصویر جدید انتخاب نشود، تصویر قبلی حفظ می‌شود.
| - آپلود ناموفق باشد، اطلاعات در دیتابیس تغییر نمی‌کند.
|--------------------------------------------------------------------------
*/

function updateUser($id, $name, $password, $image)
{
    global $conn;

    /* بررسی وجود ادمین */
    $user = getUserById($id);

    if (!$user) {
        return false;
    }

    /* حفظ رمز قبلی یا ساخت هش رمز جدید */
    if ($password !== '') {

        if (!checkPassword($password)) {
            return false;
        }

        $passwordValue = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

    } else {

        $passwordValue = $user->password;
    }

    /* تصویر قبلی به‌صورت پیش‌فرض حفظ می‌شود. */
    $imageValue = $user->image;
    $newImageUploaded = false;

    /*
     * بررسی اینکه کاربر واقعاً تلاش به آپلود تصویر کرده است
     */
    $hasUploadAttempt = false;

    if (is_array($image)) {

        if (isset($image['error'])) {

            $hasUploadAttempt =
                (int) $image['error'] !== UPLOAD_ERR_NO_FILE;

        } elseif (!empty($image['name'])) {

            $hasUploadAttempt = true;
        }
    }

    /*
     * آپلود تصویر جدید
     */
    if ($hasUploadAttempt) {

        $uploadedImage = uploadImage($image, 'users');

        if (
            $uploadedImage === false
            || !is_string($uploadedImage)
            || $uploadedImage === ''
        ) {
            return false;
        }

        $imageValue = $uploadedImage;
        $newImageUploaded = true;
    }

    /*
     * ذخیره تغییرات
     */
    try {

        $sql = "
            UPDATE users
            SET
                name = ?,
                password = ?,
                image = ?,
                updated_at = NOW()
            WHERE id = ?
        ";

        $stmt = $conn->prepare($sql);

        $success = $stmt->execute([
            $name,
            $passwordValue,
            $imageValue,
            $id
        ]);

        /*
         * در صورت شکست ذخیره دیتابیس، تصویر تازه آپلودشده
         * که هنوز به هیچ رکوردی وصل نشده است حذف می‌شود.
         */
        if (!$success && $newImageUploaded) {

            $uploadedPath =
                __DIR__ . '/../images/users/' . $imageValue;

            if (is_file($uploadedPath)) {
                @unlink($uploadedPath);
            }
        }

        return $success;

    } catch (Throwable $e) {

        /*
         * اگر دیتابیس خطا داد، فایل تازه آپلودشده باقی نماند.
         */
        if ($newImageUploaded) {

            $uploadedPath =
                __DIR__ . '/../images/users/' . $imageValue;

            if (is_file($uploadedPath)) {
                @unlink($uploadedPath);
            }
        }

        throw $e;
    }
}


/*
|--------------------------------------------------------------------------
| حذف ادمین بر اساس شناسه
|--------------------------------------------------------------------------
*/

function deleteUserById($id)
{
    global $conn;

    $sql = "DELETE FROM users WHERE id = ?";

    $stmt = $conn->prepare($sql);

    return $stmt->execute([$id]);
}