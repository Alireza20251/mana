<?php

session_start();

/*
|--------------------------------------------------------------------------
| فایل‌های اصلی پروژه
|--------------------------------------------------------------------------
*/

require_once "../../functions/helpers.php";
require_once "../../functions/DB.php";
require_once "../../functions/users.php";
require_once "../../functions/visitor_users.php";

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
| بررسی اعتبار حساب مدیر در دیتابیس
|--------------------------------------------------------------------------
*/

try {
    $currentAdmin = checkUser($_SESSION['user']);
} catch (Throwable $e) {
    error_log(
        "Roma message reply admin authentication error: "
        . $e->getMessage()
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
| بررسی شناسه بازدیدکننده
|--------------------------------------------------------------------------
*/

$rawVisitorId = $_GET['visitor_id'] ?? null;

if (
    (!is_string($rawVisitorId) && !is_int($rawVisitorId))
    || !ctype_digit((string) $rawVisitorId)
    || (int) $rawVisitorId < 1
) {
    header('Location: index.php');
    exit;
}

$visitor_id = (int) $rawVisitorId;

/*
|--------------------------------------------------------------------------
| دریافت اطلاعات بازدیدکننده
|--------------------------------------------------------------------------
*/

try {
    $visitor = getVisitorById($visitor_id);
} catch (Throwable $e) {
    error_log(
        "Roma message reply visitor lookup error: "
        . $e->getMessage()
    );

    http_response_code(500);
    exit("در حال حاضر امکان بارگذاری گفتگو وجود ندارد.");
}

if (!$visitor) {
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| ایجاد توکن امنیتی فرم
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['admin_message_reply_csrf'])
    || !is_string($_SESSION['admin_message_reply_csrf'])
    || $_SESSION['admin_message_reply_csrf'] === ''
) {
    $_SESSION['admin_message_reply_csrf'] = bin2hex(
        random_bytes(32)
    );
}

$error = "";
$formMessage = "";
$messages = [];

/*
|--------------------------------------------------------------------------
| ارسال پاسخ مدیریت
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['admin_message_reply_csrf'] ?? '';
    $rawMessage = $_POST['message'] ?? '';

    /*
     * بررسی توکن CSRF
     */
    if (
        !is_string($postedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $postedToken)
    ) {
        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } elseif (!is_string($rawMessage)) {

        $error = "متن پاسخ معتبر نیست.";

    } else {

        $formMessage = trim($rawMessage);

        if ($formMessage === '') {

            $error = "لطفاً متن پاسخ را وارد کنید.";

        } else {

            try {

                /*
                 * بررسی مجدد وجود بازدیدکننده پیش از ارسال پاسخ
                 */
                $currentVisitor = getVisitorById($visitor_id);

                if (!$currentVisitor) {

                    $error = "بازدیدکننده موردنظر پیدا نشد.";

                } else {

                    /*
                     * ثبت پاسخ مدیریت
                     */
                    $stmt = $conn->prepare(
                        "INSERT INTO messages
                            (visitor_id, sender, message, is_read)
                         VALUES (?, 'admin', ?, 0)"
                    );

                    $sent = $stmt->execute([
                        $visitor_id,
                        $formMessage
                    ]);

                    if ($sent) {

                        /*
                         * نوسازی توکن پس از ثبت موفق پاسخ
                         */
                        $_SESSION['admin_message_reply_csrf'] =
                            bin2hex(random_bytes(32));

                        /*
                         * جلوگیری از ارسال دوباره با Refresh
                         */
                        header(
                            'Location: reply.php?visitor_id='
                            . $visitor_id
                        );
                        exit;

                    } else {

                        $error = "پاسخ ارسال نشد. لطفاً دوباره تلاش کنید.";
                    }
                }

            } catch (Throwable $e) {

                error_log(
                    "Roma admin message reply error: "
                    . $e->getMessage()
                );

                $error = "هنگام ارسال پاسخ خطایی رخ داد. لطفاً دوباره تلاش کنید.";
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| علامت‌گذاری پیام‌های بازدیدکننده به‌عنوان خوانده‌شده
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->prepare(
        "UPDATE messages
         SET is_read = 1
         WHERE visitor_id = ?
           AND sender = 'visitor'
           AND is_read = 0"
    );

    $stmt->execute([$visitor_id]);

} catch (Throwable $e) {

    error_log(
        "Roma message read status error: " . $e->getMessage()
    );

    if ($error === '') {
        $error = "تغییر وضعیت خوانده‌شدن پیام‌ها انجام نشد.";
    }
}

/*
|--------------------------------------------------------------------------
| دریافت تاریخچه گفتگو
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->prepare(
        "SELECT *
         FROM messages
         WHERE visitor_id = ?
         ORDER BY created_at ASC, id ASC"
    );

    $stmt->execute([$visitor_id]);

    $messages = $stmt->fetchAll();

} catch (Throwable $e) {

    error_log(
        "Roma message history error: " . $e->getMessage()
    );

    $messages = [];

    if ($error === '') {
        $error = "بارگذاری تاریخچه گفتگو با خطا مواجه شد.";
    }
}

$panelMessageReplyCss = true;

?>

<?php include "../layouts/head.php"; ?>
<?php include "../layouts/navigation.php"; ?>
<?php include "../layouts/header.php"; ?>

<main class="main-content roma-message-reply">

    <div class="r-reply-container">

        <!-- عنوان صفحه -->
        <div class="r-reply-heading">

            <div class="r-reply-heading-icon">
                <span aria-hidden="true">✉</span>
            </div>

            <div class="r-reply-heading-text">
                <h1>گفت‌وگو با بازدیدکننده</h1>
                <p>
                    پیام‌ها را بررسی کنید و پاسخ خود را برای بازدیدکننده ارسال کنید.
                </p>
            </div>

            <a href="index.php" class="r-reply-back-link">
                <span aria-hidden="true">←</span>
                بازگشت به پیام‌ها
            </a>

        </div>

        <!-- اطلاعات بازدیدکننده -->
        <section class="r-reply-person-card">

            <div class="r-reply-person-avatar">

                <?php
                echo htmlspecialchars(
                    mb_substr(
                        $visitor->name ?? '',
                        0,
                        1,
                        'UTF-8'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

            <div class="r-reply-person-info">

                <span class="r-reply-person-label">
                    در حال گفت‌وگو با
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $visitor->name ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </strong>

                <span class="r-reply-person-email" dir="ltr">
                    <?php
                    echo htmlspecialchars(
                        $visitor->email ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </span>

            </div>

            <div class="r-reply-person-status">
                <span class="r-reply-online-dot"></span>
                گفت‌وگوی کاربر
            </div>

        </section>

        <!-- محتوای گفتگو -->
        <section class="r-reply-chat-card">

            <div class="r-reply-chat-header">

                <div>
                    <h2>تاریخچه پیام‌ها</h2>
                    <p>پیام‌ها به ترتیب زمان ارسال نمایش داده می‌شوند.</p>
                </div>

                <span class="r-reply-message-count">
                    <?php echo count($messages); ?> پیام
                </span>

            </div>

            <!-- فهرست پیام‌ها -->
            <div class="r-reply-chat-body">

                <?php if (!empty($messages)) { ?>

                    <?php foreach ($messages as $item) { ?>

                        <?php if ($item->sender === 'visitor') { ?>

                            <!-- پیام بازدیدکننده -->
                            <div class="r-chat-row r-chat-row-visitor">

                                <div class="r-chat-message-wrap">

                                    <div class="r-chat-sender">
                                        <?php
                                        echo htmlspecialchars(
                                            $visitor->name ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>
                                    </div>

                                    <div class="r-chat-bubble r-chat-bubble-visitor">

                                        <div class="r-chat-message-text">
                                            <?php
                                            echo nl2br(
                                                htmlspecialchars(
                                                    $item->message ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )
                                            );
                                            ?>
                                        </div>

                                        <div class="r-chat-time">
                                            <?php
                                            echo htmlspecialchars(
                                                $item->created_at ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php } elseif ($item->sender === 'admin') { ?>

                            <!-- پاسخ مدیریت -->
                            <div class="r-chat-row r-chat-row-admin">

                                <div class="r-chat-message-wrap">

                                    <div class="r-chat-sender r-chat-sender-admin">
                                        مدیریت روما
                                    </div>

                                    <div class="r-chat-bubble r-chat-bubble-admin">

                                        <div class="r-chat-message-text">
                                            <?php
                                            echo nl2br(
                                                htmlspecialchars(
                                                    $item->message ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                )
                                            );
                                            ?>
                                        </div>

                                        <div class="r-chat-time">
                                            <?php
                                            echo htmlspecialchars(
                                                $item->created_at ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php } ?>

                    <?php } ?>

                <?php } else { ?>

                    <div class="r-reply-empty">

                        <div class="r-reply-empty-icon">✉</div>

                        <h3>هنوز پیامی در این گفتگو وجود ندارد</h3>

                        <p>
                            می‌توانید اولین پیام را برای این بازدیدکننده ارسال کنید.
                        </p>

                    </div>

                <?php } ?>

            </div>

            <!-- فرم ارسال پاسخ -->
            <div class="r-reply-form-section">

                <div class="r-reply-form-heading">
                    <div>
                        <h2>ارسال پاسخ</h2>
                        <p>متن پاسخ خود را در کادر زیر بنویسید.</p>
                    </div>
                </div>

                <?php if ($error !== '') { ?>

                    <div class="r-reply-alert" role="alert">

                        <span class="r-reply-alert-icon">!</span>

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

                <form
                    method="POST"
                    action="reply.php?visitor_id=<?php echo $visitor_id; ?>"
                    class="r-reply-form"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                        echo htmlspecialchars(
                            $_SESSION['admin_message_reply_csrf'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                    >

                    <div class="r-reply-textarea-wrap">

                        <label for="reply-message">
                            متن پاسخ
                        </label>

                        <textarea
                            name="message"
                            id="reply-message"
                            class="r-reply-textarea"
                            rows="5"
                            placeholder="پاسخ خود را برای بازدیدکننده بنویسید..."
                            required
                        ><?php
                        echo htmlspecialchars(
                            $formMessage,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?></textarea>

                    </div>

                    <div class="r-reply-form-footer">

                        <span class="r-reply-form-hint">
                            لطفاً پیش از ارسال، متن پاسخ را بررسی کنید.
                        </span>

                        <div class="r-reply-form-actions">

                            <a
                                href="index.php"
                                class="r-reply-cancel-btn"
                            >
                                بازگشت
                            </a>

                            <button
                                type="submit"
                                class="r-reply-submit-btn"
                            >
                                <span aria-hidden="true">➤</span>
                                ارسال پاسخ
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </section>

    </div>

</main>

<?php include "../layouts/footer.php"; ?>