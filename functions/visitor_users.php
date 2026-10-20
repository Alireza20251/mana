<?php

require_once "DB.php";

/*
|--------------------------------------------------------------------------
| دریافت اطلاعات یک بازدیدکننده
|--------------------------------------------------------------------------
*/

function getVisitorById($id)
{
    global $conn;

    $sql = "
        SELECT id, name, email, created_at
        FROM visitor_users
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch();
}

/*
|--------------------------------------------------------------------------
| دریافت فهرست بازدیدکنندگان ثبت‌نام‌شده
|--------------------------------------------------------------------------
*/

function getAllVisitors()
{
    global $conn;

    $sql = "
        SELECT id, name, email, created_at
        FROM visitor_users
        ORDER BY id DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}