<?php

session_start();

/* بررسی ورود مدیر */
if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit;
}

/* فایل‌های اصلی پروژه */
require_once "../../functions/helpers.php";
require_once "../../functions/articles.php";
require_once "../../functions/categories.php";
require_once "../../functions/users.php";
require_once "../../functions/visitor_users.php";

/* فعال‌کردن CSS اختصاصی این صفحه */
$panelArticleEditCss = true;

/* دریافت شناسه مقاله */
$rawId = $_GET['id'] ?? null;

$id = (
    is_string($rawId) || is_int($rawId)
)
    ? filter_var($rawId, FILTER_VALIDATE_INT)
    : false;

if ($id === false || $id === null || $id < 1) {
    header('Location: index.php');
    exit;
}

$id = (int) $id;

/* دریافت مقاله */
$article = getArticleById($id);

if (!$article) {
    header('Location: index.php');
    exit;
}

/* دریافت دسته‌بندی‌ها */
$categories = getAllCategories();

/* مقادیر اولیه فرم */
$title = $article->title ?? '';
$body = $article->body ?? '';
$category_id = (string) ($article->category_id ?? '');
$status = $article->status ?? 'draft';

$error = '';

/* ایجاد توکن امنیتی فرم */
if (
    !isset($_SESSION['panel_article_edit_csrf'])
    || !is_string($_SESSION['panel_article_edit_csrf'])
    || $_SESSION['panel_article_edit_csrf'] === ''
) {
    $_SESSION['panel_article_edit_csrf'] = bin2hex(
        random_bytes(32)
    );
}

/* ثبت تغییرات */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['submit'])
) {

    /* دریافت ورودی‌ها با بررسی نوع داده */
    $rawTitle = $_POST['title'] ?? '';
    $rawBody = $_POST['body'] ?? '';
    $rawCategoryId = $_POST['category_id'] ?? '';
    $rawStatus = $_POST['status'] ?? '';

    $title = is_string($rawTitle)
        ? trim($rawTitle)
        : '';

    $body = is_string($rawBody)
        ? trim($rawBody)
        : '';

    $category_id = is_string($rawCategoryId)
        ? $rawCategoryId
        : '';

    $status = is_string($rawStatus)
        ? $rawStatus
        : '';

    $validStatuses = [
        'draft',
        'published',
        'archived'
    ];

    $validCategoryIds = array_map(
        function ($category) {
            return (string) $category->id;
        },
        $categories
    );

    /* دریافت امن توکن */
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['panel_article_edit_csrf'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | اعتبارسنجی CSRF
    |--------------------------------------------------------------------------
    */

    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {

        $error = 'درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.';

    } elseif ($title === '' || $body === '') {

        $error = 'لطفاً عنوان و متن مقاله را وارد کنید.';

    } elseif (
        !ctype_digit($category_id)
        || !in_array($category_id, $validCategoryIds, true)
    ) {

        $error = 'لطفاً یک دسته‌بندی معتبر انتخاب کنید.';

    } elseif (
        !in_array($status, $validStatuses, true)
    ) {

        $error = 'وضعیت انتخاب‌شده معتبر نیست.';

    } else {

        /*
         * انتخاب تصویر جدید اختیاری است.
         * در صورت انتخاب نکردن تصویر، تصویر قبلی حفظ می‌شود.
         */
        $image = (
            isset($_FILES['image'])
            && is_array($_FILES['image'])
        )
            ? $_FILES['image']
            : null;

        try {

            /* بررسی مجدد وجود مقاله قبل از ویرایش */
            $currentArticle = getArticleById($id);

            if (!$currentArticle) {

                $error = 'مقاله موردنظر پیدا نشد.';

            } else {

                $updated = updateArticle(
                    $id,
                    $title,
                    $body,
                    (int) $category_id,
                    $status,
                    $image
                );

                if ($updated === true) {

                    unset(
                        $_SESSION['panel_article_edit_csrf']
                    );

                    header('Location: index.php');
                    exit;

                } else {

                    $error = 'ذخیره تغییرات انجام نشد. اگر تصویر جدید انتخاب کرده‌اید، معتبر بودن تصویر و حجم آن را بررسی کنید و دوباره تلاش کنید.';
                }
            }

        } catch (Throwable $e) {

            error_log(
                'Roma article update error: ' . $e->getMessage()
            );

            $error = 'هنگام ذخیره تغییرات خطایی رخ داد. اطلاعات فرم را بررسی کنید و دوباره تلاش کنید.';
        }
    }
}

/* دریافت اطلاعات نویسنده */
$author = null;
$authorType = '';

if (!empty($article->visitor_id)) {

    $author = getVisitorById($article->visitor_id);
    $authorType = 'بازدیدکننده';

} elseif (!empty($article->user_id)) {

    $author = getUserById($article->user_id);
    $authorType = 'مدیر';
}

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content">

    <div class="roma-edit-article-page">

        <!-- سربرگ -->
        <section class="roma-edit-hero">

            <div class="roma-edit-hero-content">

                <span class="roma-edit-badge">
                    مدیریت محتوای روما
                </span>

                <h1>ویرایش مقاله</h1>

                <p>
                    اطلاعات مقاله را بررسی و اصلاح کنید.
                    تغییرات پس از ذخیره در سایت اعمال خواهند شد.
                </p>

            </div>

            <div class="roma-edit-hero-icon" aria-hidden="true">
                ✎
            </div>

        </section>

        <!-- اطلاعات نویسنده -->
        <?php if ($author) { ?>

            <section class="roma-edit-author">

                <div class="roma-edit-author-icon" aria-hidden="true">
                    <?php echo $authorType === 'بازدیدکننده' ? '♙' : '✓'; ?>
                </div>

                <div class="roma-edit-author-info">

                    <span class="roma-edit-author-label">
                        نویسنده مقاله
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $author->name ?? 'نامشخص',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </strong>

                </div>

                <span class="<?php echo $authorType === 'بازدیدکننده'
                    ? 'roma-edit-author-badge visitor'
                    : 'roma-edit-author-badge admin'; ?>">
                    <?php
                    echo htmlspecialchars(
                        $authorType,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>

            </section>

        <?php } ?>

        <!-- فرم ویرایش -->
        <section class="roma-edit-card">

            <div class="roma-edit-card-header">

                <div>
                    <h2>اطلاعات مقاله</h2>
                    <p>فیلدهای موردنظر را تغییر دهید و ذخیره کنید.</p>
                </div>

                <a
                    href="index.php"
                    class="roma-edit-back-link"
                >
                    بازگشت به فهرست
                    <span aria-hidden="true">←</span>
                </a>

            </div>

            <?php if ($error !== '') { ?>

                <div class="roma-edit-error" role="alert">

                    <span class="roma-edit-error-icon" aria-hidden="true">
                        !
                    </span>

                    <?php
                    echo htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            <?php } ?>

            <form
                method="POST"
                action="edit.php?id=<?php echo $id; ?>"
                enctype="multipart/form-data"
                class="roma-edit-form"
            >

                <!-- توکن امنیتی CSRF -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo htmlspecialchars(
                        $_SESSION['panel_article_edit_csrf'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                >

                <!-- عنوان -->
                <div class="roma-edit-field">

                    <label for="article-title">
                        عنوان مقاله
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="article-title"
                        name="title"
                        value="<?php
                        echo htmlspecialchars(
                            $title,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                        placeholder="عنوان مقاله را وارد کنید..."
                        maxlength="255"
                        required
                    >

                </div>

                <!-- متن -->
                <div class="roma-edit-field">

                    <label for="article-body">
                        متن مقاله
                        <span>*</span>
                    </label>

                    <textarea
                        id="article-body"
                        name="body"
                        rows="12"
                        placeholder="متن مقاله را بنویسید..."
                        required
                    ><?php
                    echo htmlspecialchars(
                        $body,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?></textarea>

                </div>

                <!-- دسته‌بندی و وضعیت -->
                <div class="roma-edit-fields-grid">

                    <div class="roma-edit-field">

                        <label for="article-category">
                            دسته‌بندی
                            <span>*</span>
                        </label>

                        <select
                            id="article-category"
                            name="category_id"
                            required
                        >

                            <?php foreach ($categories as $category) { ?>

                                <option
                                    value="<?php echo (int) $category->id; ?>"
                                    <?php echo (string) $category_id ===
                                        (string) $category->id
                                        ? 'selected'
                                        : ''; ?>
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $category->title,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="roma-edit-field">

                        <label for="article-status">
                            وضعیت انتشار
                            <span>*</span>
                        </label>

                        <select
                            id="article-status"
                            name="status"
                            required
                        >

                            <option
                                value="draft"
                                <?php echo $status === 'draft'
                                    ? 'selected'
                                    : ''; ?>
                            >
                                پیش‌نویس
                            </option>

                            <option
                                value="published"
                                <?php echo $status === 'published'
                                    ? 'selected'
                                    : ''; ?>
                            >
                                منتشرشده
                            </option>

                            <option
                                value="archived"
                                <?php echo $status === 'archived'
                                    ? 'selected'
                                    : ''; ?>
                            >
                                بایگانی‌شده
                            </option>

                        </select>

                    </div>

                </div>

                <!-- تصویر فعلی -->
                <div class="roma-edit-field">

                    <label>تصویر فعلی مقاله</label>

                    <?php if (!empty($article->image)) { ?>

                        <div class="roma-edit-current-image">

                            <img
                                src="<?php
                                echo htmlspecialchars(
                                    asset(
                                        'images/articles/' .
                                        $article->image
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                alt="<?php
                                echo htmlspecialchars(
                                    $article->title ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                            >

                            <div>
                                <strong>تصویر فعلی</strong>
                                <p>
                                    اگر تصویر جدید انتخاب نکنید،
                                    همین تصویر حفظ می‌شود.
                                </p>
                            </div>

                        </div>

                    <?php } else { ?>

                        <div class="roma-edit-no-image">
                            <span aria-hidden="true">▧</span>

                            <div>
                                <strong>این مقاله تصویر ندارد.</strong>
                                <p>
                                    می‌توانید در صورت نیاز تصویر جدیدی انتخاب کنید.
                                </p>
                            </div>
                        </div>

                    <?php } ?>

                </div>

                <!-- تصویر جدید -->
                <div class="roma-edit-field">

                    <label for="article-image">
                        تغییر تصویر مقاله
                    </label>

                    <div class="roma-edit-upload">

                        <div
                            class="roma-edit-upload-icon"
                            aria-hidden="true"
                        >
                            ↑
                        </div>

                        <div class="roma-edit-upload-content">

                            <strong>انتخاب تصویر جدید</strong>

                            <p>
                                انتخاب تصویر جدید اختیاری است.
                                با خالی گذاشتن این قسمت، تصویر فعلی باقی می‌ماند.
                            </p>

                            <input
                                type="file"
                                id="article-image"
                                name="image"
                                accept="image/*"
                            >

                        </div>

                    </div>

                </div>

                <!-- دکمه‌ها -->
                <div class="roma-edit-actions">

                    <button
                        type="submit"
                        name="submit"
                        value="1"
                        class="roma-edit-submit"
                    >
                        <span aria-hidden="true">✓</span>
                        ذخیره تغییرات
                    </button>

                    <a
                        href="index.php"
                        class="roma-edit-cancel"
                    >
                        انصراف
                    </a>

                </div>

            </form>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>