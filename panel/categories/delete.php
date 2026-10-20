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

/* فعال‌کردن CSS اختصاصی صفحه */
$panelCategoryDeleteCss = true;

/* دریافت و اعتبارسنجی شناسه */
$rawId = $_GET['id'] ?? $_POST['id'] ?? null;

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

/* دریافت دسته‌بندی */
$category = getCategoryById($id);

if (!$category) {
    header('Location: index.php');
    exit;
}

/* ایجاد توکن امنیتی فرم */
if (
    !isset($_SESSION['delete_category_token'])
    || !is_string($_SESSION['delete_category_token'])
    || $_SESSION['delete_category_token'] === ''
) {
    $_SESSION['delete_category_token'] = bin2hex(
        random_bytes(32)
    );
}

$error = "";

/* حذف فقط با درخواست POST */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['confirm_delete'])
) {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['delete_category_token'] ?? '';

    $rawPostedId = $_POST['id'] ?? null;

    $postedId = (
        is_string($rawPostedId) || is_int($rawPostedId)
    )
        ? filter_var($rawPostedId, FILTER_VALIDATE_INT)
        : false;

    /* اعتبارسنجی توکن */
    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید.";

    /* تطبیق شناسه فرم با شناسه صفحه */
    } elseif (
        $postedId === false
        || $postedId === null
        || (int) $postedId !== $id
    ) {
        $error = "شناسه دسته‌بندی معتبر نیست.";

    } else {
        try {
            /* بررسی مجدد وجود دسته‌بندی */
            $category = getCategoryById($id);

            if (!$category) {
                $error = "دسته‌بندی موردنظر قبلاً حذف شده است.";

            } else {
                /* اجرای حذف */
                deleteCategoryById($id);

                /*
                 * بررسی نتیجه واقعی حذف؛
                 * به مقدار بازگشتی تابع وابسته نیستیم.
                 */
                $remainingCategory = getCategoryById($id);

                if (!$remainingCategory) {
                    unset($_SESSION['delete_category_token']);

                    header('Location: index.php');
                    exit;
                }

                $error = "حذف دسته‌بندی انجام نشد. ممکن است این دسته‌بندی به مقالات موجود متصل باشد.";
            }

        } catch (Throwable $e) {
            error_log(
                "Roma category delete error: " . $e->getMessage()
            );

            $error = "حذف دسته‌بندی انجام نشد. ممکن است این دسته‌بندی به مقالات موجود متصل باشد.";
        }

        /* تازه‌سازی اطلاعات برای نمایش صفحه */
        $category = getCategoryById($id);
    }
}

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content">

    <div class="roma-delete-category-page">

        <section class="roma-delete-category-card">

            <div class="roma-delete-category-icon" aria-hidden="true">
                !
            </div>

            <span class="roma-delete-category-badge">
                مدیریت محتوای روما
            </span>

            <h1>تأیید حذف دسته‌بندی</h1>

            <p class="roma-delete-category-description">
                آیا مطمئن هستید که می‌خواهید این دسته‌بندی را حذف کنید؟
                پیش از ادامه، عنوان آن را بررسی کنید.
            </p>

            <?php if ($category) { ?>

                <div class="roma-delete-category-info">

                    <span class="roma-delete-category-info-label">
                        عنوان دسته‌بندی
                    </span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $category->title ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </strong>

                </div>

            <?php } ?>

            <?php if ($error !== '') { ?>

                <div class="roma-delete-category-error" role="alert">
                    <?php
                    echo htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </div>

            <?php } ?>

            <?php if ($category) { ?>

                <form
                    method="POST"
                    action="delete.php?id=<?php echo $id; ?>"
                    class="roma-delete-category-form"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $id; ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                        echo htmlspecialchars(
                            $_SESSION['delete_category_token'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                    >

                    <button
                        type="submit"
                        name="confirm_delete"
                        value="1"
                        class="roma-delete-category-confirm"
                    >
                        بله، حذف دسته‌بندی
                    </button>

                    <a
                        href="index.php"
                        class="roma-delete-category-cancel"
                    >
                        انصراف و بازگشت
                    </a>

                </form>

                <p class="roma-delete-category-note">
                    اگر این دسته‌بندی به مقالات موجود متصل باشد، ممکن است حذف آن
                    به دلیل محدودیت‌های دیتابیس انجام نشود.
                </p>

            <?php } else { ?>

                <a
                    href="index.php"
                    class="roma-delete-category-cancel"
                >
                    بازگشت به فهرست دسته‌بندی‌ها
                </a>

            <?php } ?>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>