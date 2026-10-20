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

/* فعال‌کردن CSS اختصاصی این صفحه */
$panelCategoryCreateCss = true;

$error = "";
$title = "";

/* ایجاد توکن امنیتی فرم */
if (
    !isset($_SESSION['create_category_csrf'])
    || !is_string($_SESSION['create_category_csrf'])
    || $_SESSION['create_category_csrf'] === ''
) {
    $_SESSION['create_category_csrf'] = bin2hex(
        random_bytes(32)
    );
}

/* ثبت دسته‌بندی */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['submit'])
) {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['create_category_csrf'] ?? '';

    /* بررسی توکن CSRF */
    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } else {
        /* اعتبارسنجی عنوان */
        $rawTitle = $_POST['title'] ?? '';

        if (!is_string($rawTitle)) {
            $error = "عنوان دسته‌بندی معتبر نیست.";

        } else {
            $title = trim($rawTitle);

            if ($title === '') {
                $error = "لطفاً عنوان دسته‌بندی را وارد کنید.";

            } else {
                try {
                    $created = createCategory($title);

                    if ($created === true) {
                        unset($_SESSION['create_category_csrf']);

                        header('Location: index.php');
                        exit;
                    }

                    $error = "ثبت دسته‌بندی انجام نشد. دوباره تلاش کنید.";

                } catch (Throwable $e) {
                    error_log(
                        "Roma category create error: " . $e->getMessage()
                    );

                    $error = "هنگام ثبت دسته‌بندی خطایی رخ داد. دوباره تلاش کنید.";
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

    <div class="roma-category-create-page">

        <!-- سربرگ صفحه -->
        <section class="roma-category-create-hero">

            <div class="roma-category-create-hero-content">

                <span class="roma-category-create-badge">
                    مدیریت محتوای روما
                </span>

                <h1>افزودن دسته‌بندی جدید</h1>

                <p>
                    برای مرتب‌سازی مطالب مجله، یک دسته‌بندی جدید ایجاد کنید.
                </p>

            </div>

            <div
                class="roma-category-create-hero-icon"
                aria-hidden="true"
            >
                +
            </div>

        </section>

        <!-- فرم ایجاد دسته‌بندی -->
        <section class="roma-category-create-card">

            <div class="roma-category-create-card-header">

                <div>
                    <h2>اطلاعات دسته‌بندی</h2>

                    <p>
                        عنوانی مناسب برای موضوع موردنظر انتخاب کنید.
                    </p>
                </div>

                <span class="roma-category-create-required">
                    * فیلد الزامی
                </span>

            </div>

            <?php if ($error !== '') { ?>

                <div
                    class="roma-category-create-error"
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
                class="roma-category-create-form"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo htmlspecialchars(
                        $_SESSION['create_category_csrf'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                >

                <div class="roma-category-create-field">

                    <label for="category-title">
                        عنوان دسته‌بندی
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="category-title"
                        name="title"
                        value="<?php
                        echo htmlspecialchars(
                            $title,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                        placeholder="مثلاً: فناوری، آموزش، برنامه‌نویسی..."
                        required
                        autofocus
                    >

                    <small>
                        عنوان باید واضح باشد و موضوع مقالات این دسته را مشخص کند.
                    </small>

                </div>

                <div class="roma-category-create-actions">

                    <button
                        type="submit"
                        name="submit"
                        value="1"
                        class="roma-category-create-submit"
                    >
                        <span aria-hidden="true">✓</span>
                        ذخیره دسته‌بندی
                    </button>

                    <a
                        href="index.php"
                        class="roma-category-create-cancel"
                    >
                        بازگشت به فهرست
                    </a>

                </div>

            </form>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>