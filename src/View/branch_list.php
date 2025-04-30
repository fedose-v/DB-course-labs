<?php
/**
 * @var array $data
 * @var \App\Model\Data\BranchSummary[] $branches
 */

$branches = $data['branches'];
$breadcrumbsType = \App\Model\BreadcrumbsType::HOME_PAGE_TYPE;
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Филиалы</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/organization_list.css">
    <link rel="stylesheet" href="/css/navigation_bar.css">
    <link rel="stylesheet" href="/css/breadcrumbs.css">
</head>
<body>
    <?php require(__DIR__ . '/navigation_bar.php'); ?>
    <?php require(__DIR__ . '/breadcrumbs.php'); ?>
    <div class="create-container">
        <a href="/branch/new" style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
            <p class="create-button">Создать</p>
        </a>
    </div>
    <?php foreach ($branches as $branch): ?>
        <div class="container">
            <a href="/branch?branch_id=<?= $branch->getId() ?>">
                <div class="container-main-info">
                    <p class="metadata"><?= htmlentities($branch->getFullAddress()) ?></p>
                    <p class="metadata">Работников: <?= htmlentities($branch->getEmployeesNumber()) ?></p>
                </div>
            </a>
            <div>
                <a href="/branch/delete?branch_id=<?= $branch->getId() ?>"><p class="delete-button">Удалить</p></a>
                <a href="/branch/edit?branch_id=<?= $branch->getId() ?>"><p class="edit-button">Редактирвоать</p></a>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>
