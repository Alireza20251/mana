<?php

session_start();

require_once "../functions/helpers.php";

/*
|--------------------------------------------------------------------------
| اگر مدیر وارد شده، انتقال به پنل
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['user'])) {
    header('Location: ../panel/index.php');
    exit;
}

?>

<!DOCTYPE html>
<html dir="rtl" lang="fa-IR">

<head>

    <title>ثبت‌نام مدیران | روما</title>

    <meta charset="UTF-8">

    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1"
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

                        <a href="../index.php">

                            <img
                                src="<?php echo asset('assets/Img/logo-main.png'); ?>"
                                alt="لوگوی روما"
                                class="pt-2 pb-4"
                            >

                        </a>

                        <h2>ثبت‌نام مدیران غیرفعال است</h2>

                        <p>
                            ساخت حساب مدیر از طریق این صفحه امکان‌پذیر نیست.
                            برای امنیت روما، حساب‌های مدیر باید از داخل پنل
                            مدیریت و توسط مدیر مجاز ایجاد شوند.
                        </p>

                        <a
                            href="login.php"
                            class="btn btn-block btn-success py-2 radius10 my-3"
                        >
                            ورود مدیر
                        </a>

                        <a
                            href="../index.php"
                            class="btn btn-block py-2 radius10 mb-4"
                        >
                            بازگشت به صفحه اصلی
                        </a>

                    </div>

                </div>

                <div class="col-lg-6 m-auto">

                    <img
                        src="<?php echo asset('assets/Img/login.jpg'); ?>"
                        class="img-fluid d-lg-block d-none"
                        alt="ورود به روما"
                    >

                </div>

            </div>

        </div>

    </section>

</body>

</html>