<?php

session_start();

require_once "../functions/DB.php";
require_once "../functions/helpers.php";

$message = "";
$email = "";

/*
|--------------------------------------------------------------------------
| ایجاد توکن امنیتی فرم ورود
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['visitor_login_csrf'])
    || !is_string($_SESSION['visitor_login_csrf'])
) {
    $_SESSION['visitor_login_csrf'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| پردازش فرم ورود
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $postedToken = $_POST['csrf_token'] ?? null;
    $emailInput = $_POST['email'] ?? null;
    $passwordInput = $_POST['password'] ?? null;

    /*
     * بررسی توکن امنیتی
     */
    if (
        !is_string($postedToken)
        || !hash_equals(
            $_SESSION['visitor_login_csrf'],
            $postedToken
        )
    ) {

        $message = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } elseif (
        !is_string($emailInput)
        || !is_string($passwordInput)
    ) {

        $message = "اطلاعات واردشده معتبر نیست.";

    } else {

        $email = trim($emailInput);
        $password = $passwordInput;

        if ($email === "" || $password === "") {

            $message = "لطفاً ایمیل و رمز عبور را وارد کنید.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $message = "ایمیل واردشده صحیح نیست.";

        } else {

            global $conn;

            try {

                /*
                 * جست‌وجوی بازدیدکننده بر اساس ایمیل
                 */
                $stmt = $conn->prepare(
                    "SELECT id, name, email, password
                     FROM visitor_users
                     WHERE email = ?
                     LIMIT 1"
                );

                $stmt->execute([$email]);

                $visitor = $stmt->fetch();

                /*
                 * بررسی رمز عبور هش‌شده
                 */
                if (
                    $visitor
                    && password_verify(
                        $password,
                        $visitor->password
                    )
                ) {

                    /*
                     * جلوگیری از Session Fixation
                     */
                    if (!session_regenerate_id(true)) {

                        $message = "ورود انجام نشد. لطفاً دوباره تلاش کنید.";

                    } else {

                        /*
                         * ثبت اطلاعات بازدیدکننده در نشست
                         */
                        $_SESSION["visitor_id"] = (int) $visitor->id;
                        $_SESSION["visitor_name"] = $visitor->name;
                        $_SESSION["visitor_email"] = $visitor->email;

                        /*
                         * توکن قبلی پس از ورود دیگر لازم نیست.
                         */
                        unset($_SESSION['visitor_login_csrf']);

                        header("Location: ../contact.php");
                        exit;
                    }

                } else {

                    /*
                     * پیام یکسان برای ایمیل و رمز اشتباه
                     */
                    $message = "ایمیل یا رمز عبور اشتباه است.";
                }

            } catch (Throwable $e) {

                error_log(
                    "Roma visitor login error: " . $e->getMessage()
                );

                $message = "هنگام ورود خطایی رخ داد. لطفاً دوباره تلاش کنید.";
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

    <title>ورود به حساب کاربری | روما</title>

    <meta
        name="description"
        content="ورود به حساب کاربری بازدیدکنندگان روما."
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
                ورود به حساب کاربری
            </h1>

            <p>
                برای ادامه وارد حساب کاربری خود شوید.
            </p>

        </div>


        <?php if ($message !== "") { ?>

            <div class="roma-auth-message" role="alert">

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
            action="visitor_login.php"
            class="roma-auth-form"
        >

            <!-- توکن امنیتی -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?php
                echo htmlspecialchars(
                    $_SESSION['visitor_login_csrf'],
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>"
            >

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
                    placeholder="رمز عبور خود را وارد کنید"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="roma-auth-button"
            >
                ورود به حساب
            </button>

        </form>


        <div class="roma-auth-footer">

            <span>
                حساب کاربری ندارید؟
            </span>

            <a href="visitor_register.php">
                ثبت‌نام کنید
            </a>

        </div>


        <div class="roma-auth-back">

            <a href="../index.php">
                <span>→</span>
                بازگشت به روما
            </a>

        </div>

    </div>

</div>

</body>

</html>