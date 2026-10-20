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
    header('Location: ../../auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| فایل‌های اصلی پروژه
|--------------------------------------------------------------------------
*/

require_once "../../functions/helpers.php";
require_once "../../functions/DB.php";
require_once "../../functions/users.php";

/*
|--------------------------------------------------------------------------
| اعتبارسنجی حساب مدیر
|--------------------------------------------------------------------------
*/

try {
    $currentAdmin = checkUser($_SESSION['user']);
} catch (Throwable $e) {
    error_log(
        "Roma messages admin authentication error: " . $e->getMessage()
    );

    http_response_code(500);
    exit("در حال حاضر امکان بررسی دسترسی وجود ندارد.");
}

if (!$currentAdmin) {

    $_SESSION = [];

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

    session_destroy();

    header('Location: ../../auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| دریافت آخرین پیام هر بازدیدکننده
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        m.id,
        m.visitor_id,
        m.message,
        m.sender,
        m.is_read,
        m.created_at,
        v.name,
        v.email
    FROM messages m
    INNER JOIN visitor_users v
        ON m.visitor_id = v.id
    WHERE m.id IN (
        SELECT MAX(id)
        FROM messages
        GROUP BY visitor_id
    )
    ORDER BY m.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$messages = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| محاسبه آمار گفتگوها بر اساس آخرین پیام هر بازدیدکننده
|--------------------------------------------------------------------------
*/

$totalConversations = count($messages);
$newMessages = 0;
$adminLastMessages = 0;

foreach ($messages as $item) {

    if (
        $item->sender === "visitor"
        && (int) $item->is_read === 0
    ) {
        $newMessages++;
    }

    if ($item->sender === "admin") {
        $adminLastMessages++;
    }
}

$panelMessagesCss = true;

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content roma-panel-messages">

    <div class="r-messages-container">

        <!-- عنوان صفحه -->
        <div class="r-messages-heading">

            <div class="r-messages-heading-icon">
                <span aria-hidden="true">✉</span>
            </div>

            <div class="r-messages-heading-text">
                <h1>پیام‌های بازدیدکنندگان</h1>
                <p>
                    گفتگوهای کاربران رو بررسی کنید و از همین بخش به پیام‌ها پاسخ بدید.
                </p>
            </div>

        </div>

        <!-- آمار گفتگوها -->
        <div class="r-messages-stats">

            <div class="r-messages-stat-card">

                <div class="r-messages-stat-icon r-stat-icon-total">
                    <span aria-hidden="true">☰</span>
                </div>

                <div class="r-messages-stat-info">
                    <span>تعداد گفتگوها</span>
                    <strong><?php echo $totalConversations; ?></strong>
                </div>

            </div>

            <div class="r-messages-stat-card">

                <div class="r-messages-stat-icon r-stat-icon-new">
                    <span aria-hidden="true">!</span>
                </div>

                <div class="r-messages-stat-info">
                    <span>پیام‌های جدید</span>
                    <strong><?php echo $newMessages; ?></strong>
                </div>

            </div>

            <div class="r-messages-stat-card">

                <div class="r-messages-stat-icon r-stat-icon-replied">
                    <span aria-hidden="true">✓</span>
                </div>

                <div class="r-messages-stat-info">
                    <span>آخرین پیام از مدیریت</span>
                    <strong><?php echo $adminLastMessages; ?></strong>
                </div>

            </div>

        </div>

        <!-- جدول پیام‌ها -->
        <section class="r-messages-card">

            <div class="r-messages-card-header">

                <div>
                    <h2>فهرست گفتگوها</h2>
                    <p>
                        آخرین پیام ثبت‌شده برای هر بازدیدکننده نمایش داده می‌شود.
                    </p>
                </div>

                <span class="r-messages-count">
                    <?php echo $totalConversations; ?> گفتگو
                </span>

            </div>

            <div class="r-messages-table-wrap">

                <table class="r-messages-table">

                    <thead>
                        <tr>
                            <th class="r-messages-number">ردیف</th>
                            <th>بازدیدکننده</th>
                            <th>ایمیل</th>
                            <th class="r-messages-text-column">آخرین پیام</th>
                            <th>فرستنده</th>
                            <th>وضعیت</th>
                            <th>گفتگو</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($messages)) { ?>

                        <?php $i = 1; ?>

                        <?php foreach ($messages as $item) { ?>

                            <?php
                            $fullMessage = (string) $item->message;
                            $shortMessage = mb_substr(
                                $fullMessage,
                                0,
                                100,
                                'UTF-8'
                            );

                            $hasLongMessage = mb_strlen(
                                $fullMessage,
                                'UTF-8'
                            ) > 100;
                            ?>

                            <tr>

                                <!-- ردیف -->
                                <td class="r-messages-number">
                                    <span class="r-messages-row-number">
                                        <?php echo $i++; ?>
                                    </span>
                                </td>

                                <!-- نام -->
                                <td>
                                    <div class="r-messages-visitor">

                                        <div class="r-messages-avatar">
                                            <?php
                                            echo htmlspecialchars(
                                                mb_substr(
                                                    $item->name,
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
                                                $item->name,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>
                                        </strong>

                                    </div>
                                </td>

                                <!-- ایمیل -->
                                <td>
                                    <span class="r-messages-email" dir="ltr">
                                        <?php
                                        echo htmlspecialchars(
                                            $item->email,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </span>
                                </td>

                                <!-- آخرین پیام -->
                                <td class="r-messages-message-cell">

                                    <div class="r-messages-message">
                                        <?php
                                        echo nl2br(
                                            htmlspecialchars(
                                                $shortMessage,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                        );

                                        if ($hasLongMessage) {
                                            echo '…';
                                        }
                                        ?>
                                    </div>

                                </td>

                                <!-- فرستنده -->
                                <td>

                                    <?php if ($item->sender === "visitor") { ?>

                                        <span class="r-messages-sender r-sender-visitor">
                                            بازدیدکننده
                                        </span>

                                    <?php } else { ?>

                                        <span class="r-messages-sender r-sender-admin">
                                            مدیریت
                                        </span>

                                    <?php } ?>

                                </td>

                                <!-- وضعیت -->
                                <td>

                                    <?php
                                    if (
                                        $item->sender === "visitor"
                                        && (int) $item->is_read === 0
                                    ) {
                                    ?>

                                        <span class="r-messages-status r-status-new">
                                            <span class="r-status-dot"></span>
                                            جدید
                                        </span>

                                    <?php } else { ?>

                                        <span class="r-messages-status r-status-read">
                                            <span class="r-status-dot"></span>
                                            خوانده شده
                                        </span>

                                    <?php } ?>

                                </td>

                                <!-- مشاهده گفتگو -->
                                <td>

                                    <a
                                        href="<?php
                                        echo url('panel/messages/reply.php?visitor_id=')
                                            . (int) $item->visitor_id;
                                        ?>"
                                        class="r-messages-open-btn"
                                    >
                                        مشاهده گفتگو
                                        <span aria-hidden="true">←</span>
                                    </a>

                                </td>

                            </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>
                            <td colspan="7">

                                <div class="r-messages-empty">

                                    <div class="r-messages-empty-icon">
                                        ✉
                                    </div>

                                    <h3>هنوز پیامی دریافت نشده است</h3>

                                    <p>
                                        وقتی بازدیدکنندگان برای روما پیام بفرستند،
                                        گفتگوهای آن‌ها در این بخش نمایش داده می‌شود.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>