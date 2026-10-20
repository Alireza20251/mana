<?php

require_once dirname(__DIR__, 2) . "/functions/helpers.php";
require_once dirname(__DIR__, 2) . "/functions/DB.php";

global $conn;

/*
|--------------------------------------------------------------------------
| عنوان و توضیحات صفحه جاری
|--------------------------------------------------------------------------
*/

$currentPath = str_replace(
    '\\',
    '/',
    parse_url($_SERVER['PHP_SELF'] ?? '', PHP_URL_PATH) ?: ''
);

$pageTitle = 'پنل مدیریت روما';
$pageDescription = 'مدیریت محتوا و کاربران رسانه روما';
$pageSection = 'پنل مدیریت';

$pageMap = [

    '/panel/index.php' => [
        'title' => 'داشبورد',
        'description' => 'نمای کلی مدیریت رسانه و مجله روما',
        'section' => 'داشبورد'
    ],

    '/panel/articles/index.php' => [
        'title' => 'فهرست مقالات',
        'description' => 'مشاهده و مدیریت مقالات منتشرشده و پیش‌نویس‌ها',
        'section' => 'مقالات'
    ],

    '/panel/articles/create.php' => [
        'title' => 'افزودن مقاله',
        'description' => 'ثبت یک مقاله جدید در رسانه روما',
        'section' => 'مقالات'
    ],

    '/panel/articles/edit.php' => [
        'title' => 'ویرایش مقاله',
        'description' => 'ویرایش اطلاعات و محتوای مقاله',
        'section' => 'مقالات'
    ],

    '/panel/articles/delete.php' => [
        'title' => 'حذف مقاله',
        'description' => 'بررسی و تأیید حذف مقاله',
        'section' => 'مقالات'
    ],

    '/panel/categories/index.php' => [
        'title' => 'فهرست دسته‌بندی‌ها',
        'description' => 'مدیریت دسته‌بندی‌های مقالات',
        'section' => 'دسته‌بندی‌ها'
    ],

    '/panel/categories/create.php' => [
        'title' => 'افزودن دسته‌بندی',
        'description' => 'ساخت دسته‌بندی جدید برای مقالات',
        'section' => 'دسته‌بندی‌ها'
    ],

    '/panel/categories/edit.php' => [
        'title' => 'ویرایش دسته‌بندی',
        'description' => 'ویرایش اطلاعات دسته‌بندی',
        'section' => 'دسته‌بندی‌ها'
    ],

    '/panel/categories/delete.php' => [
        'title' => 'حذف دسته‌بندی',
        'description' => 'بررسی و تأیید حذف دسته‌بندی',
        'section' => 'دسته‌بندی‌ها'
    ],

    '/panel/users/index.php' => [
        'title' => 'فهرست ادمین‌ها',
        'description' => 'مدیریت حساب‌های مدیران سایت',
        'section' => 'ادمین‌ها'
    ],

    '/panel/users/create.php' => [
        'title' => 'افزودن ادمین',
        'description' => 'ساخت حساب مدیر جدید',
        'section' => 'ادمین‌ها'
    ],

    '/panel/users/edit.php' => [
        'title' => 'ویرایش ادمین',
        'description' => 'ویرایش اطلاعات حساب مدیر',
        'section' => 'ادمین‌ها'
    ],

    '/panel/users/delete.php' => [
        'title' => 'حذف ادمین',
        'description' => 'بررسی و تأیید حذف حساب مدیر',
        'section' => 'ادمین‌ها'
    ],

    '/panel/messages/index.php' => [
        'title' => 'پیام‌های بازدیدکنندگان',
        'description' => 'مشاهده و پیگیری گفتگوهای کاربران',
        'section' => 'پیام‌ها'
    ],

    '/panel/messages/reply.php' => [
        'title' => 'گفتگو با بازدیدکننده',
        'description' => 'مشاهده پیام‌ها و ارسال پاسخ به کاربر',
        'section' => 'پیام‌ها'
    ],
];

foreach ($pageMap as $suffix => $pageInfo) {
    if (str_ends_with($currentPath, $suffix)) {
        $pageTitle = $pageInfo['title'];
        $pageDescription = $pageInfo['description'];
        $pageSection = $pageInfo['section'];
        break;
    }
}

/*
|--------------------------------------------------------------------------
| دریافت تعداد و فهرست پیام‌های خوانده‌نشده
|--------------------------------------------------------------------------
*/

$unreadMessagesCount = 0;
$recentUnreadMessages = [];

if (isset($conn) && $conn) {

    try {

        $countStmt = $conn->prepare(
            "SELECT COUNT(*)
             FROM messages
             WHERE sender = 'visitor'
             AND is_read = 0"
        );

        $countStmt->execute();

        $unreadMessagesCount = (int) $countStmt->fetchColumn();

        if ($unreadMessagesCount > 0) {

            $recentStmt = $conn->prepare(
                "SELECT
                    m.visitor_id,
                    m.message,
                    m.created_at,
                    v.name
                 FROM messages m
                 INNER JOIN visitor_users v
                    ON m.visitor_id = v.id
                 WHERE m.sender = 'visitor'
                   AND m.is_read = 0
                 ORDER BY m.id DESC
                 LIMIT 5"
            );

            $recentStmt->execute();

            $recentUnreadMessages = $recentStmt->fetchAll();
        }

    } catch (Throwable $exception) {

        $unreadMessagesCount = 0;
        $recentUnreadMessages = [];

    }
}

?>

<div class="header roma-panel-header">

    <div class="header-body">

        <!-- سمت راست: عنوان صفحه -->
        <div class="header-body-left roma-header-title-area">

            <div class="roma-header-brand-mark">
                ر
            </div>

            <div class="roma-header-title-content">

                <div class="roma-header-title-line">

                    <h3 class="page-title">
                        <?php
                        echo htmlspecialchars(
                            $pageTitle,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </h3>

                    <span class="roma-header-brand-label">
                        مدیریت روما
                    </span>

                </div>

                <p class="roma-header-description">
                    <?php
                    echo htmlspecialchars(
                        $pageDescription,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </p>

                <nav aria-label="breadcrumb" class="roma-header-breadcrumb">
                    <ol class="breadcrumb">

                        <li class="breadcrumb-item">
                            <a href="<?php echo url('panel/index.php'); ?>">
                                داشبورد
                            </a>
                        </li>

                        <?php if ($pageTitle !== 'داشبورد') { ?>

                            <li class="breadcrumb-item">
                                <?php
                                echo htmlspecialchars(
                                    $pageSection,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </li>

                            <li class="breadcrumb-item active"
                                aria-current="page">
                                <?php
                                echo htmlspecialchars(
                                    $pageTitle,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </li>

                        <?php } ?>

                    </ol>
                </nav>

            </div>

        </div>

        <!-- سمت چپ: دسترسی سریع و اعلان‌ها -->
        <div class="header-body-right roma-header-actions">

            <ul class="navbar-nav roma-header-nav">

                <!-- دسترسی سریع -->
                <li class="nav-item dropdown roma-header-dropdown">

                    <a
                        href="#"
                        class="nav-link roma-header-icon-link"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-label="دسترسی سریع"
                        title="دسترسی سریع"
                    >
                        <i class="ti-plus"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-big roma-header-dropdown-menu">

                        <div class="roma-header-dropdown-heading">
                            <h6>دسترسی سریع</h6>
                            <p>صفحات پرکاربرد پنل روما</p>
                        </div>

                        <div class="roma-header-quick-links">

                            <a
                                href="<?php echo url('panel/articles/create.php'); ?>"
                                class="roma-header-quick-link"
                            >
                                <span class="roma-quick-icon roma-quick-article">
                                    <i class="ti-pencil"></i>
                                </span>

                                <span>
                                    <strong>افزودن مقاله</strong>
                                    <small>ثبت محتوای جدید</small>
                                </span>
                            </a>

                            <a
                                href="<?php echo url('panel/categories/create.php'); ?>"
                                class="roma-header-quick-link"
                            >
                                <span class="roma-quick-icon roma-quick-category">
                                    <i class="ti-book"></i>
                                </span>

                                <span>
                                    <strong>افزودن دسته‌بندی</strong>
                                    <small>دسته‌بندی مقالات</small>
                                </span>
                            </a>

                            <a
                                href="<?php echo url('panel/articles/index.php'); ?>"
                                class="roma-header-quick-link"
                            >
                                <span class="roma-quick-icon roma-quick-list">
                                    <i class="ti-view-list"></i>
                                </span>

                                <span>
                                    <strong>فهرست مقالات</strong>
                                    <small>مدیریت محتوا</small>
                                </span>
                            </a>

                            <a
                                href="<?php echo url('panel/messages/index.php'); ?>"
                                class="roma-header-quick-link"
                            >
                                <span class="roma-quick-icon roma-quick-message">
                                    <i class="ti-comments"></i>
                                </span>

                                <span>
                                    <strong>پیام‌ها</strong>
                                    <small>گفتگو با بازدیدکنندگان</small>
                                </span>
                            </a>

                        </div>

                    </div>

                </li>

                <!-- اعلان پیام‌ها -->
                <li class="nav-item dropdown roma-header-dropdown">

                    <a
                        href="#"
                        class="nav-link roma-header-icon-link"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-label="اعلان پیام‌ها"
                        title="اعلان پیام‌ها"
                    >

                        <i class="ti-comment"></i>

                        <?php if ($unreadMessagesCount > 0) { ?>

                            <span class="roma-header-notification-badge">
                                <?php echo $unreadMessagesCount > 99
                                    ? '99+'
                                    : $unreadMessagesCount; ?>
                            </span>

                        <?php } ?>

                    </a>

                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-big roma-header-dropdown-menu roma-header-notifications-menu">

                        <div class="roma-header-notifications-heading">

                            <div>
                                <h6>پیام‌های خوانده‌نشده</h6>
                                <p>آخرین پیام‌های دریافتی کاربران</p>
                            </div>

                            <span class="roma-header-notifications-count">
                                <?php echo $unreadMessagesCount; ?>
                            </span>

                        </div>

                        <div class="roma-header-notifications-list">

                            <?php if (!empty($recentUnreadMessages)) { ?>

                                <?php foreach ($recentUnreadMessages as $notification) { ?>

                                    <?php
                                    $notificationText = mb_substr(
                                        (string) $notification->message,
                                        0,
                                        65,
                                        'UTF-8'
                                    );

                                    if (
                                        mb_strlen(
                                            (string) $notification->message,
                                            'UTF-8'
                                        ) > 65
                                    ) {
                                        $notificationText .= '…';
                                    }
                                    ?>

                                    <a
                                        href="<?php
                                        echo url('panel/messages/reply.php?visitor_id=')
                                            . (int) $notification->visitor_id;
                                        ?>"
                                        class="roma-header-notification-item"
                                    >

                                        <span class="roma-header-notification-avatar">
                                            <?php
                                            echo htmlspecialchars(
                                                mb_substr(
                                                    $notification->name,
                                                    0,
                                                    1,
                                                    'UTF-8'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>
                                        </span>

                                        <span class="roma-header-notification-content">

                                            <strong>
                                                <?php
                                                echo htmlspecialchars(
                                                    $notification->name,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>
                                            </strong>

                                            <span class="roma-header-notification-text">
                                                <?php
                                                echo nl2br(
                                                    htmlspecialchars(
                                                        $notificationText,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    )
                                                );
                                                ?>
                                            </span>

                                            <small>
                                                <?php
                                                echo htmlspecialchars(
                                                    $notification->created_at,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>
                                            </small>

                                        </span>

                                    </a>

                                <?php } ?>

                            <?php } else { ?>

                                <div class="roma-header-no-notifications">

                                    <span class="roma-header-empty-icon">
                                        ✓
                                    </span>

                                    <strong>پیام خوانده‌نشده‌ای ندارید</strong>

                                    <p>
                                        پیام جدیدی برای بررسی وجود ندارد.
                                    </p>

                                </div>

                            <?php } ?>

                        </div>

                        <a
                            href="<?php echo url('panel/messages/index.php'); ?>"
                            class="roma-header-all-notifications"
                        >
                            مشاهده همه پیام‌ها
                            <span aria-hidden="true">←</span>
                        </a>

                    </div>

                </li>

            </ul>

            <!-- دکمه‌های نمایش منو در قالب اصلی -->
            <div class="d-flex align-items-center roma-header-togglers">

                <div class="d-xl-none d-lg-none d-sm-block navigation-toggler">
                    <a href="#" aria-label="نمایش یا پنهان کردن منو">
                        <i class="ti-menu"></i>
                    </a>
                </div>

                <div class="d-xl-none d-lg-none d-sm-block navbar-toggler">
                    <a href="#" aria-label="نمایش یا پنهان کردن نوار ابزار">
                        <i class="ti-arrow-down"></i>
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>