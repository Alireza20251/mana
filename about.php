
<?php

require_once 'functions/helpers.php';

?>

<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>درباره روما | مجله اینترنتی روما</title>

    <meta
        name="description"
        content="با روما آشنا شوید؛ مجله‌ای اینترنتی برای مطالعه، یادگیری و دنبال کردن مطالب جذاب، مفید و کاربردی."
    >

    <meta
        name="robots"
        content="index, follow"
    >

    <meta
        property="og:title"
        content="درباره روما | مجله اینترنتی روما"
    >

    <meta
        property="og:description"
        content="روما یک مجله اینترنتی برای مطالعه، یادگیری و دسترسی به مطالب جذاب و کاربردی است."
    >

    <meta
        property="og:type"
        content="website"
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
        href="<?php echo asset('assets/Css/about.css'); ?>"
        rel="stylesheet"
    >
<link
    rel="stylesheet"
    href="<?php echo asset('assets/Css/roma-footer.css'); ?>"
>
</head>

<body class="rtl blog roma-about">

<div class="main_wrap">

    <?php include "layouts/header.php"; ?>

    <div class="clearfix"></div>

    <?php include "layouts/breadcrumb.php"; ?>


    <main class="about-page">

        <div class="container">


            <!-- معرفی روما -->

            <section class="about-hero">

                <span class="about-badge">
                    مجله اینترنتی روما
                </span>

                <h1>
                    درباره روما
                </h1>

                <p>
                    روما یک مجله اینترنتی برای علاقه‌مندان به مطالعه،
                    یادگیری و دنبال کردن مطالب جذاب و کاربردی است.
                    ما تلاش می‌کنیم مطالبی مفید، خواندنی و باکیفیت
                    در اختیار شما قرار دهیم.
                </p>

            </section>


            <!-- درباره روما -->

            <section class="about-content">

                <div class="about-section-title">

                    <span>
                        درباره ما
                    </span>

                    <h2>
                        روما چیست؟
                    </h2>

                </div>

                <p>
                    روما با هدف ایجاد یک فضای ساده و جذاب برای مطالعه
                    و دسترسی به مقالات مختلف راه‌اندازی شده است.
                    در این وب‌سایت می‌توانید مقالات منتشرشده در
                    موضوعات مختلف را مطالعه کنید.
                </p>

                <p>
                    هدف ما این است که محتوایی مفید و قابل استفاده
                    برای کاربران تولید و منتشر کنیم و تجربه‌ای
                    ساده و لذت‌بخش برای مطالعه ایجاد کنیم.
                </p>

            </section>


            <!-- ویژگی‌ها -->

            <section class="about-features">

                <div class="row">


                    <div class="col-lg-4 col-md-6 mb-4">

                        <div class="about-feature">

                            <div class="about-feature-icon">
                                <i class="fal fa-newspaper"></i>
                            </div>

                            <h3>
                                مقالات متنوع
                            </h3>

                            <p>
                                مجموعه‌ای از مقالات مختلف برای مطالعه
                                و افزایش اطلاعات شما.
                            </p>

                        </div>

                    </div>


                    <div class="col-lg-4 col-md-6 mb-4">

                        <div class="about-feature">

                            <div class="about-feature-icon">
                                <i class="fal fa-book-open"></i>
                            </div>

                            <h3>
                                مطالعه آسان
                            </h3>

                            <p>
                                محیطی ساده و مناسب برای مطالعه
                                مطالب بدون پیچیدگی.
                            </p>

                        </div>

                    </div>


                    <div class="col-lg-4 col-md-6 mb-4">

                        <div class="about-feature">

                            <div class="about-feature-icon">
                                <i class="fal fa-lightbulb"></i>
                            </div>

                            <h3>
                                محتوای کاربردی
                            </h3>

                            <p>
                                تلاش می‌کنیم مطالبی مفید و کاربردی
                                در اختیار کاربران قرار دهیم.
                            </p>

                        </div>

                    </div>


                </div>

            </section>


            <!-- دعوت به مطالعه -->

            <section class="about-cta">

                <span class="about-cta-label">
                    شروع مطالعه
                </span>

                <h2>
                    مطالب روما را دنبال کنید
                </h2>

                <p>
                    مقالات جدید را بخوانید و مطالب مورد علاقه خود را پیدا کنید.
                </p>

                <a href="articles.php">
                    مشاهده مقالات
                    <span>←</span>
                </a>

            </section>


        </div>

    </main>


    <?php include "layouts/footer.php"; ?>

</div>

<!-- کتابخانه اصلی -->
<script src="<?php echo asset('assets/Js/jquery.min.js'); ?>"></script>

<!-- بوت‌استرپ -->
<script src="<?php echo asset('assets/Js/bootstrap.min.js'); ?>"></script>

<!-- کتابخانه‌های موردنیاز قالب -->
<script src="<?php echo asset('assets/Js/owl.carousel.js'); ?>"></script>
<script src="<?php echo asset('assets/Js/typed.min.js'); ?>"></script>
<script src="<?php echo asset('assets/Js/intlTelInput.min.js'); ?>"></script>

<!-- کدهای اختصاصی سایت -->
<script src="<?php echo asset('assets/Js/my-script.js'); ?>"></script>
<script src="<?php echo asset('assets/Js/custom.js'); ?>"></script>
</body>

</html>

