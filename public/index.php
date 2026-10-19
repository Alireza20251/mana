<?php
declare(strict_types=1);

use Mana\Core\Application;
use Mana\Core\Response;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$app = new Application(BASE_PATH);

$app->router()->get('/', static function () {
    return Response::html(
        '<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مانا | خرید آسان، انتخاب هوشمندانه</title>
</head>
<body>
    <main>
        <h1>مانا</h1>
        <p>خرید آسان، انتخاب هوشمندانه</p>
    </main>
</body>
</html>'
    );
});

$app->run();
