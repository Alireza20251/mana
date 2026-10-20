<?php

session_start();

require_once "../functions/users.php";
require_once "../functions/helpers.php";

/* اگر مدیر وارد شده، انتقال به پنل */
if (isset($_SESSION['user'])) {
    header('Location: ../panel/index.php');
    exit;
}

$error = "";
$emailValue = "";

/* ایجاد توکن امنیتی فرم ورود */
if (
    !isset($_SESSION['admin_login_csrf'])
    || !is_string($_SESSION['admin_login_csrf'])
    || $_SESSION['admin_login_csrf'] === ''
) {
    $_SESSION['admin_login_csrf'] = bin2hex(random_bytes(32));
}

/* پردازش فرم ورود */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['submit'])
) {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['admin_login_csrf'] ?? '';

    /* بررسی توکن CSRF */
    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } else {
        $rawEmail = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        /* اعتبارسنجی نوع داده‌ها */
        if (
            !is_string($rawEmail)
            || !is_string($password)
        ) {
            $error = "اطلاعات واردشده معتبر نیست.";

        } else {
            $emailValue = trim($rawEmail);

            if ($emailValue === '' || $password === '') {
                $error = "لطفاً تمام فیلدها را تکمیل کنید.";

            } elseif (!filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
                $error = "اطلاعات واردشده صحیح نیست.";

            } else {
                try {
                    $user = checkUser($emailValue);

                    if (
                        $user
                        && isset($user->password)
                        && password_verify($password, $user->password)
                    ) {
                        /*
                         * جلوگیری از تثبیت شناسه نشست:
                         * شناسه نشست پس از ورود موفق تغییر می‌کند.
                         */
                        session_regenerate_id(true);

                        $_SESSION['user'] = $user->email;

                        unset($_SESSION['admin_login_csrf']);

                        header('Location: ../panel/index.php');
                        exit;

                    } else {
                        $error = "اطلاعات واردشده صحیح نیست.";
                    }

                } catch (Throwable $e) {
                    error_log(
                        "Roma admin login error: " . $e->getMessage()
                    );

                    $error = "در حال حاضر ورود انجام نشد. لطفاً دوباره تلاش کنید.";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html dir="rtl" lang="fa-IR">

<head>

    <title>ورود مدیر</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1"
    >

    <link
        href="<?php echo asset('assets/Css/Main.css'); ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/Style.css'); ?>"
        rel="stylesheet"
    >

</head>

<body class="rtl bg-greengrad min-h">

    <section class="container maxw">

        <div class="card login mx-md-5 mt-5 justify-content-center shadow-none">

            <div class="row">

                <div class="col-lg-6">

                    <div class="card-body p-4 text-center">

                        <a href="#">
                            <img
                                src="<?php echo asset('assets/Img/logo-main.png'); ?>"
                                alt="لوگوی روما"
                                class="pt-2 pb-4"
                            >
                        </a>

                        <?php if ($error !== '') { ?>

                            <p style="color:red;" role="alert">
                                <?php
                                echo htmlspecialchars(
                                    $error,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </p>

                        <?php } ?>

                        <form action="login.php" method="POST">

                            <!-- توکن امنیتی فرم -->
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php
                                echo htmlspecialchars(
                                    $_SESSION['admin_login_csrf'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                            >

                            <div class="form-group">

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control"
                                    placeholder="ایمیل را وارد کنید"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $emailValue,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>"
                                    autocomplete="username"
                                    required
                                >

                            </div>

                            <div class="form-group mb-4">

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="کلمه عبور"
                                    autocomplete="current-password"
                                    required
                                >

                            </div>

                            <button
                                type="submit"
                                name="submit"
                                value="1"
                                id="login"
                                class="btn btn-block py-2 btn-success radius10 my-3"
                            >
                                ورود
                            </button>

                        </form>

                    </div>

                </div>

                <div class="col-lg-6 m-auto">

                    <img
                        src="<?php echo asset('assets/Img/login.jpg'); ?>"
                        class="img-fluid d-lg-block d-none"
                        alt="صفحه ورود"
                    >

                </div>

            </div>

        </div>

    </section>

</body>

</html>