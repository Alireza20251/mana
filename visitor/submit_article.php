<?php

session_start();

require_once "../functions/DB.php";
require_once "../functions/helpers.php";
require_once "../functions/categories.php";
require_once "../functions/visitor_users.php";

/*
|--------------------------------------------------------------------------
| بررسی نشست بازدیدکننده
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["visitor_id"])
    || !filter_var(
        $_SESSION["visitor_id"],
        FILTER_VALIDATE_INT
    )
    || (int) $_SESSION["visitor_id"] < 1
) {
    header("Location: ../auth/visitor_login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| بررسی وجود حساب بازدیدکننده
|--------------------------------------------------------------------------
*/

$visitor_id = (int) $_SESSION["visitor_id"];
$visitor = getVisitorById($visitor_id);

if (!$visitor) {

    unset(
        $_SESSION["visitor_id"],
        $_SESSION["visitor_name"],
        $_SESSION["visitor_email"]
    );

    header("Location: ../auth/visitor_login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| مقادیر اولیه صفحه
|--------------------------------------------------------------------------
*/

$error = "";
$message = "";

$title = "";
$body = "";
$category_id = "";

/*
|--------------------------------------------------------------------------
| دریافت پیام موفقیت پس از انتقال مسیر
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["visitor_submit_article_flash"])) {

    $message = (string) $_SESSION["visitor_submit_article_flash"];

    unset($_SESSION["visitor_submit_article_flash"]);
}

/*
|--------------------------------------------------------------------------
| ایجاد توکن امنیتی فرم
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["visitor_submit_article_csrf"])
    || !is_string($_SESSION["visitor_submit_article_csrf"])
) {
    $_SESSION["visitor_submit_article_csrf"] = bin2hex(
        random_bytes(32)
    );
}

/*
|--------------------------------------------------------------------------
| دریافت دسته‌بندی‌ها
|--------------------------------------------------------------------------
*/

$categories = getAllCategories();

/*
|--------------------------------------------------------------------------
| پردازش ارسال مقاله
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $rawTitle = $_POST["title"] ?? "";
    $rawBody = $_POST["body"] ?? "";
    $rawCategoryId = $_POST["category_id"] ?? "";
    $postedToken = $_POST["csrf_token"] ?? null;

    /*
     * ورودی‌ها باید رشته باشند.
     */
    if (
        !is_string($rawTitle)
        || !is_string($rawBody)
        || !is_string($rawCategoryId)
    ) {

        $error = "اطلاعات واردشده معتبر نیست.";

    } else {

        $title = trim($rawTitle);
        $body = trim($rawBody);
        $category_id = trim($rawCategoryId);

        /*
        |--------------------------------------------------------------------------
        | بررسی توکن CSRF
        |--------------------------------------------------------------------------
        */

        if (
            !is_string($postedToken)
            || !hash_equals(
                $_SESSION["visitor_submit_article_csrf"],
                $postedToken
            )
        ) {

            $error = "درخواست معتبر نیست. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.";

        } elseif (
            $title === ""
            || $body === ""
            || $category_id === ""
        ) {

            $error = "لطفاً تمام فیلدها را پر کنید.";

        } elseif (
            !ctype_digit($category_id)
            || (int) $category_id < 1
        ) {

            $error = "لطفاً یک دسته‌بندی معتبر انتخاب کنید.";

        } elseif (mb_strlen($title, "UTF-8") > 255) {

            $error = "عنوان مقاله نباید بیشتر از ۲۵۵ کاراکتر باشد.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | بررسی وجود دسته‌بندی
            |--------------------------------------------------------------------------
            */

            $category = getCategoryById((int) $category_id);

            if (!$category) {

                $error = "دسته‌بندی انتخاب‌شده وجود ندارد.";

            } else {

                global $conn;

                try {

                    /*
                    |--------------------------------------------------------------------------
                    | ثبت مقاله بازدیدکننده
                    |--------------------------------------------------------------------------
                    | مقاله ابتدا به‌صورت پیش‌نویس ثبت می‌شود.
                    | user_id خالی است و visitor_id مالک مقاله است.
                    | تصویر برای این فرم الزامی نیست.
                    |--------------------------------------------------------------------------
                    */

                    $sql = "
                        INSERT INTO articles
                        (
                            title,
                            body,
                            status,
                            image,
                            user_id,
                            visitor_id,
                            category_id,
                            created_at
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                    ";

                    $stmt = $conn->prepare($sql);

                    $success = $stmt->execute([
                        $title,
                        $body,
                        "draft",
                        "",
                        null,
                        $visitor_id,
                        (int) $category_id
                    ]);

                    if ($success) {

                        /*
                         * جلوگیری از ارسال دوباره مقاله هنگام Refresh
                         */
                        $_SESSION["visitor_submit_article_flash"] =
                            "مقاله شما با موفقیت ارسال شد و پس از بررسی مدیریت منتشر خواهد شد.";

                        unset(
                            $_SESSION["visitor_submit_article_csrf"]
                        );

                        header("Location: submit_article.php");
                        exit;

                    } else {

                        $error = "ارسال مقاله انجام نشد. لطفاً دوباره تلاش کنید.";
                    }

                } catch (Throwable $e) {

                    error_log(
                        "Roma visitor article submission error: "
                        . $e->getMessage()
                    );

                    $error = "هنگام ارسال مقاله خطایی رخ داد. لطفاً دوباره تلاش کنید.";
                }
            }
        }
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

    <title>ارسال مقاله | روما</title>

    <meta
        name="description"
        content="مقاله و محتوای خود را برای بررسی و انتشار در مجله روما ارسال کنید."
    >

    <meta
        name="robots"
        content="noindex, nofollow"
    >

    <link
        href="<?php echo asset('assets/Css/Main.css'); ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/Menu-demo1.css'); ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/Style.css'); ?>"
        rel="stylesheet"
    >

    <link
        href="<?php echo asset('assets/Css/submit-article.css'); ?>"
        rel="stylesheet"
    >

</head>

<body class="rtl roma-submit-page">

<div class="roma-submit-wrapper">

    <div class="roma-submit-card">

        <div class="roma-submit-header">

            <span class="roma-submit-badge">
                مجله روما
            </span>

            <h1>
                ارسال مقاله
            </h1>

            <p>
                سلام
                <?php
                echo htmlspecialchars(
                    $visitor->name,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
                👋
            </p>

            <small>
                مقاله شما پس از بررسی مدیریت منتشر خواهد شد.
            </small>

        </div>


        <?php if ($error !== "") { ?>

            <div
                class="roma-submit-message roma-submit-error"
                role="alert"
            >

                <?php
                echo htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php } ?>


        <?php if ($message !== "") { ?>

            <div
                class="roma-submit-message roma-submit-success"
                role="status"
            >

                <?php
                echo htmlspecialchars(
                    $message,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php } ?>


        <form
            method="post"
            action="submit_article.php"
            class="roma-submit-form"
        >

            <!-- توکن امنیتی فرم -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?php
                echo htmlspecialchars(
                    $_SESSION["visitor_submit_article_csrf"],
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>"
            >


            <div class="roma-submit-group">

                <label for="title">
                    عنوان مقاله
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="255"
                    value="<?php
                    echo htmlspecialchars(
                        $title,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>"
                    placeholder="عنوان مقاله را وارد کنید"
                    required
                >

            </div>


            <div class="roma-submit-group">

                <label for="category_id">
                    دسته‌بندی
                </label>

                <select
                    id="category_id"
                    name="category_id"
                    required
                >

                    <option
                        value=""
                        disabled
                        <?php echo $category_id === ""
                            ? "selected"
                            : ""; ?>
                    >
                        انتخاب دسته‌بندی
                    </option>

                    <?php foreach ($categories as $category) { ?>

                        <option
                            value="<?php echo (int) $category->id; ?>"
                            <?php echo $category_id === (string) $category->id
                                ? "selected"
                                : ""; ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $category->title,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <div class="roma-submit-group">

                <label for="body">
                    متن مقاله
                </label>

                <textarea
                    id="body"
                    name="body"
                    rows="12"
                    placeholder="متن مقاله خود را اینجا بنویسید..."
                    required
                ><?php
                echo htmlspecialchars(
                    $body,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?></textarea>

            </div>


            <button
                type="submit"
                class="roma-submit-button"
            >
                ارسال مقاله
            </button>

        </form>


        <div class="roma-submit-back">

            <a href="../contact.php">
                <span>→</span>
                بازگشت به تماس با ما
            </a>

        </div>


    </div>

</div>

</body>

</html>