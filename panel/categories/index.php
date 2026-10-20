<?php

session_start();

/* بررسی ورود مدیر */
if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit;
}

/* فایل‌های اصلی پروژه */
require_once "../../functions/helpers.php";
require_once "../../functions/categories.php";

/* فعال کردن CSS اختصاصی این صفحه */
$panelCategoriesCss = true;

/* جستجو در عنوان دسته‌بندی */
$searchTitle = trim((string) ($_GET['title'] ?? ''));

/* دریافت دسته‌بندی‌ها */
$categories = getAllCategories();

/* فیلتر جستجو بدون تغییر توابع اصلی */
if ($searchTitle !== '') {
    $categories = array_values(array_filter(
        $categories,
        function ($category) use ($searchTitle) {
            $title = (string) ($category->title ?? '');

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

    <div class="roma-admin-categories-page">

        <!-- سربرگ صفحه -->
        <section class="roma-categories-hero">

            <div class="roma-categories-hero-content">

                <span class="roma-categories-badge">
                    مدیریت محتوای روما
                </span>

                <h1>مدیریت دسته‌بندی‌ها</h1>

                <p>
                    موضوعات مقالات روما را مدیریت کنید تا مطالب سایت
                    به شکل منظم و قابل‌دسترسی دسته‌بندی شوند.
                </p>

            </div>

            <a
                href="<?php echo url('panel/categories/create.php'); ?>"
                class="roma-categories-add-button"
            >
                <span aria-hidden="true">+</span>
                افزودن دسته‌بندی
            </a>

        </section>


        <!-- کارت فهرست دسته‌بندی‌ها -->
        <section class="roma-categories-card">

            <div class="roma-categories-toolbar">

                <div class="roma-categories-toolbar-title">

                    <h2>فهرست دسته‌بندی‌ها</h2>

                    <span class="roma-categories-count">
                        <?php echo number_format(count($categories)); ?>
                        دسته‌بندی
                    </span>

                </div>


                <!-- جستجو -->
                <form
                    method="get"
                    action=""
                    class="roma-categories-search"
                >

                    <label for="category-title-search">
                        جستجو بر اساس عنوان
                    </label>

                    <div class="roma-categories-search-control">

                        <input
                            type="search"
                            id="category-title-search"
                            name="title"
                            value="<?php echo htmlspecialchars(
                                $searchTitle,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            placeholder="عنوان دسته‌بندی را بنویسید..."
                        >

                        <button type="submit">
                            جستجو
                        </button>

                    </div>

                    <?php if ($searchTitle !== '') { ?>

                        <a
                            href="<?php echo htmlspecialchars(
                                url('panel/categories/index.php'),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="roma-categories-reset"
                        >
                            پاک کردن جستجو
                        </a>

                    <?php } ?>

                </form>

            </div>


            <!-- جدول دسته‌بندی‌ها -->
            <div
                class="roma-categories-table-wrapper"
                tabindex="0"
                role="region"
                aria-label="فهرست دسته‌بندی‌های روما"
            >

                <table class="roma-categories-table">

                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th class="roma-category-title-column">
                                عنوان دسته‌بندی
                            </th>
                            <th>ویرایش</th>
                            <th>حذف</th>
                            <th>تاریخ ایجاد</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($categories)) { ?>

                        <?php $i = 1; ?>

                        <?php foreach ($categories as $category) { ?>

                            <tr>

                                <td class="roma-category-row-number">
                                    <?php echo $i++; ?>
                                </td>

                                <td class="roma-category-title">

                                    <div class="roma-category-title-content">

                                        <span
                                            class="roma-category-title-icon"
                                            aria-hidden="true"
                                        >
                                            ▤
                                        </span>

                                        <span>
                                            <?php echo htmlspecialchars(
                                                $category->title ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </span>

                                    </div>

                                </td>

                                <td>

                                    <a
                                        class="roma-category-action roma-category-edit"
                                        href="<?php echo htmlspecialchars(
                                            url('panel/categories/edit.php?id=') .
                                            (int) $category->id,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >
                                        ویرایش
                                    </a>

                                </td>

                                <td>

                                    <a
                                        class="roma-category-action roma-category-delete"
                                        href="<?php echo htmlspecialchars(
                                            url('panel/categories/delete.php?id=') .
                                            (int) $category->id,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >
                                        حذف
                                    </a>

                                </td>

                                <td class="roma-category-date">
                                    <?php echo htmlspecialchars(
                                        $category->created_at ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </td>

                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="5" class="roma-categories-empty-cell">

                                <div class="roma-categories-empty">

                                    <span
                                        class="roma-categories-empty-icon"
                                        aria-hidden="true"
                                    >
                                        ▤
                                    </span>

                                    <h3>
                                        <?php if ($searchTitle !== '') { ?>
                                            دسته‌بندی موردنظر پیدا نشد
                                        <?php } else { ?>
                                            هنوز دسته‌بندی‌ای ثبت نشده است
                                        <?php } ?>
                                    </h3>

                                    <p>
                                        <?php if ($searchTitle !== '') { ?>
                                            عبارت دیگری را برای جستجو امتحان کنید.
                                        <?php } else { ?>
                                            برای مرتب‌سازی مقالات، اولین دسته‌بندی را بسازید.
                                        <?php } ?>
                                    </p>

                                    <?php if ($searchTitle === '') { ?>

                                        <a
                                            href="<?php echo url(
                                                'panel/categories/create.php'
                                            ); ?>"
                                            class="roma-categories-add-button"
                                        >
                                            <span aria-hidden="true">+</span>
                                            افزودن دسته‌بندی
                                        </a>

                                    <?php } ?>

                                </div>

                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>


            <!-- پایین جدول -->
            <div class="roma-categories-footer">

                تعداد نتایج:

                <strong>
                    <?php echo number_format(count($categories)); ?>
                </strong>

            </div>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>