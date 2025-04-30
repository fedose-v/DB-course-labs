<?php
/**
 * @var \App\Model\Data\BranchSummary $branch
 */

$breadcrumbsType = \App\Model\BreadcrumbsType::EDIT_BRANCH_PAGE_TYPE;
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Форма редактирования филиала</title>
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
        <form class="form" action="/save_branch.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="id" required value="<?= $branch->getId() ?>" style="display: none;" />
            <div class="form-field">
                <label for="city">Город</label>
                <input type="text" name="city" id="city" maxlength="100" required value="<?= $branch->getCity() ?>" />
            </div>
            <div class="form-field">
                <label for="address">Адрес</label>
                <input type="text" name="address" id="address" maxlength="100" required value="<?= $branch->getAddress() ?>" />
            </div>
            <div class="form-field form-field-full-width">
                <button type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</body>
</html>