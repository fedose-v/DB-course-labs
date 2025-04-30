<?php
/**
 * @var int $breadcrumbsType
 * @var \App\Model\Data\BranchSummary $branch
 * @var \App\Model\Data\BranchSummary $employeeBranch
 * @var \App\Model\Data\EmployeeSummary $employee
 */

use App\Model\BreadcrumbsType;
?>

<div class="breadcrumbs-block">
    <div class="breadcrumbs-content">
        <?php switch ($breadcrumbsType):
         case BreadcrumbsType::HOME_PAGE_TYPE: ?>
        <span>Home</span>
        <?php break ?>

        <?php case BreadcrumbsType::BRANCH_PAGE_TYPE: ?>
            <a href="/">Home</a>
            <span> > </span>
            <span><?= htmlentities($branch->getFullAddress()) ?></span>
        <?php break ?>

        <?php case BreadcrumbsType::CREATE_BRANCH_PAGE_TYPE: ?>
            <a href="/">Home</a>
            <span> > </span>
            <span>Create Branch</span>
        <?php break ?>

        <?php case BreadcrumbsType::EDIT_BRANCH_PAGE_TYPE: ?>
            <a href="/">Home</a>
            <span> > </span>
            <a href="/branch?branch_id=<?= $branch->getId() ?>"><?= htmlentities($branch->getFullAddress()) ?></a>
            <span> > </span>
            <span>Edit Branch</span>
        <?php break ?>

        <?php case BreadcrumbsType::CREATE_EMPLOYEE_PAGE_TYPE: ?>
            <a href="/">Home</a>
            <span> > </span>
            <a href="/branch?branch_id=<?= $employeeBranch->getId() ?>"><?= htmlentities($employeeBranch->getFullAddress()) ?></a>
            <span> > </span>
            <span>Create Employee</span>
        <?php break ?>

        <?php case BreadcrumbsType::EDIT_EMPLOYEE_PAGE_TYPE: ?>
            <a href="/">Home</a>
            <span> > </span>
            <a href="/branch?branch_id=<?= $employeeBranch->getId() ?>"><?= htmlentities($employeeBranch->getFullAddress()) ?></a>
            <span> > </span>
            <span><?= htmlentities($employee->getFullName()) ?></span>
            <span> > </span>
            <span>Edit Employee</span>
        <?php break ?>

        <?php endswitch; ?>
    </div>
</div>
