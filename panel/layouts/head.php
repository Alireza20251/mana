<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>پنل مدیریت روما</title>

    <!-- آیکون سایت -->
    <link rel="shortcut icon"
          href="<?php echo asset('panel/assets/media/image/favicon.png'); ?>">

    <meta name="theme-color" content="#22c4c1">

    <!-- فایل‌های اصلی قالب مدیریت -->
    <link rel="stylesheet"
          href="<?php echo asset('panel/vendors/bundle.css'); ?>"
          type="text/css">

    <link rel="stylesheet"
          href="<?php echo asset('panel/vendors/slick/slick.css'); ?>">

    <link rel="stylesheet"
          href="<?php echo asset('panel/vendors/slick/slick-theme.css'); ?>">

    <link rel="stylesheet"
          href="<?php echo asset('panel/assets/css/app.css'); ?>"
          type="text/css">

    <!-- استایل عمومی منوی پنل -->
    <link rel="stylesheet"
          href="<?php echo asset('assets/Css/panel-navigation.css'); ?>"
          type="text/css">

    <!-- استایل عمومی هدر پنل -->
    <link rel="stylesheet"
          href="<?php echo asset('assets/Css/panel-header.css'); ?>"
          type="text/css">

    <!-- استایل عمومی فوتر پنل -->
    <link rel="stylesheet"
          href="<?php echo asset('assets/Css/panel-footer.css'); ?>"
          type="text/css">

    <!-- داشبورد -->
    <?php if (!empty($dashboardPageCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-dashboard.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- فهرست مقالات -->
    <?php if (!empty($panelArticlesCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-articles.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- افزودن مقاله -->
    <?php if (!empty($panelArticleCreateCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-article-create.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- ویرایش مقاله -->
    <?php if (!empty($panelArticleEditCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-article-edit.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- حذف مقاله -->
    <?php if (!empty($panelArticleDeleteCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-article-delete.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- فهرست دسته‌بندی‌ها -->
    <?php if (!empty($panelCategoriesCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-categories.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- افزودن دسته‌بندی -->
    <?php if (!empty($panelCategoryCreateCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-category-create.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- ویرایش دسته‌بندی -->
    <?php if (!empty($panelCategoryEditCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-category-edit.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- حذف دسته‌بندی -->
    <?php if (!empty($panelCategoryDeleteCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-category-delete.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- فهرست ادمین‌ها -->
    <?php if (!empty($panelUsersCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-users.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- افزودن ادمین -->
    <?php if (!empty($panelUserCreateCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-user-create.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- ویرایش ادمین -->
    <?php if (!empty($panelUserEditCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-user-edit.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- حذف ادمین -->
    <?php if (!empty($panelUserDeleteCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-user-delete.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- فهرست بازدیدکنندگان -->
    <?php if (!empty($panelVisitorsCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-visitors.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- فهرست پیام‌ها -->
    <?php if (!empty($panelMessagesCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-messages.css'); ?>"
              type="text/css">
    <?php } ?>

    <!-- گفت‌وگو و پاسخ مدیر -->
    <?php if (!empty($panelMessageReplyCss)) { ?>
        <link rel="stylesheet"
              href="<?php echo asset('assets/Css/panel-message-reply.css'); ?>"
              type="text/css">
    <?php } ?>

</head>

<body class="small-navigation">