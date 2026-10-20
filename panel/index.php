<?php

session_start();

/*
|--------------------------------------------------------------------------
| بررسی نشست مدیر
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['user'])
    || !is_string($_SESSION['user'])
    || trim($_SESSION['user']) === ''
) {
    header('Location: ../auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| فایل‌های اصلی پروژه
|--------------------------------------------------------------------------
*/

require_once "../functions/helpers.php";
require_once "../functions/DB.php";
require_once "../functions/users.php";

/*
|--------------------------------------------------------------------------
| بررسی اعتبار حساب مدیر در دیتابیس
|--------------------------------------------------------------------------
*/

$currentAdmin = checkUser($_SESSION['user']);

if (!$currentAdmin) {

    /* پاک‌کردن اطلاعات نشست */
    $_SESSION = [];

    /* حذف کوکی نشست */
    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    /* پایان نشست نامعتبر */
    session_destroy();

    header('Location: ../auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| فعال‌کردن CSS داشبورد
|--------------------------------------------------------------------------
*/

$dashboardPageCss = true;

/*
|--------------------------------------------------------------------------
| آمار داشبورد روما
|--------------------------------------------------------------------------
*/

$stats = [
    'articles'  => null,
    'published' => null,
    'pending'   => null,
    'messages'  => null
];

$queries = [
    'articles' => "
        SELECT COUNT(*)
        FROM articles
    ",

    'published' => "
        SELECT COUNT(*)
        FROM articles
        WHERE status = 'published'
    ",

    'pending' => "
        SELECT COUNT(*)
        FROM articles
        WHERE visitor_id IS NOT NULL
        AND status = 'draft'
    ",

    'messages' => "
        SELECT COUNT(*)
        FROM messages
        WHERE sender = 'visitor'
        AND is_read = 0
    "
];

/*
|--------------------------------------------------------------------------
| اجرای کوئری‌های آمار
|--------------------------------------------------------------------------
*/

foreach ($queries as $key => $sql) {

    try {

        $stats[$key] = (int) $conn
            ->query($sql)
            ->fetchColumn();

    } catch (Throwable $e) {

        error_log(
            "Roma dashboard stats error: " . $e->getMessage()
        );
    }
}

/*
|--------------------------------------------------------------------------
| نمایش امن آمار
|--------------------------------------------------------------------------
*/

function romaDashboardNumber($value)
{
    if ($value === null) {
        return '—';
    }

    return number_format($value);
}

?>

<?php include "layouts/head.php"; ?>
<?php include "layouts/navigation.php"; ?>
<?php include "layouts/header.php"; ?>

<main class="main-content">

    <div class="roma-admin-dashboard">

        <!-- بخش خوشامدگویی -->
        <section class="roma-dashboard-welcome">

            <div class="roma-dashboard-welcome-content">

                <span class="roma-dashboard-label">
                    مرکز مدیریت مجله روما
                </span>

                <h1>خوش آمدید 👋</h1>

                <p>
                    از این بخش می‌توانید مقالات و دسته‌بندی‌ها را مدیریت کنید،
                    مطالب ارسالی بازدیدکنندگان را بررسی کنید و به پیام‌های
                    کاربران پاسخ دهید.
                </p>

            </div>

            <div class="roma-dashboard-logo" aria-hidden="true">
                ر
            </div>

        </section>


        <!-- کارت‌های آمار -->
        <section class="roma-dashboard-stats-section">

            <h2 class="roma-dashboard-heading">
                وضعیت روما
            </h2>

            <p class="roma-dashboard-subheading">
                خلاصه‌ای از وضعیت محتوا و پیام‌های سایت
            </p>

            <div class="roma-dashboard-stats">

                <!-- کل مقالات -->
                <article class="roma-dashboard-stat">

                    <div class="roma-dashboard-stat-top">
                        <div
                            class="roma-dashboard-stat-icon roma-stat-teal"
                            aria-hidden="true"
                        >
                            ✎
                        </div>
                    </div>

                    <div class="roma-dashboard-stat-number">
                        <?php
                        echo romaDashboardNumber($stats['articles']);
                        ?>
                    </div>

                    <h3 class="roma-dashboard-stat-title">
                        کل مقالات
                    </h3>

                    <p class="roma-dashboard-stat-description">
                        تمام مقالات ثبت‌شده در روما
                    </p>

                </article>


                <!-- مقالات منتشرشده -->
                <article class="roma-dashboard-stat">

                    <div class="roma-dashboard-stat-top">
                        <div
                            class="roma-dashboard-stat-icon roma-stat-blue"
                            aria-hidden="true"
                        >
                            ✓
                        </div>
                    </div>

                    <div class="roma-dashboard-stat-number">
                        <?php
                        echo romaDashboardNumber($stats['published']);
                        ?>
                    </div>

                    <h3 class="roma-dashboard-stat-title">
                        مقالات منتشرشده
                    </h3>

                    <p class="roma-dashboard-stat-description">
                        مطالب منتشرشده برای بازدیدکنندگان
                    </p>

                </article>


                <!-- مقالات در انتظار بررسی -->
                <article class="roma-dashboard-stat">

                    <div class="roma-dashboard-stat-top">
                        <div
                            class="roma-dashboard-stat-icon roma-stat-orange"
                            aria-hidden="true"
                        >
                            ◷
                        </div>
                    </div>

                    <div class="roma-dashboard-stat-number">
                        <?php
                        echo romaDashboardNumber($stats['pending']);
                        ?>
                    </div>

                    <h3 class="roma-dashboard-stat-title">
                        مقالات در انتظار بررسی
                    </h3>

                    <p class="roma-dashboard-stat-description">
                        مقالات بازدیدکنندگان با وضعیت پیش‌نویس
                    </p>

                </article>


                <!-- پیام‌های خوانده‌نشده -->
                <article class="roma-dashboard-stat">

                    <div class="roma-dashboard-stat-top">
                        <div
                            class="roma-dashboard-stat-icon roma-stat-purple"
                            aria-hidden="true"
                        >
                            ✉
                        </div>
                    </div>

                    <div class="roma-dashboard-stat-number">
                        <?php
                        echo romaDashboardNumber($stats['messages']);
                        ?>
                    </div>

                    <h3 class="roma-dashboard-stat-title">
                        پیام‌های خوانده‌نشده
                    </h3>

                    <p class="roma-dashboard-stat-description">
                        پیام‌های جدید بازدیدکنندگان
                    </p>

                </article>

            </div>

        </section>


        <!-- میانبرهای مدیریت -->
        <section class="roma-dashboard-shortcuts-section">

            <h2 class="roma-dashboard-heading">
                دسترسی سریع
            </h2>

            <p class="roma-dashboard-subheading">
                بخش موردنظر خود را انتخاب کنید
            </p>

            <div class="roma-dashboard-shortcuts">

                <!-- مدیریت مقالات -->
                <a
                    href="articles/index.php"
                    class="roma-dashboard-shortcut"
                >

                    <div
                        class="roma-dashboard-shortcut-icon"
                        aria-hidden="true"
                    >
                        ✎
                    </div>

                    <div class="roma-dashboard-shortcut-content">
                        <h3>مدیریت مقالات</h3>
                        <p>مشاهده، ویرایش و انتشار مطالب</p>
                    </div>

                    <span
                        class="roma-dashboard-shortcut-arrow"
                        aria-hidden="true"
                    >←</span>

                </a>


                <!-- افزودن مقاله -->
                <a
                    href="articles/create.php"
                    class="roma-dashboard-shortcut"
                >

                    <div
                        class="roma-dashboard-shortcut-icon"
                        aria-hidden="true"
                    >
                        +
                    </div>

                    <div class="roma-dashboard-shortcut-content">
                        <h3>افزودن مقاله</h3>
                        <p>ثبت مطلب جدید در مجله</p>
                    </div>

                    <span
                        class="roma-dashboard-shortcut-arrow"
                        aria-hidden="true"
                    >←</span>

                </a>


                <!-- مدیریت دسته‌بندی‌ها -->
                <a
                    href="categories/index.php"
                    class="roma-dashboard-shortcut"
                >

                    <div
                        class="roma-dashboard-shortcut-icon"
                        aria-hidden="true"
                    >
                        ▤
                    </div>

                    <div class="roma-dashboard-shortcut-content">
                        <h3>مدیریت دسته‌بندی‌ها</h3>
                        <p>مشاهده و ویرایش دسته‌بندی مطالب</p>
                    </div>

                    <span
                        class="roma-dashboard-shortcut-arrow"
                        aria-hidden="true"
                    >←</span>

                </a>


                <!-- افزودن دسته‌بندی -->
                <a
                    href="categories/create.php"
                    class="roma-dashboard-shortcut"
                >

                    <div
                        class="roma-dashboard-shortcut-icon"
                        aria-hidden="true"
                    >
                        ⊞
                    </div>

                    <div class="roma-dashboard-shortcut-content">
                        <h3>افزودن دسته‌بندی</h3>
                        <p>ایجاد دسته‌بندی جدید</p>
                    </div>

                    <span
                        class="roma-dashboard-shortcut-arrow"
                        aria-hidden="true"
                    >←</span>

                </a>


                <!-- مدیریت ادمین‌ها -->
                <a
                    href="users/index.php"
                    class="roma-dashboard-shortcut"
                >

                    <div
                        class="roma-dashboard-shortcut-icon"
                        aria-hidden="true"
                    >
                        ♙
                    </div>

                    <div class="roma-dashboard-shortcut-content">
                        <h3>مدیریت ادمین‌ها</h3>
                        <p>مشاهده حساب مدیران سایت</p>
                    </div>

                    <span
                        class="roma-dashboard-shortcut-arrow"
                        aria-hidden="true"
                    >←</span>

                </a>


                <!-- پیام‌های کاربران -->
                <a
                    href="messages/index.php"
                    class="roma-dashboard-shortcut"
                >

                    <div
                        class="roma-dashboard-shortcut-icon"
                        aria-hidden="true"
                    >
                        ✉
                    </div>

                    <div class="roma-dashboard-shortcut-content">
                        <h3>پیام‌های کاربران</h3>
                        <p>مشاهده پیام‌ها و پاسخ به بازدیدکنندگان</p>
                    </div>

                    <span
                        class="roma-dashboard-shortcut-arrow"
                        aria-hidden="true"
                    >←</span>

                </a>

            </div>

        </section>


        <!-- راهنمای داشبورد -->
        <section class="roma-dashboard-note">

            <div
                class="roma-dashboard-note-icon"
                aria-hidden="true"
            >
                !
            </div>

            <p>
                مقاله‌های ارسالی بازدیدکنندگان را پیش از انتشار بررسی کنید.
                وضعیت این مطالب از بخش مدیریت مقالات قابل تغییر است.
            </p>

        </section>

    </div>

</main>

<?php include "layouts/footer.php"; ?>