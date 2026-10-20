<?php

session_start();

/*
|--------------------------------------------------------------------------
| بررسی ورود مدیر
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| فایل‌های اصلی پروژه
|--------------------------------------------------------------------------
*/

require_once "../../functions/helpers.php";
require_once "../../functions/articles.php";

/* فعال‌کردن CSS اختصاصی صفحه */
$panelArticleDeleteCss = true;

/*
|--------------------------------------------------------------------------
| دریافت و اعتبارسنجی شناسه مقاله
|--------------------------------------------------------------------------
*/

$rawId = $_GET['id'] ?? $_POST['id'] ?? null;

if (
    (!is_string($rawId) && !is_int($rawId))
    || filter_var($rawId, FILTER_VALIDATE_INT) === false
    || (int) $rawId < 1
) {
    header('Location: index.php');
    exit;
}

$id = (int) $rawId;

/*
|--------------------------------------------------------------------------
| دریافت مقاله
|--------------------------------------------------------------------------
*/

$article = getArticleById($id);

if (!$article) {
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| ایجاد توکن امنیتی فرم
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['delete_article_token'])
    || !is_string($_SESSION['delete_article_token'])
    || $_SESSION['delete_article_token'] === ''
) {
    $_SESSION['delete_article_token'] = bin2hex(
        random_bytes(32)
    );
}

$error = "";

/*
|--------------------------------------------------------------------------
| حذف مقاله فقط پس از تأیید معتبر
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['confirm_delete'])
) {

    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['delete_article_token'] ?? '';

    $postedId = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    /*
     * بررسی شناسه و توکن امنیتی
     */
    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || !hash_equals($sessionToken, $submittedToken)
    ) {

        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید.";

    } elseif (
        $postedId === false
        || $postedId === null
        || (int) $postedId !== $id
    ) {

        $error = "شناسه مقاله معتبر نیست. دوباره تلاش کنید.";

    } else {

        try {

            /*
             * اجرای حذف مقاله
             */
            deleteArticleById($id);

            /*
             * تابع فعلی deleteArticleById نتیجه‌ای برنمی‌گرداند؛
             * بنابراین وجود مقاله را پس از حذف دوباره بررسی می‌کنیم.
             */
            $remainingArticle = getArticleById($id);

            if (!$remainingArticle) {

                unset($_SESSION['delete_article_token']);

                header('Location: index.php');
                exit;

            } else {

                $error = "حذف مقاله انجام نشد. لطفاً دوباره تلاش کنید.";
            }

        } catch (Throwable $e) {

            error_log(
                "Roma article delete error: " . $e->getMessage()
            );

            $error = "حذف مقاله انجام نشد. لطفاً دوباره تلاش کنید.";
        }
    }
}

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content">

    <div class="roma-delete-article-page">

        <section class="roma-delete-article-card">

            <div class="roma-delete-icon" aria-hidden="true">
                !
            </div>

            <span class="roma-delete-badge">
                مدیریت محتوای روما
            </span>

            <h1>تأیید حذف مقاله</h1>

            <p class="roma-delete-description">
                آیا مطمئن هستید که می‌خواهید این مقاله را حذف کنید؟
                پیش از ادامه، عنوان مقاله را بررسی کنید.
            </p>

            <div class="roma-delete-article-info">

                <span class="roma-delete-info-label">
                    عنوان مقاله
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $article->title ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </strong>

            </div>

            <?php if ($error !== '') { ?>

                <div class="roma-delete-error" role="alert">
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
                action="delete.php?id=<?php echo (int) $id; ?>"
                class="roma-delete-form"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo (int) $id; ?>"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo htmlspecialchars(
                        $_SESSION['delete_article_token'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                >

                <button
                    type="submit"
                    name="confirm_delete"
                    value="1"
                    class="roma-delete-confirm-button"
                >
                    بله، حذف مقاله
                </button>

                <a
                    href="index.php"
                    class="roma-delete-cancel-button"
                >
                    انصراف و بازگشت
                </a>

            </form>

            <p class="roma-delete-warning">
                با تأیید این عملیات، مقاله از پایگاه داده حذف می‌شود.
            </p>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>