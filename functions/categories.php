<?php

require_once "DB.php";
require_once "helpers.php";

function createCategory($title)
{
    global $conn;

    $sql = "INSERT INTO categories SET title = ?, created_at = NOW()";
    $stmt = $conn->prepare($sql);

    return $stmt->execute([$title]);
}

function getCategoryById($id)
{
    global $conn;

    $sql = "SELECT * FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch();
}

function getAllCategories()
{
    global $conn;

    $sql = "SELECT * FROM categories";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}

function updateCategory($id, $title)
{
    global $conn;

    $sql = "UPDATE categories SET title = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);

    return $stmt->execute([$title, $id]);
}

function deleteCategoryById($id)
{
    global $conn;

    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);

    return $stmt->execute([$id]);
}