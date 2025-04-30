<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Request\OrganizationApiRequestParser;
use App\Database\ConnectionProvider;
use App\Model\Employee;
use App\Model\Service\ServiceProvider;
use App\Environment;
use App\Upload\UploadFiles;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

final readonly class OrganizationApiController
{
    private const HTTP_STATUS_OK = 200;
    private const HTTP_STATUS_BAD_REQUEST = 400;
    private const HTTP_SEE_OTHER = 302;

    public function showAllBranches(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $branches = ServiceProvider::getInstance()->getBranchQueryService()->getBranchList();
        return $this->success($response, 'branch_list.php', ['branches' => $branches]);
    }

    public function showEmployeesOfBranch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $branchId = OrganizationApiRequestParser::parseInteger($request->getQueryParams(), 'branch_id');
        $branch = ServiceProvider::getInstance()->getBranchQueryService()->find($branchId);
        if (!$branch)
        {
            throw new \RuntimeException("Fail to find branch");
        }
        $employees = ServiceProvider::getInstance()->getEmployeeQueryService()->getEmployeeListByBranchId($branchId);

        return $this->success($response, 'employee_list.php', ['branch' => $branch, 'employees' => $employees]);
    }

    public function showEditBranchForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $branch = ServiceProvider::getInstance()->getBranchQueryService()->find(OrganizationApiRequestParser::parseInteger($request->getQueryParams(), 'branch_id'));
        if (!$branch)
        {
            throw new \RuntimeException("Fail to find branch");
        }
        return $this->success($response, 'edit_branch_form.php', ['branch' => $branch]);
    }

    public function showEditEmployeeForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $employee = ServiceProvider::getInstance()->getEmployeeQueryService()
            ->find(OrganizationApiRequestParser::parseInteger($request->getQueryParams(), 'employee_id'));
        if (!$employee)
        {
            throw new \RuntimeException("Fail to find employee");
        }
        $branches = ServiceProvider::getInstance()->getBranchQueryService()->getBranchList();
        $employeeBranch = ServiceProvider::getInstance()->getBranchQueryService()->find($employee->getBranchId());

        return $this->success($response, 'edit_employee_form.php', ['employee' => $employee, 'branches' => $branches, 'employeeBranch' => $employeeBranch]);
    }

    public function addBranch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $branch = OrganizationApiRequestParser::parseBranchParams($request->getParsedBody());
            ServiceProvider::getInstance()->getBranchQueryService()->add($branch);
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException("Fail to add branch: " . $e->getMessage());
        }

        return $this->redirect($response, '/');
    }

    public function addEmployee(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $avatarName = UploadFiles::checkAvatarType(Employee::FIELD_AVATAR_PATH);
            $employee = OrganizationApiRequestParser::parseEmployeeParams($request->getParsedBody());
            $employeeId = ServiceProvider::getInstance()->getEmployeeQueryService()->add($employee);
            if ($avatarName)
            {
                $avatarName = UploadFiles::transformAvatarPath($avatarName, $employeeId);
                UploadFiles::uploadAvatar(Employee::FIELD_AVATAR_PATH, $avatarName);
                ServiceProvider::getInstance()->getEmployeeQueryService()->updateAvatarPath($avatarName, $employeeId);
            }
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException("Fail to add employee: " . $e->getMessage());
        }

        return $this->redirect($response, "/branch?branch_id={$employee->getBranchId()}");
    }

    public function editBranch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $branch = OrganizationApiRequestParser::parseBranchParams($request->getParsedBody());
            ServiceProvider::getInstance()->getBranchQueryService()->save($branch);
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException("Fail to edit branch: " . $e->getMessage());
        }

        return $this->redirect($response, '/branches');
    }

    public function editEmployee(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        ConnectionProvider::getConnection()->beginTransaction();
        try
        {
            $avatarName = UploadFiles::checkAvatarType(Employee::FIELD_AVATAR_PATH);
            $employee = OrganizationApiRequestParser::parseEmployeeParams($request->getParsedBody());
            ServiceProvider::getInstance()->getEmployeeQueryService()->save($employee);
            if ($avatarName)
            {
                $avatarName = UploadFiles::transformAvatarPath($avatarName, $employee->getId());
                UploadFiles::uploadAvatar(Employee::FIELD_AVATAR_PATH, $avatarName);
                ServiceProvider::getInstance()->getEmployeeQueryService()->updateAvatarPath($avatarName, $employee->getId());
            }
            ConnectionProvider::getConnection()->commit();
        }
        catch (\Exception $e)
        {
            ConnectionProvider::getConnection()->rollBack();
            throw new \RuntimeException("Fail to edit employee: " . $e->getMessage());
        }

        return $this->redirect($response, "/branch?branch_id={$employee->getBranchId()}");
    }

    public function deleteBranch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $branchId = OrganizationApiRequestParser::parseInteger($request->getQueryParams(), 'branch_id');
        $branch = ServiceProvider::getInstance()->getBranchQueryService()->find($branchId);
        if (!$branch)
        {
            throw new \RuntimeException("Fail to delete branch");
        }

        ServiceProvider::getInstance()->getBranchQueryService()->delete($branchId);
        return $this->redirect($response, '/branches');
    }

    public function deleteEmployee(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $employeeId = OrganizationApiRequestParser::parseInteger($request->getQueryParams(), 'employee_id');
        $employee = ServiceProvider::getInstance()->getEmployeeQueryService()->find($employeeId);
        if (!$employee)
        {
            throw new \RuntimeException("Fail to delete employee");
        }

        ServiceProvider::getInstance()->getEmployeeQueryService()->delete($employeeId);
        return $this->redirect($response, "/branch?branch_id={$employee->getBranchId()}");
    }

    public function showAddBranchForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->success($response, 'add_branch_form.php');
    }

    public function showAddEmployeeForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $branchId = OrganizationApiRequestParser::parseInteger($request->getQueryParams(), 'branch_id');
        $employeeBranch = ServiceProvider::getInstance()->getBranchQueryService()->find($branchId);
        if (!$employeeBranch)
        {
            throw new \RuntimeException("Fail to find employee");
        }
        $branches = ServiceProvider::getInstance()->getBranchQueryService()->getBranchList();
        return $this->success($response, 'add_employee_form.php', ['branches' => $branches, 'employeeBranch' => $employeeBranch]);
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->redirect($response, '/branches');
    }

    private function redirect(ResponseInterface $response, string $url): ResponseInterface
    {
        return $response->withHeader('Location', $url)->withStatus(self::HTTP_SEE_OTHER);
    }

    private function success(ResponseInterface $response, string $templateName, array $responseData = []): ResponseInterface
    {
        try
        {
            $renderer = new PhpRenderer(Environment::getViewPath());
            return $renderer->render($response, $templateName, $responseData)->withStatus(self::HTTP_STATUS_OK);
        }
        catch (\Throwable $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function badRequest(ResponseInterface $response, array $errors): ResponseInterface
    {
        $responseData = ['errors' => $errors];
        return $this->withJson($response, $responseData)->withStatus(self::HTTP_STATUS_BAD_REQUEST);
    }

    private function withJson(ResponseInterface $response, array $responseData): ResponseInterface
    {
        try
        {
            $responseBytes = json_encode($responseData, JSON_THROW_ON_ERROR);
            $response->getBody()->write($responseBytes);
//            return $response;
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (\JsonException $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}