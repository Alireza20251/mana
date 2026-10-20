<?php

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit;
}

/* فایل‌های اصلی پروژه */
require_once "../../functions/helpers.php";
require_once "../../functions/users.php";

/* استایل صفحه */
$panelUserCreateCss = true;

/* مقادیر فرم */
$error = "";
$message = "";
$nameValue = "";
$emailValue = "";

/* ایجاد توکن امنیتی فرم */
if (
    !isset($_SESSION['admin_user_create_csrf'])
    || !is_string($_SESSION['admin_user_create_csrf'])
    || $_SESSION['admin_user_create_csrf'] === ''
) {
    $_SESSION['admin_user_create_csrf'] = bin2hex(
        random_bytes(32)
    );
}

/* پردازش فرم */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['submit'])
) {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['admin_user_create_csrf'] ?? '';

    /* بررسی توکن CSRF */
    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } else {
        $rawName = $_POST['name'] ?? '';
        $rawEmail = $_POST['email'] ?? '';
        $rawPassword = $_POST['password'] ?? '';

        /* جلوگیری از دریافت آرایه به‌جای متن */
        if (
            !is_string($rawName)
            || !is_string($rawEmail)
            || !is_string($rawPassword)
        ) {
            $error = "اطلاعات واردشده معتبر نیست.";

        } else {
            $nameValue = trim($rawName);
            $emailValue = trim($rawEmail);
            $password = $rawPassword;

            if (
                $nameValue === ''
                || $emailValue === ''
                || $password === ''
            ) {
                $error = "لطفاً تمام فیلدهای ضروری را تکمیل کنید.";

            } elseif (
                !filter_var($emailValue, FILTER_VALIDATE_EMAIL)
            ) {
                $error = "لطفاً یک آدرس ایمیل معتبر وارد کنید.";

            } elseif (!checkPassword($password)) {
                $error = "رمز عبور باید حداقل ۶ کاراکتر داشته باشد.";

            } else {
                $image = null;
                $uploadedImage = null;

                try {
                    /* بررسی ایمیل تکراری */
                    if (checkUser($emailValue)) {
                        $error = "این ایمیل قبلاً ثبت شده است.";

                    } else {
                        /*
                         * تصویر ادمین اختیاری است.
                         */
                        if (
                            isset($_FILES['image'])
                            && is_array($_FILES['image'])
                        ) {
                            $imageError = $_FILES['image']['error']
                                ?? UPLOAD_ERR_NO_FILE;

                            if (
                                !is_int($imageError)
                                || $imageError !== UPLOAD_ERR_NO_FILE
                            ) {
                                $uploadedImage = uploadImage(
                                    $_FILES['image'],
                                    'users'
                                );

                                if (
                                    $uploadedImage === false
                                    || !is_string($uploadedImage)
                                    || $uploadedImage === ''
                                ) {
                                    $error = "آپلود تصویر انجام نشد. فرمت تصویر و حجم آن را بررسی کنید.";

                                } else {
                                    $image = $uploadedImage;
                                }
                            }
                        }

                        /*
                         * ثبت فقط در صورت نبود خطا
                         */
                        if ($error === '') {
                            $created = createUser(
                                $nameValue,
                                $emailValue,
                                $password,
                                $image
                            );

                            if ($created === true) {
                                $message = "ادمین با موفقیت ذخیره شد.";

                                $nameValue = "";
                                $emailValue = "";

                                /*
                                 * جلوگیری از استفاده مجدد
                                 * از توکن قبلی فرم
                                 */
                                $_SESSION['admin_user_create_csrf'] =
                                    bin2hex(random_bytes(32));

                            } else {
                                $error = "ثبت ادمین انجام نشد. لطفاً دوباره تلاش کنید.";
                            }
                        }
                    }

                    /*
                     * اگر تصویر آپلود شده ولی ثبت کاربر
                     * موفق نشده است، تصویر اضافه را پاک کن.
                     */
                    if (
                        $error !== ''
                        && is_string($uploadedImage)
                        && $uploadedImage !== ''
                    ) {
                        $imagePath = __DIR__
                            . '/../../images/users/'
                            . $uploadedImage;

                        if (is_file($imagePath)) {
                            @unlink($imagePath);
                        }
                    }

                } catch (Throwable $e) {
                    error_log(
                        "Roma admin creation error: " . $e->getMessage()
                    );

                    $error = "هنگام ثبت ادمین خطایی رخ داد. لطفاً دوباره تلاش کنید.";

                    /* پاک‌کردن تصویر در صورت شکست ثبت */
                    if (
                        is_string($uploadedImage)
                        && $uploadedImage !== ''
                    ) {
                        $imagePath = __DIR__
                            . '/../../images/users/'
                            . $uploadedImage;

                        if (is_file($imagePath)) {
                            @unlink($imagePath);
                        }
                    }
                }
            }
        }
    }
}

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content roma-admin-create">

    <div class="r-admin-create-container">

        <div class="r-admin-create-heading">

            <div class="r-admin-create-heading-icon">
                <span>+</span>
            </div>

            <div class="r-admin-create-heading-text">
                <h1>افزودن ادمین</h1>
                <p>اطلاعات مدیر جدید را وارد کنید.</p>
            </div>

            <a href="index.php" class="r-admin-back-link">
                <span aria-hidden="true">←</span>
                بازگشت به فهرست ادمین‌ها
            </a>

        </div>

        <section class="r-admin-create-card">

            <div class="r-admin-create-card-header">

                <div>
                    <h2>اطلاعات مدیر</h2>
                    <p>فیلدهای ستاره‌دار ضروری هستند.</p>
                </div>

                <span class="r-admin-create-badge">
                    مدیریت کاربران
                </span>

            </div>

            <div class="r-admin-create-card-body">

                <?php if ($error !== '') { ?>

                    <div
                        class="r-admin-alert r-admin-alert-error"
                        role="alert"
                    >
                        <span class="r-admin-alert-icon">!</span>

                        <span>
                            <?php
                            echo htmlspecialchars(
                                $error,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </span>
                    </div>

                <?php } ?>

                <?php if ($message !== '') { ?>

                    <div
                        class="r-admin-alert r-admin-alert-success"
                        role="status"
                    >
                        <span class="r-admin-alert-icon">✓</span>

                        <span>
                            <?php
                            echo htmlspecialchars(
                                $message,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </span>
                    </div>

                <?php } ?>

                <form
                    method="POST"
                    action="create.php"
                    enctype="multipart/form-data"
                    class="r-admin-create-form"
                >

                    <!-- توکن امنیتی CSRF -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                        echo htmlspecialchars(
                            $_SESSION['admin_user_create_csrf'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                    >

                    <div class="r-admin-form-grid">

                        <div class="r-admin-form-group">

                            <label for="admin-name">
                                نام و نام خانوادگی
                                <span class="r-required">*</span>
                            </label>

                            <input
                                type="text"
                                id="admin-name"
                                name="name"
                                class="r-admin-input"
                                placeholder="نام کامل مدیر را وارد کنید"
                                value="<?php
                                echo htmlspecialchars(
                                    $nameValue,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                autocomplete="name"
                                required
                            >

                        </div>

                        <div class="r-admin-form-group">

                            <label for="admin-email">
                                ایمیل
                                <span class="r-required">*</span>
                            </label>

                            <input
                                type="email"
                                id="admin-email"
                                name="email"
                                class="r-admin-input"
                                placeholder="example@email.com"
                                value="<?php
                                echo htmlspecialchars(
                                    $emailValue,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                dir="ltr"
                                autocomplete="email"
                                required
                            >

                        </div>

                        <div class="r-admin-form-group">

                            <label for="admin-password">
                                رمز عبور
                                <span class="r-required">*</span>
                            </label>

                            <input
                                type="password"
                                id="admin-password"
                                name="password"
                                class="r-admin-input"
                                placeholder="رمز عبور مدیر جدید"
                                autocomplete="new-password"
                                required
                            >

                            <small class="r-admin-field-hint">
                                رمز عبور باید حداقل ۶ کاراکتر داشته باشد.
                            </small>

                        </div>

                        <div class="r-admin-form-group">

                            <label for="admin-image">
                                تصویر مدیر
                                <span class="r-admin-optional">
                                    (اختیاری)
                                </span>
                            </label>

                            <div class="r-admin-file-box">

                                <input
                                    type="file"
                                    id="admin-image"
                                    name="image"
                                    accept="image/*"
                                    class="r-admin-file-input"
                                >

                                <label
                                    for="admin-image"
                                    class="r-admin-file-label"
                                >
                                    <span class="r-admin-file-icon">↑</span>

                                    <span>
                                        <strong>انتخاب تصویر</strong>
                                        <small>
                                            برای افزودن عکس مدیر کلیک کنید.
                                        </small>
                                    </span>
                                </label>

                            </div>

                            <small class="r-admin-field-hint">
                                حجم تصویر حداکثر ۵ مگابایت باشد.
                            </small>

                        </div>

                    </div>

                    <div class="r-admin-form-footer">

                        <p>
                            <span class="r-required">*</span>
                            تکمیل این فیلدها الزامی است.
                        </p>

                        <div class="r-admin-form-actions">

                            <a
                                href="index.php"
                                class="r-admin-cancel-btn"
                            >
                                انصراف
                            </a>

                            <button
                                type="submit"
                                name="submit"
                                value="1"
                                class="r-admin-submit-btn"
                            >
                                <span aria-hidden="true">✓</span>
                                ذخیره ادمین
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>