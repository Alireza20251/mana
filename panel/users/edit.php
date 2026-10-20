<?php

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit;
}

/* فایل‌های اصلی پروژه */
require_once "../../functions/helpers.php";
require_once "../../functions/users.php";

/* بررسی شناسه ادمین */
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

/* دریافت اطلاعات ادمین */
$user = getUserById($id);

if (!$user) {
    header('Location: index.php');
    exit;
}

/* فعال‌کردن CSS اختصاصی صفحه */
$panelUserEditCss = true;

/* مقادیر اولیه فرم */
$error = "";
$nameValue = $user->name;

/* ایجاد توکن امنیتی فرم */
if (
    !isset($_SESSION['admin_user_edit_csrf'])
    || !is_string($_SESSION['admin_user_edit_csrf'])
    || $_SESSION['admin_user_edit_csrf'] === ''
) {
    $_SESSION['admin_user_edit_csrf'] = bin2hex(
        random_bytes(32)
    );
}

/* ثبت تغییرات */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['submit'])
) {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['admin_user_edit_csrf'] ?? '';

    /* اعتبارسنجی توکن CSRF */
    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } else {
        $rawName = $_POST['name'] ?? '';
        $rawPassword = $_POST['password'] ?? '';

        /* جلوگیری از دریافت آرایه به‌جای متن */
        if (
            !is_string($rawName)
            || !is_string($rawPassword)
        ) {
            $error = "اطلاعات واردشده معتبر نیست.";

        } else {
            $nameValue = trim($rawName);
            $password = $rawPassword;

            if ($nameValue === '') {
                $error = "لطفاً نام و نام خانوادگی را وارد کنید.";

            } elseif (
                $password !== ''
                && !checkPassword($password)
            ) {
                $error = "رمز عبور باید حداقل ۶ کاراکتر داشته باشد.";

            } else {
                /* دریافت تصویر ارسالی */
                $image = null;

                if (isset($_FILES['image'])) {
                    if (!is_array($_FILES['image'])) {
                        $error = "اطلاعات تصویر معتبر نیست.";
                    } else {
                        $image = $_FILES['image'];
                    }
                }

                if ($error === '') {
                    try {
                        /*
                         * انتخاب تصویر جدید اختیاری است.
                         * تابع updateUser تصویر قبلی را حفظ می‌کند.
                         */
                        $updated = updateUser(
                            $id,
                            $nameValue,
                            $password,
                            $image
                        );

                        if ($updated === true) {
                            unset($_SESSION['admin_user_edit_csrf']);

                            header('Location: index.php');
                            exit;

                        } else {
                            $error = "تغییرات ذخیره نشد. اگر تصویر جدید انتخاب کرده‌اید، فرمت و حجم آن را بررسی کنید.";
                        }

                    } catch (Throwable $e) {
                        error_log(
                            "Roma admin update error: " . $e->getMessage()
                        );

                        $error = "هنگام ذخیره تغییرات خطایی رخ داد. لطفاً دوباره تلاش کنید.";
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

<main class="main-content roma-admin-edit">

    <div class="r-user-edit-container">

        <!-- عنوان صفحه -->
        <div class="r-user-edit-heading">

            <div class="r-user-edit-heading-icon">
                <span>✎</span>
            </div>

            <div class="r-user-edit-heading-text">
                <h1>ویرایش ادمین</h1>
                <p>اطلاعات حساب مدیر را مشاهده و ویرایش کنید.</p>
            </div>

            <a href="index.php" class="r-user-edit-back">
                <span aria-hidden="true">←</span>
                بازگشت به فهرست ادمین‌ها
            </a>

        </div>

        <!-- کارت ویرایش -->
        <section class="r-user-edit-card">

            <div class="r-user-edit-card-header">

                <div>
                    <h2>اطلاعات حساب کاربری</h2>
                    <p>تغییرات موردنظر را وارد و ذخیره کنید.</p>
                </div>

                <span class="r-user-edit-badge">
                    ویرایش مدیر
                </span>

            </div>

            <div class="r-user-edit-card-body">

                <?php if ($error !== '') { ?>

                    <div
                        class="r-user-edit-alert r-user-edit-alert-error"
                        role="alert"
                    >

                        <span class="r-user-edit-alert-icon">!</span>

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

                <!-- اطلاعات مدیر -->
                <div class="r-user-profile">

                    <div class="r-user-profile-avatar">

                        <?php if (!empty($user->image)) { ?>

                            <img
                                src="<?php
                                echo asset('images/users/') .
                                    htmlspecialchars(
                                        $user->image,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>"
                                alt="تصویر مدیر"
                            >

                        <?php } else { ?>

                            <span>
                                <?php
                                echo htmlspecialchars(
                                    mb_substr(
                                        $user->name,
                                        0,
                                        1,
                                        'UTF-8'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </span>

                        <?php } ?>

                    </div>

                    <div class="r-user-profile-info">

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $user->name,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </strong>

                        <span>حساب مدیر روما</span>

                    </div>

                </div>

                <form
                    method="POST"
                    action="edit.php?id=<?php echo $id; ?>"
                    enctype="multipart/form-data"
                    class="r-user-edit-form"
                >

                    <!-- توکن امنیتی -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                        echo htmlspecialchars(
                            $_SESSION['admin_user_edit_csrf'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                    >

                    <div class="r-user-edit-grid">

                        <!-- نام -->
                        <div class="r-user-edit-field">

                            <label for="user-name">
                                نام و نام خانوادگی
                                <span class="r-user-required">*</span>
                            </label>

                            <input
                                type="text"
                                id="user-name"
                                name="name"
                                class="r-user-edit-input"
                                placeholder="نام کامل مدیر"
                                value="<?php
                                echo htmlspecialchars(
                                    $nameValue,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                maxlength="255"
                                autocomplete="name"
                                required
                            >

                        </div>

                        <!-- ایمیل -->
                        <div class="r-user-edit-field">

                            <label for="user-email">
                                ایمیل
                            </label>

                            <input
                                type="email"
                                id="user-email"
                                class="r-user-edit-input r-user-email-disabled"
                                value="<?php
                                echo htmlspecialchars(
                                    $user->email,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>"
                                dir="ltr"
                                disabled
                            >

                            <small class="r-user-edit-hint">
                                ایمیل این حساب از این صفحه قابل تغییر نیست.
                            </small>

                        </div>

                        <!-- رمز عبور -->
                        <div class="r-user-edit-field">

                            <label for="user-password">
                                رمز عبور جدید
                            </label>

                            <input
                                type="password"
                                id="user-password"
                                name="password"
                                class="r-user-edit-input"
                                placeholder="در صورت نیاز وارد کنید"
                                autocomplete="new-password"
                            >

                            <small class="r-user-edit-hint">
                                برای حفظ رمز فعلی، این قسمت را خالی بگذارید.
                                رمز جدید باید حداقل ۶ کاراکتر باشد.
                            </small>

                        </div>

                        <!-- تصویر جدید -->
                        <div class="r-user-edit-field">

                            <label for="user-image">
                                تغییر تصویر مدیر
                                <span class="r-user-edit-optional">
                                    (اختیاری)
                                </span>
                            </label>

                            <div class="r-user-edit-file-box">

                                <input
                                    type="file"
                                    id="user-image"
                                    name="image"
                                    class="r-user-edit-file-input"
                                    accept="image/*"
                                >

                                <label
                                    for="user-image"
                                    class="r-user-edit-file-label"
                                >

                                    <span class="r-user-edit-file-icon">
                                        ↑
                                    </span>

                                    <span>
                                        <strong>انتخاب تصویر جدید</strong>

                                        <small>
                                            اگر تصویری انتخاب نکنید،
                                            عکس فعلی حفظ می‌شود.
                                        </small>
                                    </span>

                                </label>

                            </div>

                            <small class="r-user-edit-hint">
                                فرمت تصویر باید مجاز باشد و حجم آن از ۵ مگابایت بیشتر نباشد.
                            </small>

                        </div>

                    </div>

                    <!-- دکمه‌ها -->
                    <div class="r-user-edit-footer">

                        <p>
                            <span class="r-user-required">*</span>
                            تکمیل نام مدیر الزامی است.
                        </p>

                        <div class="r-user-edit-actions">

                            <a
                                href="index.php"
                                class="r-user-edit-cancel"
                            >
                                انصراف
                            </a>

                            <button
                                type="submit"
                                name="submit"
                                value="1"
                                class="r-user-edit-submit"
                            >
                                <span aria-hidden="true">✓</span>
                                ذخیره تغییرات
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>