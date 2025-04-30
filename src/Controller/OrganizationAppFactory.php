<?php
declare(strict_types=1);

namespace App\Controller;

use Slim\App;
use Slim\Factory\AppFactory;

class OrganizationAppFactory
{
    public static function createApp(): App
    {
        $isProduction = getenv('APP_ENV') === 'prod';
        $app = AppFactory::create();

        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(!$isProduction, true, true);
        self::setRoutes($app);

        return $app;
    }

    private static function setRoutes(App &$app): void
    {
        $controller = new OrganizationApiController();
        $app->get('/', $controller->index(...));
        $app->get('/branches', $controller->showAllBranches(...));
        $app->get('/branch', $controller->showEmployeesOfBranch(...));
        $app->get('/branch/new', $controller->showAddBranchForm(...));
        $app->get('/branch/edit', $controller->showEditBranchForm(...));
        $app->get('/branch/employee/new', $controller->showAddEmployeeForm(...));
        $app->get('/branch/employee/edit', $controller->showEditEmployeeForm(...));
        $app->get('/branch/delete', $controller->deleteBranch(...));
        $app->get('/branch/employee/delete', $controller->deleteEmployee(...));

        $app->post('/branch/add', $controller->addBranch(...));
        $app->post('/branch/edit', $controller->editBranch(...));
        $app->post('/branch/employee/add', $controller->addEmployee(...));
        $app->post('/branch/employee/edit', $controller->editEmployee(...));
    }
}