<?php

session_start();

/* بررسی ورود مدیر */
if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit;
}

/* فایل‌های اصلی پروژه */
require_once "../../functions/helpers.php";
require_once "../../functions/users.php";
require_once "../../functions/visitor_users.php";
require_once "../../functions/articles.php";
require_once "../../functions/categories.php";

/* فعال‌کردن CSS اختصاصی */
$panelArticlesCss = true;

/* جستجو بر اساس عنوان مقاله */
$searchTitle = trim((string) ($_GET['title'] ?? ''));

/* دریافت مقالات */
$articles = getAllArticles();

/* فیلتر جستجو بدون تغییر تابع اصلی مقالات */
if ($searchTitle !== '') {
    $articles = array_values(array_filter(
        $articles,
        function ($article) use ($searchTitle) {
            $title = (string) ($article->title ?? '');

            if (function_exists('mb_stripos')) {
                return mb_stripos(
                    $title,
                    $searchTitle,
                    0,
                    'UTF-8'
                ) !== false;
            }

            return stripos($title, $searchTitle) !== false;
        }
    ));
}

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content">

    <div class="roma-admin-articles-page">

        <!-- سربرگ صفحه -->
        <section class="roma-admin-page-header">

            <div class="roma-admin-page-heading">
                <span class="roma-admin-page-badge">
                    مدیریت محتوای روما
                </span>

                <h1>مدیریت مقالات</h1>

                <p>
                    مقالات سایت را مشاهده کنید، وضعیت انتشار آن‌ها را بررسی
                    کنید و در صورت نیاز مطالب را ویرایش کنید.
                </p>
            </div>

            <a
                href="<?php echo url('panel/articles/create.php'); ?>"
                class="roma-admin-add-button"
            >
                <span aria-hidden="true">+</span>
                افزودن مقاله
            </a>

        </section>


        <!-- بخش فهرست مقالات -->
        <section class="roma-admin-articles-card">

            <div class="roma-admin-articles-toolbar">

                <div class="roma-admin-articles-toolbar-title">
                    <h2>فهرست مقالات</h2>

                    <span class="roma-admin-articles-count">
                        <?php echo number_format(count($articles)); ?>
                        مقاله
                    </span>
                </div>

                <!-- جستجوی عنوان -->
                <form
                    method="get"
                    action=""
                    class="roma-admin-article-search"
                >

                    <label for="article-title-search">
                        جستجو
                    </label>

                    <div class="roma-admin-search-control">

                        <input
                            type="search"
                            id="article-title-search"
                            name="title"
                            value="<?php echo htmlspecialchars(
                                $searchTitle,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            placeholder="عنوان مقاله را بنویسید..."
                        >

                        <button type="submit">
                            جستجو
                        </button>

                    </div>

                    <?php if ($searchTitle !== '') { ?>
                        <a
                            href="<?php echo htmlspecialchars(
                                url('panel/articles/index.php'),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="roma-admin-search-reset"
                        >
                            پاک کردن جستجو
                        </a>
                    <?php } ?>

                </form>

            </div>


            <!-- جدول -->
            <div
                class="roma-admin-table-wrapper"
                tabindex="0"
                role="region"
                aria-label="فهرست مقالات"
            >

                <table class="roma-admin-articles-table">

                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>تصویر</th>
                            <th class="roma-admin-title-column">
                                عنوان مقاله
                            </th>
                            <th>نویسنده</th>
                            <th>دسته‌بندی</th>
                            <th>وضعیت</th>
                            <th>ویرایش</th>
                            <th>حذف</th>
                            <th>تاریخ ایجاد</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($articles)) { ?>

                        <?php $i = 1; ?>

                        <?php foreach ($articles as $article) { ?>

                            <tr>

                                <!-- ردیف -->
                                <td class="roma-admin-row-number">
                                    <?php echo $i++; ?>
                                </td>


                                <!-- تصویر مقاله -->
                                <td>
                                    <div class="roma-admin-article-thumb">

                                        <?php if (!empty($article->image)) { ?>

                                            <img
                                                src="<?php echo htmlspecialchars(
                                                    asset(
                                                        'images/articles/' .
                                                        $article->image
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>"
                                                alt="<?php echo htmlspecialchars(
                                                    $article->title ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>"
                                                loading="lazy"
                                            >

                                        <?php } else { ?>

                                            <span
                                                class="roma-admin-no-image"
                                                aria-label="بدون تصویر"
                                                title="بدون تصویر"
                                            >
                                                —
                                            </span>

                                        <?php } ?>

                                    </div>
                                </td>


                                <!-- عنوان -->
                                <td class="roma-admin-article-title">

                                    <?php echo htmlspecialchars(
                                        $article->title ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </td>


                                <!-- نویسنده -->
                                <td>

                                    <?php if (!empty($article->visitor_id)) { ?>

                                        <?php
                                        $visitor = getVisitorById(
                                            $article->visitor_id
                                        );
                                        ?>

                                        <div class="roma-admin-author">

                                            <span>
                                                <?php
                                                echo htmlspecialchars(
                                                    $visitor->name ?? 'بازدیدکننده',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>
                                            </span>

                                            <span class="roma-admin-author-badge">
                                                بازدیدکننده
                                            </span>

                                        </div>

                                    <?php } else { ?>

                                        <?php
                                        $user = getUserById($article->user_id);
                                        ?>

                                        <span>
                                            <?php
                                            echo htmlspecialchars(
                                                $user->name ?? 'نامشخص',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>
                                        </span>

                                    <?php } ?>

                                </td>


                                <!-- دسته‌بندی -->
                                <td>

                                    <?php
                                    $category = getCategoryById(
                                        $article->category_id
                                    );
                                    ?>

                                    <span class="roma-admin-category">
                                        <?php
                                        echo htmlspecialchars(
                                            $category->title ?? 'نامشخص',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </span>

                                </td>


                                <!-- وضعیت انتشار -->
                                <td>
                                    <?php echo status($article->status); ?>
                                </td>


                                <!-- ویرایش -->
                                <td>

                                    <a
                                        class="roma-admin-action roma-admin-edit"
                                        href="<?php echo htmlspecialchars(
                                            url('panel/articles/edit.php?id=') .
                                            (int) $article->id,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >
                                        ویرایش
                                    </a>

                                </td>


                                <!-- حذف -->
                                <td>

                                    <a
                                        class="roma-admin-action roma-admin-delete"
                                        href="<?php echo htmlspecialchars(
                                            url('panel/articles/delete.php?id=') .
                                            (int) $article->id,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >
                                        حذف
                                    </a>

                                </td>


                                <!-- تاریخ ایجاد -->
                                <td class="roma-admin-date">

                                    <?php
                                    echo htmlspecialchars(
                                        $article->created_at ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </td>

                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td
                                colspan="9"
                                class="roma-admin-empty-cell"
                            >

                                <div class="roma-admin-empty">

                                    <span
                                        class="roma-admin-empty-icon"
                                        aria-hidden="true"
                                    >
                                        ✎
                                    </span>

                                    <h3>
                                        <?php if ($searchTitle !== '') { ?>
                                            مقاله‌ای با این عنوان پیدا نشد
                                        <?php } else { ?>
                                            هنوز مقاله‌ای ثبت نشده است
                                        <?php } ?>
                                    </h3>

                                    <p>
                                        <?php if ($searchTitle !== '') { ?>
                                            عبارت دیگری را برای جستجو امتحان کنید.
                                        <?php } else { ?>
                                            می‌توانید اولین مقاله روما را ثبت کنید.
                                        <?php } ?>
                                    </p>

                                    <?php if ($searchTitle === '') { ?>
                                        <a
                                            href="<?php echo url(
                                                'panel/articles/create.php'
                                            ); ?>"
                                            class="roma-admin-add-button"
                                        >
                                            افزودن مقاله
                                        </a>
                                    <?php } ?>

                                </div>

                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

            <div class="roma-admin-table-footer">
                تعداد نتایج:
                <strong>
                    <?php echo number_format(count($articles)); ?>
                </strong>
            </div>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>