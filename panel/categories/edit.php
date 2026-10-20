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
$panelCategoryEditCss = true;

/* دریافت و بررسی شناسه دسته‌بندی */
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

/* دریافت دسته‌بندی */
$category = getCategoryById($id);

if (!$category) {
    header('Location: index.php');
    exit;
}

/* مقدار اولیه فرم */
$title = $category->title ?? '';
$error = '';

/* ایجاد توکن امنیتی فرم */
if (
    !isset($_SESSION['edit_category_csrf'])
    || !is_string($_SESSION['edit_category_csrf'])
    || $_SESSION['edit_category_csrf'] === ''
) {
    $_SESSION['edit_category_csrf'] = bin2hex(
        random_bytes(32)
    );
}

/* ذخیره تغییرات */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['submit'])
) {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['edit_category_csrf'] ?? '';

    /* بررسی توکن CSRF */
    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        $error = 'درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.';

    } else {
        $rawTitle = $_POST['title'] ?? '';

        if (!is_string($rawTitle)) {
            $error = 'عنوان دسته‌بندی معتبر نیست.';

        } else {
            $title = trim($rawTitle);

            if ($title === '') {
                $error = 'لطفاً عنوان دسته‌بندی را وارد کنید.';

            } else {
                try {
                    /* بررسی مجدد وجود دسته‌بندی */
                    $category = getCategoryById($id);

                    if (!$category) {
                        $error = 'دسته‌بندی موردنظر پیدا نشد.';

                    } else {
                        $updated = updateCategory($id, $title);

                        if ($updated === true) {
                            unset($_SESSION['edit_category_csrf']);

                            header('Location: index.php');
                            exit;
                        }

                        $error = 'ذخیره تغییرات انجام نشد. دوباره تلاش کنید.';
                    }

                } catch (Throwable $e) {
                    error_log(
                        "Roma category edit error: " . $e->getMessage()
                    );

                    $error = 'هنگام ذخیره تغییرات خطایی رخ داد. دوباره تلاش کنید.';
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

    <div class="roma-category-edit-page">

        <!-- سربرگ صفحه -->
        <section class="roma-category-edit-hero">

            <div class="roma-category-edit-hero-content">

                <span class="roma-category-edit-badge">
                    مدیریت محتوای روما
                </span>

                <h1>ویرایش دسته‌بندی</h1>

                <p>
                    عنوان دسته‌بندی را اصلاح کنید تا موضوع مقالات
                    روما به‌درستی نمایش داده شود.
                </p>

            </div>

            <div
                class="roma-category-edit-hero-icon"
                aria-hidden="true"
            >
                ✎
            </div>

        </section>

        <!-- فرم ویرایش -->
        <section class="roma-category-edit-card">

            <div class="roma-category-edit-card-header">

                <div>
                    <h2>اطلاعات دسته‌بندی</h2>

                    <p>
                        عنوان جدید را وارد کنید و تغییرات را ذخیره کنید.
                    </p>
                </div>

                <span class="roma-category-edit-required">
                    * فیلد الزامی
                </span>

            </div>

            <?php if ($error !== '') { ?>

                <div
                    class="roma-category-edit-error"
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
                action="edit.php?id=<?php echo $id; ?>"
                class="roma-category-edit-form"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo htmlspecialchars(
                        $_SESSION['edit_category_csrf'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                >

                <div class="roma-category-edit-field">

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
                        placeholder="عنوان دسته‌بندی را وارد کنید..."
                        required
                        autofocus
                    >

                    <small>
                        عنوان باید واضح و مرتبط با موضوع مقالات این دسته باشد.
                    </small>

                </div>

                <div class="roma-category-edit-actions">

                    <button
                        type="submit"
                        name="submit"
                        value="1"
                        class="roma-category-edit-submit"
                    >
                        <span aria-hidden="true">✓</span>
                        ذخیره تغییرات
                    </button>

                    <a
                        href="index.php"
                        class="roma-category-edit-cancel"
                    >
                        بازگشت به فهرست
                    </a>

                </div>

            </form>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>