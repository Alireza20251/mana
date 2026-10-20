<?php

session_start();

require_once "../functions/DB.php";
require_once "../functions/helpers.php";
require_once "../functions/visitor_users.php";

/*
|--------------------------------------------------------------------------
| بررسی نشست بازدیدکننده
|--------------------------------------------------------------------------
*/

$rawVisitorId = $_SESSION['visitor_id'] ?? null;

$visitorId = (
    is_string($rawVisitorId) || is_int($rawVisitorId)
)
    ? filter_var($rawVisitorId, FILTER_VALIDATE_INT)
    : false;

if ($visitorId === false || $visitorId === null || $visitorId < 1) {

    unset(
        $_SESSION['visitor_id'],
        $_SESSION['visitor_name'],
        $_SESSION['visitor_email']
    );

    header("Location: ../auth/visitor_login.php");
    exit;
}

$visitorId = (int) $visitorId;

/*
|--------------------------------------------------------------------------
| بررسی وجود حساب بازدیدکننده
|--------------------------------------------------------------------------
*/

try {
    $visitor = getVisitorById($visitorId);
} catch (Throwable $e) {
    error_log(
        "Roma visitor messages user lookup error: " . $e->getMessage()
    );

    http_response_code(500);
    exit("در حال حاضر امکان بارگذاری پیام‌ها وجود ندارد.");
}

if (!$visitor) {

    unset(
        $_SESSION['visitor_id'],
        $_SESSION['visitor_name'],
        $_SESSION['visitor_email']
    );

    header("Location: ../auth/visitor_login.php");
    exit;
}

$error = "";
$messageText = "";
$messages = [];

/*
|--------------------------------------------------------------------------
| ایجاد توکن امنیتی فرم
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['visitor_messages_csrf'])
    || !is_string($_SESSION['visitor_messages_csrf'])
    || $_SESSION['visitor_messages_csrf'] === ''
) {
    $_SESSION['visitor_messages_csrf'] = bin2hex(
        random_bytes(32)
    );
}

/*
|--------------------------------------------------------------------------
| ارسال پیام
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submittedToken = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['visitor_messages_csrf'] ?? '';

    $rawMessage = $_POST['message'] ?? '';

    $messageText = is_string($rawMessage)
        ? trim($rawMessage)
        : '';

    /* بررسی توکن CSRF */
    if (
        !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)
    ) {
        $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

    } elseif (!is_string($rawMessage)) {

        $error = "متن پیام معتبر نیست.";

    } elseif ($messageText === '') {

        $error = "لطفاً متن پیام را وارد کنید.";

    } else {

        try {

            $stmt = $conn->prepare(
                "INSERT INTO messages
                    (visitor_id, sender, message, is_read)
                 VALUES (?, 'visitor', ?, 0)"
            );

            $sent = $stmt->execute([
                $visitorId,
                $messageText
            ]);

            if ($sent) {

                /* جلوگیری از استفاده مجدد از توکن */
                $_SESSION['visitor_messages_csrf'] = bin2hex(
                    random_bytes(32)
                );

                header("Location: messages.php");
                exit;

            } else {

                $error = "پیام ارسال نشد. لطفاً دوباره تلاش کنید.";
            }

        } catch (Throwable $e) {

            error_log(
                "Roma visitor message sending error: " . $e->getMessage()
            );

            $error = "هنگام ارسال پیام خطایی رخ داد. لطفاً دوباره تلاش کنید.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| خوانده‌شده کردن پاسخ‌های مدیر
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->prepare(
        "UPDATE messages
         SET is_read = 1
         WHERE visitor_id = ?
           AND sender = 'admin'
           AND is_read = 0"
    );

    $stmt->execute([$visitorId]);

    /*
    |--------------------------------------------------------------------------
    | دریافت فقط پیام‌های همین بازدیدکننده
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare(
        "SELECT *
         FROM messages
         WHERE visitor_id = ?
         ORDER BY created_at ASC"
    );

    $stmt->execute([$visitorId]);

    $messages = $stmt->fetchAll();

} catch (Throwable $e) {

    error_log(
        "Roma visitor messages loading error: " . $e->getMessage()
    );

    $messages = [];

    if ($error === '') {
        $error = "بارگذاری پیام‌ها با خطا مواجه شد. لطفاً صفحه را دوباره باز کنید.";
    }
}

?>

<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>پیام‌های من | روما</title>

    <meta
        name="description"
        content="ارتباط مستقیم کاربران با مدیریت مجله روما."
    >

    <meta
        name="robots"
        content="noindex, nofollow"
    >

    <link rel="stylesheet" href="../assets/Css/Main.css">
    <link rel="stylesheet" href="../assets/Css/Menu-demo1.css">
    <link rel="stylesheet" href="../assets/Css/Style.css">
    <link rel="stylesheet" href="../assets/Css/visitor-messages.css">

</head>

<body class="rtl roma-messages-page">

<div class="roma-chat-page">

    <!-- لینک بازگشت -->
    <div class="roma-chat-back">

        <a href="../index.php">
            <span aria-hidden="true">←</span>
            بازگشت به صفحه اصلی
        </a>

    </div>

    <!-- باکس اصلی چت -->
    <div class="roma-chat-box">

        <!-- هدر چت -->
        <div class="roma-chat-header">

            <div>

                <h1>
                    پشتیبانی روما
                </h1>

                <div class="roma-chat-online">

                    <span></span>
                    مدیریت روما

                </div>

            </div>

            <div class="roma-chat-total">

                <?php echo count($messages); ?>

                پیام

            </div>

        </div>

        <!-- قسمت پیام‌ها -->
        <div class="roma-chat-body">

            <?php if (!empty($messages)) { ?>

                <div class="roma-chat-messages">

                    <?php foreach ($messages as $item) { ?>

                        <?php if ($item->sender === "visitor") { ?>

                            <!-- پیام کاربر -->
                            <div class="roma-message-row visitor">

                                <div class="roma-message">

                                    <div class="roma-message-sender">
                                        شما
                                    </div>

                                    <div class="roma-message-text">

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

                                    <div class="roma-message-date">

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

                        <?php } else { ?>

                            <!-- پیام مدیریت -->
                            <div class="roma-message-row admin">

                                <div class="roma-message">

                                    <div class="roma-message-sender">
                                        مدیریت روما
                                    </div>

                                    <div class="roma-message-text">

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

                                    <div class="roma-message-date">

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

                        <?php } ?>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <!-- حالت بدون پیام -->
                <div class="roma-chat-empty">

                    <div class="roma-empty-symbol">
                        💬
                    </div>

                    <h2>
                        هنوز پیامی ارسال نشده
                    </h2>

                    <p>
                        اولین پیام خود را برای مدیریت روما ارسال کنید.
                    </p>

                </div>

            <?php } ?>

        </div>

        <!-- قسمت ارسال -->
        <div class="roma-chat-compose">

            <?php if ($error !== '') { ?>

                <div class="roma-chat-error" role="alert">

                    <?php
                    echo htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>

                </div>

            <?php } ?>

            <form
                method="POST"
                action="messages.php"
                class="roma-chat-form"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php
                    echo htmlspecialchars(
                        $_SESSION['visitor_messages_csrf'],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                >

                <textarea
                    name="message"
                    id="message"
                    placeholder="پیامتان را بنویسید..."
                    rows="1"
                    required
                ><?php
                echo htmlspecialchars(
                    $messageText,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?></textarea>

                <button
                    type="submit"
                    title="ارسال پیام"
                >
                    <span aria-hidden="true">➤</span>
                </button>

            </form>

            <div class="roma-chat-hint">
                پیام شما برای مدیریت روما ارسال می‌شود.
            </div>

        </div>

    </div>

</div>

</body>
</html>