<?php
$breadcrumbsType = \App\Model\BreadcrumbsType::CREATE_BRANCH_PAGE_TYPE;
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Форма создания филиала</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/branch_form.css">
    <link rel="stylesheet" href="/css/navigation_bar.css">
    <link rel="stylesheet" href="/css/breadcrumbs.css">
</head>
<body>
    <?php require(__DIR__ . '/navigation_bar.php'); ?>
    <?php require(__DIR__ . '/breadcrumbs.php'); ?>
    <div class="form-container">
        <form class="form" action="/branch/add" method="POST" enctype="multipart/form-data">
            <div class="form-field">
                <label for="city">Город</label>
                <input type="text" name="city" id="city" maxlength="100" required />
            </div>
            <div class="form-field">
                <label for="address">Адрес</label>
                <input type="text" name="address" id="address" maxlength="100" required />
            </div>
            <div class="form-field form-field-full-width">
                <button type="submit">Создать</button>
            </div>
        </form>
    </div>
</body>