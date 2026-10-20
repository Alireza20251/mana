<?php

require_once 'functions/helpers.php';
require_once 'functions/articles.php';
require_once 'functions/categories.php';

$articles = getAllArticles();

?>

<!DOCTYPE html>
<html dir="rtl" lang="fa-IR">

<head>

    <meta charset="UTF-8">

    <title>روما | مجله‌ای برای خواندن و یادگیری</title>

    <meta
        name="description"
        content="روما؛ مجله‌ای برای مطالعه مقالات متنوع، مطالب کاربردی و محتوای جذاب در موضوعات مختلف."
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="robots" content="index, follow">

    <meta property="og:title" content="روما | مجله‌ای برای خواندن و یادگیری">

    <meta
        property="og:description"
        content="مطالب و مقالات متنوع و کاربردی را در مجله روما بخوانید."
    >

    <meta property="og:type" content="website">

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
        href="<?php echo asset('assets/Css/home.css'); ?>"
    >

    <!-- استایل اختصاصی فوتر روما -->
    <link
        rel="stylesheet"
        href="<?php echo asset('assets/Css/roma-footer.css'); ?>"
    >

</head>

<body class="rtl blog roma-home">

<div class="main_wrap">

    <div class="of-site-mask"></div>

    <!-- Header -->
    <?php include "layouts/header.php"; ?>

    <div class="clearfix"></div>

    <!-- Breadcrumb -->
    <?php include "layouts/breadcrumb.php"; ?>

    <!-- Main Content -->
    <main>

        <!-- Hero -->
        <section class="roma-hero">

            <div class="roma-hero-container">

                <div class="roma-hero-content">

                    <span class="roma-hero-badge">
                        مجله روما
                    </span>

                    <h1>
                        جایی برای
                        <span>خواندن، یادگیری و کشف</span>
                        مطالب جدید
                    </h1>

                    <p class="roma-hero-text">
                        در روما می‌توانید مقالات متنوع و مطالب کاربردی را
                        بخوانید، موضوعات جدید یاد بگیرید و محتوایی متناسب
                        با علایق خود پیدا کنید.
                    </p>

                    <div class="roma-hero-buttons">

                        <a
                            href="articles.php"
                            class="roma-btn roma-btn-primary"
                        >
                            مشاهده مقالات
                        </a>

                        <a
                            href="about.php"
                            class="roma-btn roma-btn-secondary"
                        >
                            درباره روما
                        </a>

                    </div>

                </div>

                <div class="roma-hero-visual">

                    <div class="roma-hero-card">

                        <div class="roma-hero-card-top">

                            <span class="roma-dot"></span>

                            <span class="roma-hero-card-title">
                                امروز چه چیزی یاد بگیریم؟
                            </span>

                        </div>

                        <div class="roma-hero-card-line medium"></div>

                        <div class="roma-hero-card-line"></div>

                        <div class="roma-hero-card-line short"></div>

                        <div class="roma-hero-card-highlight">

                            <strong>مقالات جدید روما</strong>

                            <span>
                                مطالب تازه، کاربردی و جذاب را دنبال کنید
                                و هر روز چیز جدیدی یاد بگیرید.
                            </span>

                        </div>

                        <div class="roma-hero-card-meta">

                            <span>مطالب جدید</span>

                            <span>•</span>

                            <span>یادگیری بیشتر</span>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- Features -->
        <section class="roma-section">

            <div class="roma-section-heading">

                <span>
                    چرا روما؟
                </span>

                <h2>
                    تجربه‌ای ساده برای مطالعه
                </h2>

                <p>
                    محتوای مورد علاقه‌ات را پیدا کن، بخوان و
                    چیزهای جدید یاد بگیر.
                </p>

            </div>

            <div class="roma-features">

                <article class="roma-feature-card">

                    <div class="roma-feature-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            <path
                                d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21.5V4.5Z"
                            />

                            <path
                                d="M4 19a2.5 2.5 0 0 1 2.5-2.5H20"
                            />

                            <path
                                d="M8 6h8M8 10h8"
                            />

                        </svg>

                    </div>

                    <h3>
                        مقالات متنوع
                    </h3>

                    <p>
                        مجموعه‌ای از مطالب مختلف برای مطالعه و
                        آشنایی با موضوعات جدید.
                    </p>

                </article>

                <article class="roma-feature-card">

                    <div class="roma-feature-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path
                                d="m20 20-4-4"
                            />

                            <path
                                d="M8.5 11h5M11 8.5v5"
                            />

                        </svg>

                    </div>

                    <h3>
                        مطالعه آسان
                    </h3>

                    <p>
                        طراحی ساده و خوانا برای اینکه بدون
                        حواس‌پرتی روی محتوا تمرکز کنید.
                    </p>

                </article>

                <article class="roma-feature-card">

                    <div class="roma-feature-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                        >

                            <path
                                d="M9 18h6"
                            />

                            <path
                                d="M10 22h4"
                            />

                            <path
                                d="M8.2 14.5A7 7 0 1 1 15.8 14.5C14.7 15.4 14 16.3 14 18h-4c0-1.7-.7-2.6-1.8-3.5Z"
                            />

                            <path
                                d="M9 10.5a2.5 2.5 0 0 1 5 0"
                            />

                        </svg>

                    </div>

                    <h3>
                        محتوای کاربردی
                    </h3>

                    <p>
                        مطالبی که می‌توانند به افزایش اطلاعات
                        و یادگیری شما کمک کنند.
                    </p>

                </article>

            </div>

        </section>

        <!-- Latest Articles -->
        <section class="roma-latest">

            <div class="roma-section">

                <div class="roma-section-heading">

                    <span>
                        تازه‌ترین مطالب
                    </span>

                    <h2>
                        آخرین مقالات روما
                    </h2>

                    <p>
                        جدیدترین مقالات منتشرشده را از اینجا دنبال کنید.
                    </p>

                </div>

                <div class="roma-latest-grid">

                    <?php

                    $latestArticles = array_slice($articles, 0, 6);

                    foreach ($latestArticles as $article) {

                        $category = getCategoryById($article->category_id);

                        $description = trim(
                            strip_tags($article->body)
                        );

                        if (mb_strlen($description) > 120) {
                            $description = mb_substr(
                                $description,
                                0,
                                120
                            ) . '...';
                        }

                    ?>

                        <article class="roma-latest-card">

                            <?php if (!empty($article->image)) { ?>

                                <a
                                    href="<?php echo 'blog_details.php?id=' . $article->id; ?>"
                                    aria-label="<?php echo htmlspecialchars($article->title); ?>"
                                >

                                    <img
                                        class="roma-latest-image"
                                        src="<?php echo asset('images/articles/' . $article->image); ?>"
                                        alt="<?php echo htmlspecialchars($article->title); ?>"
                                        loading="lazy"
                                    >

                                </a>

                            <?php } else { ?>

                                <div
                                    class="roma-latest-image"
                                    aria-hidden="true"
                                ></div>

                            <?php } ?>

                            <div class="roma-latest-content">

                                <?php if ($category) { ?>

                                    <span class="text-primary fa12 IRANSans_Medium">

                                        <?php echo htmlspecialchars($category->title); ?>

                                    </span>

                                <?php } ?>

                                <h3>

                                    <a
                                        href="<?php echo 'blog_details.php?id=' . $article->id; ?>"
                                    >

                                        <?php echo htmlspecialchars($article->title); ?>

                                    </a>

                                </h3>

                                <p>
                                    <?php echo htmlspecialchars($description); ?>
                                </p>

                                <a
                                    href="<?php echo 'blog_details.php?id=' . $article->id; ?>"
                                    class="roma-btn roma-btn-secondary"
                                >
                                    مطالعه مقاله
                                </a>

                            </div>

                        </article>

                    <?php } ?>

                </div>

                <?php if (count($articles) > 0) { ?>

                    <div
                        class="roma-hero-buttons"
                        style="justify-content:center; margin-top:35px;"
                    >

                        <a
                            href="articles.php"
                            class="roma-btn roma-btn-primary"
                        >
                            مشاهده همه مقالات
                        </a>

                    </div>

                <?php } ?>

            </div>

        </section>

        <!-- CTA -->
        <section class="roma-cta">

            <h2>
                مقاله‌ای برای اشتراک‌گذاری داری؟
            </h2>

            <p>
                اگر مطلب مفیدی داری که فکر می‌کنی برای خوانندگان
                روما جذاب است، می‌توانی آن را برای بررسی ارسال کنی.
            </p>

            <a
                href="contact.php"
                class="roma-btn roma-btn-primary"
            >
                ارسال مقاله
            </a>

        </section>

    </main>

    <!-- فوتر مشترک روما -->
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