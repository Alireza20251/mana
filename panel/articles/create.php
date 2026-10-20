<?php

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit;
}

/* فایل‌های اصلی پروژه */
require_once "../../functions/helpers.php";
require_once "../../functions/articles.php";
require_once "../../functions/categories.php";
require_once "../../functions/users.php";

/* فعال‌کردن استایل اختصاصی صفحه */
$panelArticleCreateCss = true;

/* بررسی کاربر واردشده */
$user = checkUser($_SESSION['user']);

if (!$user) {
    header('Location: ../../auth/login.php');
    exit;
}

$user_id = $user->id;

/* دریافت دسته‌بندی‌ها */
$categories = getAllCategories();

/* مقادیر اولیه فرم */
$error = "";
$title = "";
$body = "";
$category_id = "";
$status = "draft";

/* ایجاد توکن امنیتی فرم */
if (
    !isset($_SESSION['panel_article_create_csrf'])
    || !is_string($_SESSION['panel_article_create_csrf'])
    || $_SESSION['panel_article_create_csrf'] === ''
) {
    $_SESSION['panel_article_create_csrf'] = bin2hex(
        random_bytes(32)
    );
}

/* ثبت مقاله */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | دریافت امن ورودی‌ها
    |--------------------------------------------------------------------------
    */

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

    $image = $_FILES['image'] ?? null;

    /*
    |--------------------------------------------------------------------------
    | اعتبارسنجی توکن CSRF
    |--------------------------------------------------------------------------
    */

    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['panel_article_create_csrf'] ?? '';

    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {

        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | بررسی فیلدهای ضروری و آپلود تصویر
        |--------------------------------------------------------------------------
        */

        $validImageUpload = (
            is_array($image)
            && isset($image['name'], $image['error'])
            && is_string($image['name'])
            && $image['name'] !== ''
            && is_int($image['error'])
            && $image['error'] === UPLOAD_ERR_OK
        );

        if (
            $title === ''
            || $body === ''
            || $category_id === ''
            || !$validImageUpload
        ) {

            $error = "لطفاً تمام فیلدها و تصویر مقاله را تکمیل کنید.";

        } elseif (
            !in_array(
                $status,
                ['draft', 'published', 'archived'],
                true
            )
        ) {

            $error = "وضعیت انتخاب‌شده معتبر نیست.";

        } elseif (
            !ctype_digit($category_id)
            || (int) $category_id < 1
        ) {

            $error = "دسته‌بندی انتخاب‌شده معتبر نیست.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | بررسی وجود دسته‌بندی
            |--------------------------------------------------------------------------
            */

            $categoryExists = false;

            foreach ($categories as $category) {

                if ((int) $category->id === (int) $category_id) {
                    $categoryExists = true;
                    break;
                }
            }

            if (!$categoryExists) {

                $error = "دسته‌بندی انتخاب‌شده وجود ندارد. لطفاً دوباره انتخاب کنید.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | ثبت مقاله و بررسی نتیجه
                |--------------------------------------------------------------------------
                */

                try {

                    $created = createArticle(
                        $title,
                        $body,
                        $user_id,
                        (int) $category_id,
                        $status,
                        $image
                    );

                    if ($created === true) {

                        unset(
                            $_SESSION['panel_article_create_csrf']
                        );

                        header('Location: index.php');
                        exit;

                    } else {

                        $error = "مقاله ذخیره نشد. لطفاً معتبر بودن تصویر و حجم آن را بررسی کنید و دوباره تلاش کنید.";

                    }

                } catch (Throwable $e) {

                    error_log(
                        "Roma article creation error: "
                        . $e->getMessage()
                    );

                    $error = "هنگام ذخیره مقاله خطایی رخ داد. لطفاً دوباره تلاش کنید.";
                }
            }
        }
    }
}

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content">

    <div class="roma-create-article-page">

        <!-- سربرگ صفحه -->
        <section class="roma-create-hero">

            <div>
                <span class="roma-create-badge">
                    مدیریت محتوای روما
                </span>

                <h1>افزودن مقاله جدید</h1>

                <p>
                    محتوای جدید خود را ثبت کنید و پس از تکمیل اطلاعات،
                    آن را ذخیره یا برای انتشار آماده کنید.
                </p>
            </div>

            <div class="roma-create-hero-icon" aria-hidden="true">
                ✎
            </div>

        </section>

        <!-- فرم مقاله -->
        <section class="roma-create-card">

            <div class="roma-create-card-header">

                <div>
                    <h2>اطلاعات مقاله</h2>
                    <p>فیلدهای زیر را با دقت تکمیل کنید.</p>
                </div>

                <span class="roma-create-required">
                    * فیلدهای الزامی
                </span>

            </div>

            <?php if ($error !== '') { ?>

                <div
                    class="roma-create-alert roma-create-alert-error"
                    role="alert"
                >

                    <span aria-hidden="true">!</span>

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
                action="create.php"
                enctype="multipart/form-data"
                class="roma-create-form"
            >

                <!-- توکن امنیتی -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo htmlspecialchars(
                        $_SESSION['panel_article_create_csrf'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                >

                <!-- عنوان مقاله -->
                <div class="roma-create-field">

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

                    <small>
                        عنوانی روشن و مرتبط با محتوای مقاله انتخاب کنید.
                    </small>

                </div>

                <!-- متن مقاله -->
                <div class="roma-create-field">

                    <label for="article-body">
                        متن مقاله
                        <span>*</span>
                    </label>

                    <textarea
                        id="article-body"
                        name="body"
                        rows="12"
                        placeholder="متن مقاله را اینجا بنویسید..."
                        required
                    ><?php
                    echo htmlspecialchars(
                        $body,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?></textarea>

                    <small>
                        متن مقاله را خوانا و با فاصله‌بندی مناسب وارد کنید.
                    </small>

                </div>

                <!-- دسته‌بندی و وضعیت -->
                <div class="roma-create-fields-grid">

                    <div class="roma-create-field">

                        <label for="article-category">
                            دسته‌بندی
                            <span>*</span>
                        </label>

                        <select
                            id="article-category"
                            name="category_id"
                            required
                        >

                            <option
                                value=""
                                disabled
                                <?php echo $category_id === ''
                                    ? 'selected'
                                    : ''; ?>
                            >
                                انتخاب دسته‌بندی
                            </option>

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

                    <div class="roma-create-field">

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

                <!-- آپلود تصویر -->
                <div class="roma-create-field">

                    <label for="article-image">
                        تصویر مقاله
                        <span>*</span>
                    </label>

                    <div class="roma-create-upload">

                        <div
                            class="roma-create-upload-icon"
                            aria-hidden="true"
                        >
                            ↑
                        </div>

                        <div class="roma-create-upload-content">

                            <strong>
                                تصویر مقاله را انتخاب کنید
                            </strong>

                            <p>
                                یک فایل تصویری از دستگاه خود انتخاب کنید.
                            </p>

                            <input
                                type="file"
                                id="article-image"
                                name="image"
                                accept="image/*"
                                required
                            >

                        </div>

                    </div>

                    <small>
                        انتخاب تصویر برای ثبت مقاله الزامی است.
                    </small>

                </div>

                <!-- دکمه‌ها -->
                <div class="roma-create-actions">

                    <button
                        type="submit"
                        name="submit"
                        value="1"
                        class="roma-create-submit"
                    >
                        <span aria-hidden="true">✓</span>
                        ذخیره مقاله
                    </button>

                    <a
                        href="index.php"
                        class="roma-create-cancel"
                    >
                        بازگشت به فهرست مقالات
                    </a>

                </div>

            </form>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>