<?php

session_start();

/* فایل‌های اصلی پروژه */
require_once "../../functions/helpers.php";
require_once "../../functions/users.php";
require_once "../../functions/visitor_users.php";

/* بررسی ورود مدیر */
if (
    !isset($_SESSION['user'])
    || !is_string($_SESSION['user'])
    || trim($_SESSION['user']) === ''
) {
    header('Location: ../../auth/login.php');
    exit;
}

/* بررسی اعتبار حساب مدیر */
try {

    $currentAdmin = checkUser($_SESSION['user']);

    if (!$currentAdmin) {
        $_SESSION = [];
        session_destroy();

        header('Location: ../../auth/login.php');
        exit;
    }

} catch (Throwable $e) {

    error_log(
        "Roma visitors admin authentication error: " . $e->getMessage()
    );

    http_response_code(500);
    exit("در حال حاضر امکان بررسی دسترسی وجود ندارد.");
}

/* مقادیر صفحه */
$visitors = [];
$error = "";
$search = "";

/* دریافت فهرست بازدیدکنندگان */
try {

    $visitors = getAllVisitors();

} catch (Throwable $e) {

    error_log(
        "Roma visitors list error: " . $e->getMessage()
    );

    $error = "دریافت فهرست بازدیدکنندگان انجام نشد. لطفاً دوباره تلاش کنید.";
}

/* فیلتر بر اساس نام یا ایمیل */
if ($error === '' && isset($_GET['q'])) {

    if (!is_string($_GET['q'])) {

        $error = "عبارت جست‌وجو معتبر نیست.";

    } else {

        $search = trim($_GET['q']);

        if (mb_strlen($search, 'UTF-8') > 100) {
            $search = mb_substr($search, 0, 100, 'UTF-8');
        }

        if ($search !== '') {

            $visitors = array_values(
                array_filter(
                    $visitors,
                    function ($visitor) use ($search) {

                        $name = (string) ($visitor->name ?? '');
                        $email = (string) ($visitor->email ?? '');

                        return mb_stripos($name, $search, 0, 'UTF-8') !== false
                            || mb_stripos($email, $search, 0, 'UTF-8') !== false;
                    }
                )
            );
        }
    }
}

/* فعال‌کردن CSS اختصاصی این صفحه */
$panelVisitorsCss = true;

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content roma-panel-visitors">

    <div class="roma-visitors-container">

        <!-- عنوان صفحه -->
        <section class="roma-visitors-heading">

            <div class="roma-visitors-heading-icon" aria-hidden="true">
                ♙
            </div>

            <div>
                <h1>مدیریت بازدیدکنندگان</h1>

                <p>
                    فهرست افرادی که در سایت روما ثبت‌نام کرده‌اند.
                </p>
            </div>

        </section>

        <!-- آمار -->
        <section class="roma-visitors-stat">

            <div class="roma-visitors-stat-icon" aria-hidden="true">
                👥
            </div>

            <div>
                <span>تعداد بازدیدکنندگان</span>

                <strong>
                    <?php echo count($visitors); ?>
                </strong>
            </div>

        </section>

        <!-- فهرست کاربران -->
        <section class="roma-visitors-card">

            <div class="roma-visitors-card-header">

                <div>
                    <h2>بازدیدکنندگان ثبت‌نام‌شده</h2>

                    <p>
                        نام، ایمیل و تاریخ ثبت‌نام کاربران را مشاهده کنید.
                    </p>
                </div>

            </div>

            <!-- جست‌وجو -->
            <form
                method="GET"
                action="index.php"
                class="roma-visitors-search"
            >

                <input
                    type="search"
                    name="q"
                    value="<?php
                    echo htmlspecialchars(
                        $search,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                    placeholder="جست‌وجو بر اساس نام یا ایمیل..."
                    maxlength="100"
                    aria-label="جست‌وجوی بازدیدکنندگان"
                >

                <button type="submit">
                    جست‌وجو
                </button>

                <?php if ($search !== '') { ?>

                    <a href="index.php">
                        پاک‌کردن جست‌وجو
                    </a>

                <?php } ?>

            </form>

            <?php if ($error !== '') { ?>

                <div class="roma-visitors-error" role="alert">

                    <?php
                    echo htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            <?php } else { ?>

                <div class="roma-visitors-table-wrap">

                    <table class="roma-visitors-table">

                        <thead>
                            <tr>
                                <th>شناسه</th>
                                <th>نام بازدیدکننده</th>
                                <th>ایمیل</th>
                                <th>تاریخ ثبت‌نام</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (!empty($visitors)) { ?>

                                <?php foreach ($visitors as $visitor) { ?>

                                    <tr>

                                        <td>
                                            <?php
                                            echo (int) $visitor->id;
                                            ?>
                                        </td>

                                        <td>

                                            <div class="roma-visitors-user">

                                                <div class="roma-visitors-avatar">
                                                    <?php
                                                    echo htmlspecialchars(
                                                        mb_substr(
                                                            (string) ($visitor->name ?? '?'),
                                                            0,
                                                            1,
                                                            'UTF-8'
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                    ?>
                                                </div>

                                                <strong>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        (string) ($visitor->name ?? 'نامشخص'),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    );
                                                    ?>
                                                </strong>

                                            </div>

                                        </td>

                                        <td>
                                            <span class="roma-visitors-email" dir="ltr">
                                                <?php
                                                echo htmlspecialchars(
                                                    (string) ($visitor->email ?? ''),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                (string) ($visitor->created_at ?? '—'),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>
                                        </td>

                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="4">

                                        <div class="roma-visitors-empty">

                                            <span aria-hidden="true">♙</span>

                                            <h3>
                                                <?php echo $search !== ''
                                                    ? 'بازدیدکننده‌ای پیدا نشد'
                                                    : 'هنوز بازدیدکننده‌ای ثبت‌نام نکرده است'; ?>
                                            </h3>

                                            <p>
                                                <?php echo $search !== ''
                                                    ? 'نام یا ایمیل دیگری را جست‌وجو کنید.'
                                                    : 'پس از ثبت‌نام کاربران، اطلاعات آن‌ها در این بخش نمایش داده می‌شود.'; ?>
                                            </p>

                                        </div>

                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            <?php } ?>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>