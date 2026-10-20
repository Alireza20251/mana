<?php

require_once "../../functions/helpers.php";
require_once "../../functions/users.php";

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
| ایجاد توکن امنیتی فرم
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['admin_user_delete_csrf'])
    || !is_string($_SESSION['admin_user_delete_csrf'])
    || $_SESSION['admin_user_delete_csrf'] === ''
) {
    $_SESSION['admin_user_delete_csrf'] = bin2hex(
        random_bytes(32)
    );
}

$error = "";
$user = null;
$id = 0;

/*
|--------------------------------------------------------------------------
| دریافت و اعتبارسنجی شناسه ادمین
|--------------------------------------------------------------------------
*/

$rawId = $_GET['id'] ?? null;

if (
    (is_string($rawId) || is_int($rawId))
    && filter_var($rawId, FILTER_VALIDATE_INT) !== false
    && (int) $rawId > 0
) {
    $id = (int) $rawId;
}

if ($id < 1) {

    $error = "شناسه ادمین معتبر نیست.";

} else {

    $user = getUserById($id);

    if (!$user) {
        $error = "ادمین موردنظر پیدا نشد یا قبلاً حذف شده است.";
    }
}

/*
|--------------------------------------------------------------------------
| حذف فقط با درخواست POST و توکن معتبر
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['confirm_delete'])
) {

    $postedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['admin_user_delete_csrf'] ?? '';

    $rawPostedId = $_POST['id'] ?? null;

    $postedId = (
        is_string($rawPostedId) || is_int($rawPostedId)
    )
        ? filter_var($rawPostedId, FILTER_VALIDATE_INT)
        : false;

    /*
     * بررسی توکن امنیتی
     */
    if (
        !is_string($postedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $postedToken)
    ) {

        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } elseif (
        $postedId === false
        || $postedId === null
        || (int) $postedId !== $id
        || $id < 1
    ) {

        $error = "شناسه ادمین معتبر نیست.";

    } else {

        /*
         * بررسی مجدد وجود ادمین قبل از حذف
         */
        $user = getUserById($id);

        if (!$user) {

            $error = "ادمین موردنظر پیدا نشد یا قبلاً حذف شده است.";

        } else {

            try {

                /*
                 * بررسی حساب مدیر فعلی
                 */
                $currentAdmin = checkUser($_SESSION['user']);

                if (!$currentAdmin) {

                    $error = "حساب مدیر فعلی معتبر نیست. دوباره وارد پنل شوید.";

                } elseif ((int) $currentAdmin->id === $id) {

                    $error = "برای جلوگیری از قفل شدن پنل، نمی‌توانید حساب مدیر فعلی را حذف کنید.";

                } else {

                    /*
                     * اجرای حذف و بررسی نتیجه
                     */
                    $deleted = deleteUserById($id);

                    if ($deleted === true) {

                        /*
                         * اطمینان از حذف شدن رکورد
                         */
                        $remainingUser = getUserById($id);

                        if (!$remainingUser) {

                            unset(
                                $_SESSION['admin_user_delete_csrf']
                            );

                            header('Location: index.php');
                            exit;

                        } else {

                            $error = "حذف ادمین تأیید نشد. لطفاً دوباره تلاش کنید.";
                        }

                    } else {

                        $error = "حذف ادمین انجام نشد. لطفاً دوباره تلاش کنید.";
                    }
                }

            } catch (Throwable $e) {

                error_log(
                    "Roma admin delete error: " . $e->getMessage()
                );

                $error = "هنگام حذف ادمین خطایی رخ داد. لطفاً دوباره تلاش کنید.";
            }
        }
    }

    /*
     * در صورت شکست، اطلاعات ادمین دوباره دریافت می‌شود
     * تا صفحه تأیید بتواند مشخصات موجود را نشان دهد.
     */
    if ($id > 0) {
        $user = getUserById($id);
    }
}

$panelUserDeleteCss = true;

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content roma-admin-delete">

    <div class="r-user-delete-container">

        <!-- عنوان صفحه -->
        <div class="r-user-delete-heading">

            <div class="r-user-delete-heading-icon">
                <span>!</span>
            </div>

            <div class="r-user-delete-heading-text">
                <h1>حذف ادمین</h1>
                <p>پیش از حذف، مشخصات مدیر را بررسی کنید.</p>
            </div>

            <a href="index.php" class="r-user-delete-back">
                <span aria-hidden="true">←</span>
                بازگشت به فهرست ادمین‌ها
            </a>

        </div>

        <section class="r-user-delete-card">

            <div class="r-user-delete-card-header">
                <h2>تأیید حذف حساب</h2>
                <span class="r-user-delete-badge">مدیریت کاربران</span>
            </div>

            <div class="r-user-delete-card-body">

                <?php if ($error !== "") { ?>

                    <div
                        class="r-user-delete-alert r-user-delete-alert-error"
                        role="alert"
                    >

                        <span class="r-user-delete-alert-icon">!</span>

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

                <?php if ($user) { ?>

                    <div class="r-user-delete-warning">

                        <div class="r-user-delete-warning-icon">
                            !
                        </div>

                        <div>

                            <strong>
                                آیا از حذف این ادمین مطمئن هستید؟
                            </strong>

                            <p>
                                این عملیات ممکن است قابل بازگشت نباشد.
                                پیش از ادامه، مشخصات زیر را بررسی کنید.
                            </p>

                        </div>

                    </div>

                    <div class="r-user-delete-profile">

                        <div class="r-user-delete-avatar">

                            <?php if (!empty($user->image)) { ?>

                                <img
                                    src="<?php
                                    echo asset('images/') .
                                        htmlspecialchars(
                                            $user->image,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                    ?>"
                                    alt="تصویر ادمین"
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

                        <div class="r-user-delete-details">

                            <span class="r-user-delete-label">
                                نام و نام خانوادگی
                            </span>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $user->name,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </strong>

                            <span class="r-user-delete-label">
                                ایمیل
                            </span>

                            <span
                                class="r-user-delete-email"
                                dir="ltr"
                            >
                                <?php
                                echo htmlspecialchars(
                                    $user->email,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </span>

                        </div>

                    </div>

                    <form
                        method="POST"
                        action="delete.php?id=<?php echo (int) $user->id; ?>"
                        class="r-user-delete-form"
                    >

                        <input
                            type="hidden"
                            name="id"
                            value="<?php echo (int) $user->id; ?>"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php
                            echo htmlspecialchars(
                                $_SESSION['admin_user_delete_csrf'],
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>"
                        >

                        <div class="r-user-delete-actions">

                            <a
                                href="index.php"
                                class="r-user-delete-cancel"
                            >
                                انصراف
                            </a>

                            <button
                                type="submit"
                                name="confirm_delete"
                                value="1"
                                class="r-user-delete-submit"
                            >
                                <span aria-hidden="true">×</span>
                                تأیید و حذف ادمین
                            </button>

                        </div>

                    </form>

                <?php } else { ?>

                    <div class="r-user-delete-empty">

                        <p>
                            برای ادامه، به فهرست ادمین‌ها برگردید.
                        </p>

                        <a
                            href="index.php"
                            class="r-user-delete-cancel"
                        >
                            بازگشت به فهرست
                        </a>

                    </div>

                <?php } ?>

            </div>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>