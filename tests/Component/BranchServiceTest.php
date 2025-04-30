<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Database\BranchQueryService;
use App\Model\Branch;
use App\Model\Data\BranchSummary;
use App\Tests\Common\AbstractDatabaseTestCase;

class BranchServiceTest extends AbstractDatabaseTestCase
{
    private BranchQueryService $branchService;

    protected function setUp(): void
    {
        parent::setUp();
        $connection = $this->getConnection();
        $this->branchService = new BranchQueryService($connection);
    }

    public function testBranchMethods(): void
    {
        $branch = $this->createTestBranch(null, 'Москва', 'ул.Арбат 89');

        $branchId = $this->branchService->add($branch);
        $branchData = $this->branchService->find($branchId);
        $this->assertBranchData($branchData, $branchId, 'Москва', 'ул.Арбат 89');

        $this->branchService->save($this->createTestBranch($branchId, 'Йошкар-Ола', 'ул.Эшкинина 8Б'));
        $branchData = $this->branchService->find($branchId);
        $this->assertBranchData($branchData, $branchId, 'Йошкар-Ола', 'ул.Эшкинина 8Б');

        $branchList = $this->branchService->getBranchList();
        $this->assertCount(1, $branchList);
        $this->assertBranchData($branchList[0], $branchId, 'Йошкар-Ола', 'ул.Эшкинина 8Б');

        $this->branchService->delete($branchId);
        $branchData = $this->branchService->find($branchId);
        $this->assertNull($branchData);
    }

    private function assertBranchData(BranchSummary $branchData, int $id, string $city, string $address): void
    {
        $this->assertEquals($branchData->getId(), $id, 'id');
        $this->assertEquals($branchData->getCity(), $city, 'city');
        $this->assertEquals($branchData->getAddress(), $address, 'address');
        $this->assertEquals(0, $branchData->getEmployeesNumber(), 'employeesNumber');
    }

    private function createTestBranch(?int $id, string $city, string $address): Branch
    {
        return new Branch($id, $city, $address);
    }
}