<?php

require_once "functions/helpers.php";
require_once "functions/DB.php";

$query = '';
$results = [];
$error = '';
$searched = isset($_GET['q']);

if ($searched) {
    $rawQuery = $_GET['q'];

    if (!is_string($rawQuery)) {
        $error = "عبارت جست‌وجو معتبر نیست.";
    } else {
        $query = trim($rawQuery);

        if (mb_strlen($query, 'UTF-8') > 100) {
            $query = mb_substr($query, 0, 100, 'UTF-8');
        }

        if ($query !== '') {
            try {
                $sql = "
                    SELECT
                        a.id,
                        a.title,
                        a.body,
                        a.image,
                        a.category_id,
                        a.created_at,
                        c.title AS category_title
                    FROM articles AS a
                    LEFT JOIN categories AS c
                        ON c.id = a.category_id
                    WHERE a.status = ?
                      AND (
                          a.title LIKE ?
                          OR a.body LIKE ?
                      )
                    ORDER BY a.created_at DESC, a.id DESC
                ";

                $stmt = $conn->prepare($sql);
                $searchTerm = '%' . $query . '%';

                $stmt->execute([
                    'published',
                    $searchTerm,
                    $searchTerm
                ]);

                $results = $stmt->fetchAll();

            } catch (Throwable $e) {
                error_log(
                    "Roma article search error: " . $e->getMessage()
                );

                $error = "جست‌وجو در حال حاضر انجام نشد. لطفاً دوباره تلاش کنید.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>جست‌وجو در روما | مجله اینترنتی</title>

    <meta
        name="description"
        content="مقالات منتشرشدهٔ روما را بر اساس عنوان یا متن جست‌وجو کنید."
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta name="robots" content="noindex, follow">

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
        href="<?php echo asset('assets/Css/roma-footer.css'); ?>"
    >

    <link
        rel="stylesheet"
        href="<?php echo asset('assets/Css/search.css'); ?>"
    >

</head>

<body class="rtl roma-search-page">

<div class="main_wrap">

    <div class="of-site-mask"></div>

    <?php include "layouts/header.php"; ?>

    <div class="clearfix"></div>

    <main class="roma-search-main">

        <section class="roma-search-hero">

            <span class="roma-search-eyebrow">
                مجله روما
            </span>

            <h1>
                دنبال چه چیزی می‌گردی؟
            </h1>

            <p>
                عنوان یا بخشی از متن مقاله را وارد کن تا مطالب مرتبط را پیدا کنی.
            </p>

            <form
                action="search.php"
                method="GET"
                class="roma-search-form"
                role="search"
            >

                <label for="search-query" class="roma-search-label">
                    عبارت جست‌وجو
                </label>

                <input
                    type="search"
                    name="q"
                    id="search-query"
                    value="<?php
                    echo htmlspecialchars(
                        $query,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                    placeholder="مثلاً برنامه‌نویسی، آموزش، فناوری..."
                    maxlength="100"
                    required
                >

                <button type="submit">
                    <span aria-hidden="true">⌕</span>
                    جست‌وجو
                </button>

            </form>

        </section>

        <section class="roma-search-results">

            <?php if ($error !== '') { ?>

                <div class="roma-search-state roma-search-error" role="alert">
                    <?php
                    echo htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </div>

            <?php } elseif ($searched && $query === '') { ?>

                <div class="roma-search-state">
                    عبارت موردنظر را در کادر جست‌وجو وارد کن.
                </div>

            <?php } elseif ($searched && empty($results)) { ?>

                <div class="roma-search-state roma-search-empty">

                    <span aria-hidden="true">⌕</span>

                    <h2>مقاله‌ای پیدا نشد</h2>

                    <p>
                        برای «<?php
                        echo htmlspecialchars(
                            $query,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>» نتیجه‌ای پیدا نکردیم. عبارت دیگری را امتحان کن.
                    </p>

                    <a href="articles.php">
                        مشاهده همه مقالات
                    </a>

                </div>

            <?php } elseif ($searched) { ?>

                <div class="roma-search-results-heading">

                    <div>
                        <h2>نتایج جست‌وجو</h2>

                        <p>
                            نتایج برای عبارت
                            «<?php
                            echo htmlspecialchars(
                                $query,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>»
                        </p>
                    </div>

                    <span class="roma-search-count">
                        <?php echo count($results); ?> مقاله
                    </span>

                </div>

                <div class="roma-search-grid">

                    <?php foreach ($results as $article) { ?>

                        <?php
                        $description = trim(
                            strip_tags((string) $article->body)
                        );

                        if (mb_strlen($description, 'UTF-8') > 160) {
                            $description = mb_substr(
                                $description,
                                0,
                                160,
                                'UTF-8'
                            ) . '…';
                        }

                        $imageFile = basename(
                            str_replace(
                                '\\',
                                '/',
                                (string) ($article->image ?? '')
                            )
                        );
                        ?>

                        <article class="roma-search-card">

                            <a
                                class="roma-search-card-image-link"
                                href="blog_details.php?id=<?php echo (int) $article->id; ?>"
                            >

                                <?php if ($imageFile !== '') { ?>

                                    <img
                                        class="roma-search-card-image"
                                        src="<?php
                                        echo htmlspecialchars(
                                            asset(
                                                'images/articles/'
                                                . rawurlencode($imageFile)
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>"
                                        alt="<?php
                                        echo htmlspecialchars(
                                            $article->title,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>"
                                        loading="lazy"
                                    >

                                <?php } else { ?>

                                    <div
                                        class="roma-search-card-placeholder"
                                        aria-hidden="true"
                                    >
                                        ر
                                    </div>

                                <?php } ?>

                            </a>

                            <div class="roma-search-card-content">

                                <?php if (!empty($article->category_title)) { ?>

                                    <span class="roma-search-category">
                                        <?php
                                        echo htmlspecialchars(
                                            $article->category_title,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </span>

                                <?php } ?>

                                <h3>
                                    <a href="blog_details.php?id=<?php echo (int) $article->id; ?>">
                                        <?php
                                        echo htmlspecialchars(
                                            $article->title,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </a>
                                </h3>

                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $description,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </p>

                                <a
                                    href="blog_details.php?id=<?php echo (int) $article->id; ?>"
                                    class="roma-search-read-more"
                                >
                                    مطالعه مقاله
                                    <span aria-hidden="true">←</span>
                                </a>

                            </div>

                        </article>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <div class="roma-search-start">

                    <h2>مطالب روما را کشف کن</h2>

                    <p>
                        عبارت موردنظرت را جست‌وجو کن یا برای مرور مطالب به صفحه مقالات برو.
                    </p>

                    <a href="articles.php">
                        مشاهده همه مقالات
                        <span aria-hidden="true">←</span>
                    </a>

                </div>

            <?php } ?>

        </section>

    </main>

    <?php include "layouts/footer.php"; ?>

</div>

<script src="<?php echo asset('assets/Js/jquery.min.js'); ?>"></script>
<script src="<?php echo asset('assets/Js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo asset('assets/Js/owl.carousel.js'); ?>"></script>
<script src="<?php echo asset('assets/Js/typed.min.js'); ?>"></script>
<script src="<?php echo asset('assets/Js/intlTelInput.min.js'); ?>"></script>
<script src="<?php echo asset('assets/Js/my-script.js'); ?>"></script>
<script src="<?php echo asset('assets/Js/custom.js'); ?>"></script>

</body>
</html>