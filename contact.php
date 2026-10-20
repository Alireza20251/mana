<?php

session_start();

require_once "functions/helpers.php";

?>

<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ارتباط با روما | مجله اینترنتی روما</title>

    <meta
        name="description"
        content="ارتباط با روما؛ ارسال مقاله، ارتباط با مدیریت و استفاده از امکانات حساب کاربری در مجله اینترنتی روما."
    >

    <meta name="robots" content="index, follow">

    <meta
        property="og:title"
        content="ارتباط با روما | مجله اینترنتی روما"
    >

    <meta
        property="og:description"
        content="با روما در ارتباط باشید، مقاله خود را ارسال کنید و با مدیریت ارتباط داشته باشید."
    >

    <link
        rel="stylesheet"
        href="<?php echo asset('assets/Css/Main.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?php echo asset('assets/Css/Menu-demo1.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?php echo asset('assets/Css/Style.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?php echo asset('assets/Css/contact.css'); ?>"
    >

    <!-- استایل مشترک فوتر روما -->
    <link
        rel="stylesheet"
        href="<?php echo asset('assets/Css/roma-footer.css'); ?>"
    >

</head>

<body class="rtl roma-contact-page">

    <main class="roma-contact-wrapper">

        <div class="roma-contact-card">

            <!-- آیکون -->
            <div class="roma-contact-icon">
                <span>✦</span>
            </div>

            <!-- عنوان بخش -->
            <div class="roma-contact-badge">
                ارتباط با روما
            </div>

            <?php if (isset($_SESSION["visitor_id"])) { ?>

                <!-- کاربر وارد شده -->
                <h1>
                    سلام
                    <?php
                    echo htmlspecialchars(
                        (string) ($_SESSION["visitor_name"] ?? "دوست روما"),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                    👋
                </h1>

                <p class="roma-contact-description">
                    شما وارد حساب کاربری خود شده‌اید.
                    حالا می‌توانید مقاله یا محتوای خود را
                    برای بررسی و انتشار در روما ارسال کنید
                    یا با مدیریت در ارتباط باشید.
                </p>

                <div class="roma-contact-actions">

                    <!-- ارسال مقاله -->
                    <a
                        href="visitor/submit_article.php"
                        class="roma-contact-button roma-contact-primary"
                    >
                        <span>ارسال مقاله</span>
                        <span class="roma-contact-arrow">←</span>
                    </a>

                    <!-- پیام به مدیریت -->
                    <a
                        href="visitor/messages.php"
                        class="roma-contact-button roma-contact-secondary"
                    >
                        <span>پیام به مدیریت</span>
                        <span class="roma-contact-arrow">←</span>
                    </a>

                    <!-- خروج -->
                    <a
                        href="auth/visitor_logout.php"
                        class="roma-contact-logout"
                    >
                        خروج از حساب
                    </a>

                </div>

            <?php } else { ?>

                <!-- کاربر وارد نشده -->
                <h1>
                    مقاله خودت رو با روما به اشتراک بذار
                </h1>

                <p class="roma-contact-description">
                    اگر مقاله، مطلب یا محتوای مفیدی داری که فکر می‌کنی
                    برای خوانندگان روما جذاب است، می‌توانی آن را برای ما ارسال کنی.
                    مطالب ارسالی پس از بررسی توسط مدیریت منتشر خواهند شد.
                </p>

                <div class="roma-contact-actions">

                    <a
                        href="auth/visitor_login.php"
                        class="roma-contact-button roma-contact-primary"
                    >
                        <span>ورود / ثبت‌نام</span>
                        <span class="roma-contact-arrow">←</span>
                    </a>

                </div>

            <?php } ?>

            <!-- بازگشت -->
            <div class="roma-contact-footer">

                <a href="index.php">
                    <span>→</span>
                    بازگشت به صفحه اصلی
                </a>

            </div>

        </div>

    </main>

</body>
</html>