<?php

require_once 'functions/helpers.php';
require_once 'functions/articles.php';
require_once 'functions/categories.php';

$article = null;

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $article = getArticleById($id);
}

if (!$article) {
    header('Location: articles.php');
    exit;
}

$category = null;

if (!empty($article->category_id)) {
    $category = getCategoryById($article->category_id);
}

$plainText = trim(strip_tags($article->body));

$description = $plainText;

if (mb_strlen($description) > 160) {
    $description = mb_substr($description, 0, 160) . '...';
}

?>

<!DOCTYPE html>

<html dir="rtl" lang="fa-IR">

<head>

    <meta charset="UTF-8">

    <title>
        <?php echo htmlspecialchars($article->title); ?> | روما
    </title>

    <meta
        name="description"
        content="<?php echo htmlspecialchars($description); ?>"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    >

    <meta
        name="robots"
        content="index, follow"
    >

    <meta
        property="og:title"
        content="<?php echo htmlspecialchars($article->title); ?> | روما"
    >

    <meta
        property="og:description"
        content="<?php echo htmlspecialchars($description); ?>"
    >

    <meta
        property="og:type"
        content="article"
    >

    <?php if (!empty($article->image)) { ?>

        <meta
            property="og:image"
            content="<?php echo asset('images/articles/' . $article->image); ?>"
        >

    <?php } ?>


    <link
        href="<?php echo asset('assets/Css/Main.css') ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/Menu-demo1.css') ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/Style.css') ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/blog-details.css') ?>"
        rel="stylesheet"
    >
<link
    rel="stylesheet"
    href="<?php echo asset('assets/Css/roma-footer.css'); ?>"
>
</head>


<body class="rtl blog_details">


<div class="main_wrap">

    <div class="of-site-mask"></div>


    <!-- Header -->

    <?php include "layouts/header.php" ?>


    <div class="clearfix"></div>


    <!-- Breadcrumb -->

    <?php include "layouts/breadcrumb.php" ?>


    <!-- Article -->

    <main class="roma-article-page">

        <div class="roma-article-wrapper">

            <article class="roma-article-card">


                <!-- Article Header -->

                <header class="roma-article-header">


                    <?php if ($category) { ?>

                        <span class="roma-article-category">

                            <?php
                            echo htmlspecialchars($category->title);
                            ?>

                        </span>

                    <?php } ?>


                    <h1 class="roma-article-title">

                        <?php
                        echo htmlspecialchars($article->title);
                        ?>

                    </h1>


                    <div class="roma-article-meta">

                        <span>
                            مقاله روما
                        </span>

                        <span class="roma-article-meta-dot">
                            •
                        </span>

                        <span>

                            <?php
                            echo htmlspecialchars($article->created_at);
                            ?>

                        </span>

                    </div>


                </header>


                <!-- Article Image -->

                <?php if (!empty($article->image)) { ?>

                    <div class="roma-article-image-wrapper">

                        <img
                            src="<?php echo asset('images/articles/' . $article->image); ?>"
                            alt="<?php echo htmlspecialchars($article->title); ?>"
                            class="roma-article-image"
                        >

                    </div>

                <?php } else { ?>

                    <div class="roma-article-no-image">

                        تصویر برای این مقاله ثبت نشده است

                    </div>

                <?php } ?>


                <!-- Article Content -->

                <div class="roma-article-content">

                    <?php echo $article->body; ?>

                </div>


                <!-- Article Footer -->

                <div class="roma-article-footer">

                    <a
                        href="articles.php"
                        class="roma-back-link"
                    >

                        <span>→</span>

                        بازگشت به مقالات

                    </a>

                </div>


            </article>

        </div>

    </main>


    <!-- Footer -->

    <?php include "layouts/footer.php" ?>


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
<script src="<?php echo asset('assets/Js/custom.js'); ?>"></script>ی
</body>

</html>