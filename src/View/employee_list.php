<?php
/**
 * @var \App\Model\Data\BranchSummary $branch
 * @var \App\Model\Data\EmployeeSummary[] $employees
 */

$breadcrumbsType = \App\Model\BreadcrumbsType::BRANCH_PAGE_TYPE;
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Работники</title>
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
        <a href="/branch/employee/new?branch_id=<?=$branch->getId()?>" style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
                <p class="create-button">Создать</p>
        </a>
    </div>
    <?php foreach ($employees as $employee): ?>
        <div class="container">
            <a href="/branch/employee/edit?employee_id=<?= $employee->getId() ?>">
                <div class="container-main-info">
                    <p class="metadata"><?= htmlentities($employee->getFullName()) ?></p>
                    <p class="metadata"><?= htmlentities($employee->getJobTitle()) ?></p>
                </div>
            </a>
            <div class="remove-container">
                <a href="/delete_employee.php?employee_id=<?= $employee->getId() ?>"><p class="delete-button">Удалить</p></a>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>