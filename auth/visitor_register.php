<?php

session_start();

require_once "../functions/DB.php";
require_once "../functions/helpers.php";

$message = "";
$name = "";
$email = "";

/*
|--------------------------------------------------------------------------
| توکن امنیتی فرم ثبت‌نام
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['visitor_register_csrf'])
    || !is_string($_SESSION['visitor_register_csrf'])
) {
    $_SESSION['visitor_register_csrf'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| پیام ثبت‌نام موفق پس از تغییر مسیر
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['visitor_register_flash'])) {
    $message = (string) $_SESSION['visitor_register_flash'];
    unset($_SESSION['visitor_register_flash']);
}

/*
|--------------------------------------------------------------------------
| پردازش فرم ثبت‌نام
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $postedToken = $_POST['csrf_token'] ?? null;

    if (
        !is_string($postedToken)
        || !hash_equals(
            $_SESSION['visitor_register_csrf'],
            $postedToken
        )
    ) {

        $message = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } else {

        $rawName = $_POST["name"] ?? "";
        $rawEmail = $_POST["email"] ?? "";
        $rawPassword = $_POST["password"] ?? "";

        if (
            !is_string($rawName)
            || !is_string($rawEmail)
            || !is_string($rawPassword)
        ) {

            $message = "اطلاعات واردشده معتبر نیست.";

        } else {

            $name = trim($rawName);
            $email = trim($rawEmail);
            $password = $rawPassword;

            if (
                $name === ""
                || $email === ""
                || $password === ""
            ) {

                $message = "لطفاً تمام فیلدها را پر کنید.";

            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $message = "ایمیل واردشده صحیح نیست.";

            } else {

                /*
                 * بررسی حداقل طول رمز عبور:
                 * ۸ کاراکتر، با پشتیبانی از کاراکترهای فارسی
                 */
                if (function_exists('mb_strlen')) {

                    $passwordLength = mb_strlen(
                        $password,
                        'UTF-8'
                    );

                } else {

                    $passwordLength = preg_match_all(
                        '/./us',
                        $password
                    );

                    if ($passwordLength === false) {
                        $passwordLength = strlen($password);
                    }
                }

                if ($passwordLength < 8) {

                    $message = "رمز عبور باید حداقل ۸ کاراکتر داشته باشد.";

                } else {

                    global $conn;

                    try {

                        /*
                         * بررسی تکراری نبودن ایمیل
                         */
                        $stmt = $conn->prepare(
                            "SELECT id
                             FROM visitor_users
                             WHERE email = ?
                             LIMIT 1"
                        );

                        $stmt->execute([$email]);

                        if ($stmt->fetch()) {

                            $message = "این ایمیل قبلاً ثبت‌نام کرده است.";

                        } else {

                            /*
                             * هش کردن رمز عبور
                             */
                            $hashedPassword = password_hash(
                                $password,
                                PASSWORD_DEFAULT
                            );

                            if ($hashedPassword === false) {

                                $message = "ساخت رمز عبور با خطا مواجه شد. لطفاً دوباره تلاش کنید.";

                            } else {

                                /*
                                 * ثبت بازدیدکننده در دیتابیس
                                 */
                                $stmt = $conn->prepare(
                                    "INSERT INTO visitor_users
                                        (name, email, password)
                                     VALUES (?, ?, ?)"
                                );

                                $success = $stmt->execute([
                                    $name,
                                    $email,
                                    $hashedPassword
                                ]);

                                if ($success) {

                                    /*
                                     * جلوگیری از ثبت مجدد فرم
                                     * پس از تازه‌سازی صفحه
                                     */
                                    $_SESSION['visitor_register_flash'] =
                                        "ثبت‌نام با موفقیت انجام شد.";

                                    unset(
                                        $_SESSION['visitor_register_csrf']
                                    );

                                    header(
                                        'Location: visitor_register.php'
                                    );
                                    exit;

                                } else {

                                    $message = "ثبت‌نام انجام نشد. لطفاً دوباره تلاش کنید.";

                                }
                            }
                        }

                    } catch (Throwable $e) {

                        error_log(
                            "Roma visitor registration error: "
                            . $e->getMessage()
                        );

                        $message = "هنگام ثبت‌نام خطایی رخ داد. لطفاً دوباره تلاش کنید.";
                    }
                }
            }
        }
    }
}

?>

<!DOCTYPE html>

<html lang="fa-IR" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ثبت‌نام در روما | ایجاد حساب کاربری</title>

    <meta
        name="description"
        content="در روما حساب کاربری خود را ایجاد کنید و مطالب و مقاله‌های خود را برای بررسی و انتشار ارسال کنید."
    >

    <meta
        name="robots"
        content="noindex, nofollow"
    >

    <link
        href="<?php echo asset('assets/Css/Main.css'); ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/Menu-demo1.css'); ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/Style.css'); ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/visitor-auth.css'); ?>"
        rel="stylesheet"
    >

</head>

<body class="rtl roma-auth-page">

<div class="roma-auth-wrapper">

    <div class="roma-auth-card">

        <div class="roma-auth-header">

            <span class="roma-auth-badge">
                مجله روما
            </span>

            <h1>
                ایجاد حساب کاربری
            </h1>

            <p>
                در روما ثبت‌نام کنید و مطالب خود را با ما به اشتراک بگذارید.
            </p>

        </div>


        <?php if ($message !== "") { ?>

            <div class="roma-auth-message" role="status">
                <?php
                echo htmlspecialchars(
                    $message,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </div>

        <?php } ?>


        <form
            method="post"
            action="visitor_register.php"
            class="roma-auth-form"
        >

            <!-- توکن امنیتی فرم -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?php
                echo htmlspecialchars(
                    $_SESSION['visitor_register_csrf'],
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>"
            >

            <div class="roma-form-group">

                <label for="name">
                    نام
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="نام خود را وارد کنید"
                    value="<?php
                    echo htmlspecialchars(
                        $name,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                    autocomplete="name"
                    required
                >

            </div>


            <div class="roma-form-group">

                <label for="email">
                    ایمیل
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="ایمیل خود را وارد کنید"
                    value="<?php
                    echo htmlspecialchars(
                        $email,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                    autocomplete="email"
                    required
                >

            </div>


            <div class="roma-form-group">

                <label for="password">
                    رمز عبور
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="حداقل ۸ کاراکتر"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="roma-auth-button"
            >
                ایجاد حساب
            </button>

        </form>


        <div class="roma-auth-footer">

            <span>
                قبلاً حساب ساخته‌اید؟
            </span>

            <a href="visitor_login.php">
                وارد شوید
            </a>

        </div>


        <div class="roma-auth-back">

            <a href="../index.php">
                <span>→</span>