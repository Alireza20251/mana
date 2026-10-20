<?php

require_once dirname(__DIR__, 2) . "/functions/DB.php";

global $conn;

$unreadMessages = 0;

if (isset($conn) && $conn) {

    $stmt = $conn->prepare(
        "SELECT COUNT(*)
         FROM messages
         WHERE sender = 'visitor'
         AND is_read = 0"
    );

    $stmt->execute();

    $unreadMessages = (int) $stmt->fetchColumn();
}

?>

<div class="navigation">

    <!-- نوار آیکون‌ها -->
    <div class="navigation-icon-menu">

        <ul>

            <!-- ادمین‌ها -->
            <li data-toggle="tooltip" title="ادمین‌ها">
                <a href="#users" title="ادمین‌ها" aria-label="ادمین‌ها">
                    <i class="icon ti-user"></i>
                </a>
            </li>

            <!-- بلاگ -->
            <li data-toggle="tooltip" title="بلاگ">
                <a href="#blog" title="بلاگ" aria-label="بلاگ">
                    <i class="icon ti-book"></i>
                </a>
            </li>

            <!-- بازدیدکنندگان -->
            <li data-toggle="tooltip" title="بازدیدکنندگان">
                <a
                    href="#visitors"
                    title="بازدیدکنندگان"
                    aria-label="بازدیدکنندگان"
                >
                    <i class="icon ti-id-badge"></i>
                </a>
            </li>

            <!-- پیام‌ها -->
            <li data-toggle="tooltip" title="پیام‌ها">
                <a href="#messages" title="پیام‌ها" aria-label="پیام‌ها">
                    <i class="icon ti-comments"></i>

                    <?php if ($unreadMessages > 0) { ?>
                        <span class="roma-nav-icon-badge">
                            <?php echo $unreadMessages; ?>
                        </span>
                    <?php } ?>

                </a>
            </li>

        </ul>

        <ul>

            <!-- تنظیمات و مدیریت ادمین‌ها -->
            <li data-toggle="tooltip" title="مدیریت ادمین‌ها">
                <a
                    href="<?php echo url('panel/users/index.php'); ?>"
                    class="go-to-page"
                    aria-label="مدیریت ادمین‌ها"
                >
                    <i class="icon ti-settings"></i>
                </a>
            </li>

            <!-- خروج -->
            <li data-toggle="tooltip" title="خروج">
                <a
                    href="<?php echo url('auth/logout.php'); ?>"
                    class="go-to-page"
                    aria-label="خروج از پنل"
                >
                    <i class="icon ti-power-off"></i>
                </a>
            </li>

        </ul>

    </div>

    <!-- منوهای اصلی -->
    <div class="navigation-menu-body">

        <!-- ادمین‌ها -->
        <ul id="users">

            <li>

                <a href="#">ادمین‌ها</a>

                <ul>

                    <li>
                        <a href="<?php echo url('panel/users/create.php'); ?>">
                            افزودن ادمین
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo url('panel/users/index.php'); ?>">
                            فهرست ادمین‌ها
                        </a>
                    </li>

                </ul>

            </li>

        </ul>

        <!-- بلاگ -->
        <ul id="blog">

            <li>

                <a href="#">دسته‌بندی‌ها</a>

                <ul>

                    <li>
                        <a href="<?php echo url('panel/categories/create.php'); ?>">
                            ایجاد دسته‌بندی
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo url('panel/categories/index.php'); ?>">
                            فهرست دسته‌بندی‌ها
                        </a>
                    </li>

                </ul>

            </li>

            <li>

                <a href="#">مقاله‌ها</a>

                <ul>

                    <li>
                        <a href="<?php echo url('panel/articles/create.php'); ?>">
                            ایجاد مقاله
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo url('panel/articles/index.php'); ?>">
                            فهرست مقاله‌ها
                        </a>
                    </li>

                </ul>

            </li>

        </ul>

        <!-- بازدیدکنندگان -->
        <ul id="visitors">

            <li>

                <a href="#">بازدیدکنندگان</a>

                <ul>

                    <li>
                        <a href="<?php echo url('panel/visitors/index.php'); ?>">
                            فهرست بازدیدکنندگان
                        </a>
                    </li>

                </ul>

            </li>

        </ul>

        <!-- پیام‌ها -->
        <ul id="messages">

            <li>

                <a href="#">
                    پیام‌ها

                    <?php if ($unreadMessages > 0) { ?>
                        <span class="roma-nav-unread-count">
                            <?php echo $unreadMessages; ?>
                        </span>
                    <?php } ?>

                </a>

                <ul>

                    <li>
                        <a href="<?php echo url('panel/messages/index.php'); ?>">

                            فهرست پیام‌ها

                            <?php if ($unreadMessages > 0) { ?>
                                <span class="roma-nav-unread-text">
                                    (<?php echo $unreadMessages; ?> جدید)
                                </span>
                            <?php } ?>

                        </a>
                    </li>

                </ul>

            </li>

        </ul>

    </div>

</div>