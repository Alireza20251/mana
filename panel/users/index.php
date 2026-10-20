<?php

session_start();

/* بررسی ورود مدیر */
if (!isset($_SESSION['user'])) {
    header('Location: ../../auth/login.php');
    exit;
}

/* فایل‌های اصلی پروژه */
require_once "../../functions/helpers.php";
require_once "../../functions/users.php";

/* فعال‌کردن CSS اختصاصی صفحه */
$panelUsersCss = true;

/* جستجو بر اساس نام یا ایمیل */
$searchTerm = trim((string) ($_GET['search'] ?? ''));

/* دریافت فهرست ادمین‌ها */
$users = getAllUsers();

/* فیلتر نتایج بدون تغییر تابع اصلی */
if ($searchTerm !== '') {
    $users = array_values(array_filter(
        $users,
        function ($user) use ($searchTerm) {

            $name = (string) ($user->name ?? '');
            $email = (string) ($user->email ?? '');

            if (function_exists('mb_stripos')) {
                return
                    mb_stripos($name, $searchTerm, 0, 'UTF-8') !== false
                    || mb_stripos($email, $searchTerm, 0, 'UTF-8') !== false;
            }

            return
                stripos($name, $searchTerm) !== false
                || stripos($email, $searchTerm) !== false;
        }
    ));
}

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content">

    <div class="roma-admin-users-page">

        <!-- سربرگ صفحه -->
        <section class="roma-users-hero">

            <div class="roma-users-hero-content">

                <span class="roma-users-badge">
                    مدیریت دسترسی‌ها
                </span>

                <h1>مدیریت ادمین‌ها</h1>

                <p>
                    حساب مدیران روما را مشاهده و مدیریت کنید.
                    از این بخش می‌توانید اطلاعات ادمین‌ها را ویرایش کنید.
                </p>

            </div>

            <a
                href="<?php echo url('panel/users/create.php'); ?>"
                class="roma-users-add-button"
            >
                <span aria-hidden="true">+</span>
                افزودن ادمین
            </a>

        </section>


        <!-- کارت فهرست -->
        <section class="roma-users-card">

            <div class="roma-users-toolbar">

                <div class="roma-users-toolbar-title">

                    <h2>فهرست ادمین‌ها</h2>

                    <span class="roma-users-count">
                        <?php echo number_format(count($users)); ?>
                        ادمین
                    </span>

                </div>


                <!-- جستجو -->
                <form
                    method="get"
                    action=""
                    class="roma-users-search"
                >

                    <label for="admin-search">
                        جستجو بر اساس نام یا ایمیل
                    </label>

                    <div class="roma-users-search-control">

                        <input
                            type="search"
                            id="admin-search"
                            name="search"
                            value="<?php echo htmlspecialchars(
                                $searchTerm,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            placeholder="نام یا ایمیل ادمین..."
                        >

                        <button type="submit">
                            جستجو
                        </button>

                    </div>

                    <?php if ($searchTerm !== '') { ?>

                        <a
                            href="<?php echo htmlspecialchars(
                                url('panel/users/index.php'),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            class="roma-users-reset"
                        >
                            پاک کردن جستجو
                        </a>

                    <?php } ?>

                </form>

            </div>


            <!-- جدول ادمین‌ها -->
            <div
                class="roma-users-table-wrapper"
                tabindex="0"
                role="region"
                aria-label="فهرست ادمین‌های روما"
            >

                <table class="roma-users-table">

                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>تصویر</th>
                            <th class="roma-users-name-column">
                                نام و نام خانوادگی
                            </th>
                            <th>ایمیل</th>
                            <th>ویرایش</th>
                            <th>حذف</th>
                            <th>تاریخ ایجاد</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($users)) { ?>

                        <?php $i = 1; ?>

                        <?php foreach ($users as $user) { ?>

                            <tr>

                                <!-- ردیف -->
                                <td class="roma-users-row-number">
                                    <?php echo $i++; ?>
                                </td>


                                <!-- تصویر ادمین -->
                                <td>

                                    <div class="roma-users-avatar">

                                        <?php if (!empty($user->image)) { ?>

                                            <img
                                                src="<?php echo htmlspecialchars(
                                                    asset(
                                                        'images/users/' .
                                                        $user->image
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>"
                                                alt="<?php echo htmlspecialchars(
                                                    $user->name ?? 'ادمین',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>"
                                                loading="lazy"
                                            >

                                        <?php } else { ?>

                                            <span aria-hidden="true">
                                                ♙
                                            </span>

                                        <?php } ?>

                                    </div>

                                </td>


                                <!-- نام -->
                                <td class="roma-users-name">

                                    <?php echo htmlspecialchars(
                                        $user->name ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </td>


                                <!-- ایمیل -->
                                <td class="roma-users-email">

                                    <?php echo htmlspecialchars(
                                        $user->email ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </td>


                                <!-- ویرایش -->
                                <td>

                                    <a
                                        class="roma-users-action roma-users-edit"
                                        href="<?php echo htmlspecialchars(
                                            url('panel/users/edit.php?id=') .
                                            (int) $user->id,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >
                                        ویرایش
                                    </a>

                                </td>


                                <!-- حذف -->
                                <td>

                                    <a
                                        class="roma-users-action roma-users-delete"
                                        href="<?php echo htmlspecialchars(
                                            url('panel/users/delete.php?id=') .
                                            (int) $user->id,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>"
                                    >
                                        حذف
                                    </a>

                                </td>


                                <!-- تاریخ ایجاد -->
                                <td class="roma-users-date">

                                    <?php echo htmlspecialchars(
                                        $user->created_at ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </td>

                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="7" class="roma-users-empty-cell">

                                <div class="roma-users-empty">

                                    <span
                                        class="roma-users-empty-icon"
                                        aria-hidden="true"
                                    >
                                        ♙
                                    </span>

                                    <h3>
                                        <?php if ($searchTerm !== '') { ?>
                                            ادمینی با این مشخصات پیدا نشد
                                        <?php } else { ?>
                                            ادمینی برای نمایش وجود ندارد
                                        <?php } ?>
                                    </h3>

                                    <p>
                                        <?php if ($searchTerm !== '') { ?>
                                            عبارت جستجو را تغییر بده و دوباره تلاش کن.
                                        <?php } else { ?>
                                            از طریق دکمه افزودن ادمین می‌توانی مدیر جدیدی ثبت کنی.
                                        <?php } ?>
                                    </p>

                                    <?php if ($searchTerm === '') { ?>

                                        <a
                                            href="<?php echo url(
                                                'panel/users/create.php'
                                            ); ?>"
                                            class="roma-users-add-button"
                                        >
                                            <span aria-hidden="true">+</span>
                                            افزودن ادمین
                                        </a>

                                    <?php } ?>

                                </div>

                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>


            <!-- پایین جدول -->
            <div class="roma-users-footer">

                تعداد نتایج:

                <strong>
                    <?php echo number_format(count($users)); ?>
                </strong>

            </div>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>