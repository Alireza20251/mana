<?php

require_once 'functions/helpers.php';
require_once 'functions/articles.php';
require_once 'functions/categories.php';

$articles = getAllArticles();

?>

<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>مقالات | روما</title>

    <link href="<?php echo asset('assets/Css/Main.css'); ?>" rel="stylesheet">
    <link href="<?php echo asset('assets/Css/Menu-demo1.css'); ?>" rel="stylesheet">
    <link href="<?php echo asset('assets/Css/Style.css'); ?>" rel="stylesheet">
    <link href="<?php echo asset('assets/Css/articles.css'); ?>" rel="stylesheet">
<link
    rel="stylesheet"
    href="<?php echo asset('assets/Css/roma-footer.css'); ?>"
>
</head>

<body class="rtl blog">

<div class="main_wrap">

    <?php include "layouts/header.php"; ?>

    <div class="clearfix"></div>

    <?php include "layouts/breadcrumb.php"; ?>

    <main class="articles-page">

        <div class="container">

            <div class="articles-heading">

                <h1>همه مقالات</h1>

                <p>
                    جدیدترین مطالب و مقالات روما را بخوانید
                </p>

            </div>


            <?php if (!empty($articles)) { ?>

                <div class="row">

                    <?php foreach ($articles as $article) { ?>

                        <?php
                        $category = getCategoryById($article->category_id);
                        ?>

                        <div class="col-xl-4 col-md-6 mb-4">

                            <article class="article-card">

                                <div class="article-image">

                                    <a href="blog_details.php?id=<?php echo $article->id; ?>">

                                        <img
                                            src="<?php echo asset('images/articles/' . $article->image); ?>"
                                            alt="<?php echo htmlspecialchars($article->title); ?>"
                                        >

                                    </a>

                                </div>


                                <div class="article-content">

                                    <?php if ($category) { ?>

                                        <span class="article-category">
                                            <?php echo $category->title; ?>
                                        </span>

                                    <?php } ?>


                                    <h2 class="article-title">

                                        <a href="blog_details.php?id=<?php echo $article->id; ?>">

                                            <?php echo htmlspecialchars($article->title); ?>

                                        </a>

                                    </h2>


                                    <p class="article-summary">

                                        <?php
                                        echo mb_substr(
                                            strip_tags($article->body),
                                            0,
                                            140
                                        );
                                        ?>

                                        ...

                                    </p>


                                    <a
                                        class="read-more"
                                        href="blog_details.php?id=<?php echo $article->id; ?>"
                                    >
                                        ادامه مطلب
                                    </a>

                                </div>

                            </article>

                        </div>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <div class="no-articles">

                    <h3>هنوز مقاله‌ای منتشر نشده است.</h3>

                    <p>به‌زودی مطالب جدید در روما قرار خواهد گرفت.</p>

                </div>

            <?php } ?>

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