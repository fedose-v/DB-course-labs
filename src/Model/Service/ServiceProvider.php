<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Database\BranchQueryService;
use App\Database\ConnectionProvider;
use App\Database\EmployeeQueryService;

final class ServiceProvider
{
    private ?BranchQueryService $branchQueryService = null;
    private ?EmployeeQueryService $employeeQueryService = null;

    public static function getInstance(): self
    {
        static $instance = null;
        if ($instance === null)
        {
            $instance = new self();
        }
        return $instance;
    }

    public function getBranchQueryService(): BranchQueryService
    {
        if ($this->branchQueryService === null)
        {
            $this->branchQueryService = new BranchQueryService(ConnectionProvider::getConnection());
        }
        return $this->branchQueryService;
    }

    public function getEmployeeQueryService(): EmployeeQueryService
    {
        if ($this->employeeQueryService === null)
        {
            $this->employeeQueryService = new EmployeeQueryService(ConnectionProvider::getConnection());
        }
        return $this->employeeQueryService;
    }
}