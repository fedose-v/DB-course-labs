<?php
/**
 * @var \App\Model\Data\BranchSummary[] $branches
 * @var \App\Model\Data\BranchSummary $employeeBranch
 */

$breadcrumbsType = \App\Model\BreadcrumbsType::CREATE_EMPLOYEE_PAGE_TYPE;
?>

<!DOCTYPE html>
<html lang="">
<head>
    <title>Форма создания работника</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/employee_form.css">
    <link rel="stylesheet" href="/css/navigation_bar.css">
    <link rel="stylesheet" href="/css/breadcrumbs.css">
</head>
<body>
    <?php require(__DIR__ . '/navigation_bar.php'); ?>
    <?php require(__DIR__ . '/breadcrumbs.php'); ?>
    <div class="form-container">
        <form class="form" action="/add_employee.php" method="POST" enctype="multipart/form-data">
            <div class="form-main-part">
                <div style="width: 350px;">
                    <div class="form-field">
                        <label for="name">Имя</label>
                        <input type="text" name="first_name" id="name" maxlength="100" required />
                    </div>
                    <div class="form-field">
                        <label for="last_name">Фамилия</label>
                        <input type="text" name="last_name" id="last_name" maxlength="100" required />
                    </div>
                    <div class="form-field">
                        <label for="middle_name">Отчество</label>
                        <input type="text" name="middle_name" id="middle_name" maxlength="100" placeholder="Опционально" />
                    </div>
                    <div class="form-field">
                        <label for="birth_date">Дата рождения</label>
                        <input type="date" class="form-field-date" name="birth_date" id="birth_date" required />
                    </div>
                </div>
                <div>
                    <label for="avatar_path">
                        <img src="/img/default_employee_photo.png" id="avatar_preview" style="width: 150px; height: 150px"/>
                    </label>
                    <input type="file" id="avatar_path" name="avatar_path" accept="image/png, image/jpg, image/jpeg" style="display: none">
                </div>
            </div>
            <div class="form-field">
                <label for="gender">Пол</label>
                <select id="gender" name="gender" required>
                    <option value="M">Мужской</option>
                    <option value="W">Женский</option>
                </select>
            </div>
            <div class="form-field">
                <label for="branch">Адрес филиала</label>
                <select id="branch" name="branch_id" required>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch->getId() ?> <?= $employeeBranch->getId() == $branch->getId() ? 'selected' : '' ?>"><?= $branch->getFullAddress() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="job_title">Должность</label>
                <input type="text" name="job_title" id="job_title" maxlength="100" required />
            </div>
            <div class="form-field">
                <label for="hire_date">Дата найма</label>
                <input type="date" class="form-field-date" name="hire_date" id="hire_date" required />
            </div>
            <div class="form-field">
                <label for="phone_number">Телефон</label>
                <input type="text" name="phone_number" id="phone_number" maxlength="30" />
            </div>
            <div class="form-field">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" maxlength="100" />
            </div>
            <div class="form-field">
                <label for="description">Описание</label>
                <textarea name="description" id="description" maxlength="300"></textarea>
            </div>
            <div class="form-field form-field-full-width">
                <button type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</body>
<script type="application/javascript" src="/js/employee_form.js"></script>
</html>