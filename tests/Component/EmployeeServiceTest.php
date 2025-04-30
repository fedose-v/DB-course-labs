<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Database\BranchQueryService;
use App\Database\ConnectionProvider;
use App\Database\EmployeeQueryService;
use App\Model\Branch;
use App\Model\Data\EmployeeSummary;
use App\Model\Employee;
use App\Tests\Common\AbstractDatabaseTestCase;

class EmployeeServiceTest extends AbstractDatabaseTestCase
{
    private EmployeeQueryService $employeeService;
    private BranchQueryService $branchService;

    protected function setUp(): void
    {
        parent::setUp();
        $connection = ConnectionProvider::getConnection();
        $this->employeeService = new EmployeeQueryService($connection);
        $this->branchService = new BranchQueryService($connection);
    }

    public function testEmployeeMethods(): void
    {
        $firstBranchId = $this->createTestBranch('Москва', 'ул.Арбат 89');
        $secondBranchId = $this->createTestBranch('Йошкар-Ола', 'ул.Эшкинина 8Б');
        $employeeId = $this->createTestEmployee(
            $firstBranchId,
            'Алексей',
            'Швед',
            null,
            'Директор',
            '777777',
            'leha.shved@example.com',
            'M',
            '1987-01-01',
            '2005-01-01',
            '',
            null
        );

        $employeeData = $this->employeeService->find($employeeId);
        $this->assertEmployeeFields(
            $employeeData,
            $firstBranchId,
            'Алексей',
            'Швед',
            null,
            'Директор',
            '777777',
            'leha.shved@example.com',
            'M',
            '1987-01-01',
            '2005-01-01',
            '',
            null
        );
        $this->assertBranchHasEmployee($firstBranchId);
        $this->assertBranchHasNoEmployees($secondBranchId);

        $this->employeeService->save(new Employee($employeeId,
                $secondBranchId,
                'Антон',
                'Чехов',
                'Павлович',
                'Писатель',
                '123123',
                'anton.chekov@example.com',
                'M',
                new \DateTimeImmutable('17-01-1860'),
                new \DateTimeImmutable('17-01-1920'),
                'Писарь',
                'avatar20.jpg'
            )
        );

        $employeeList = $this->employeeService->getEmployeeListByBranchId($secondBranchId);
        $this->assertCount(1, $employeeList);
        $this->assertEmployeeFields(
            $employeeList[0],
            $secondBranchId,
            'Антон',
            'Чехов',
            'Павлович',
            'Писатель',
            '123123',
            'anton.chekov@example.com',
            'M',
            '1860-01-17',
            '1920-01-17',
            'Писарь',
            null
        );
        $this->assertBranchHasEmployee($secondBranchId);
        $this->assertBranchHasNoEmployees($firstBranchId);

        $this->employeeService->updateAvatarPath('avatar20.jpg', $employeeId);
        $employeeData = $this->employeeService->find($employeeId);
        $this->assertEmployeeFields(
            $employeeData,
            $secondBranchId,
            'Антон',
            'Чехов',
            'Павлович',
            'Писатель',
            '123123',
            'anton.chekov@example.com',
            'M',
            '1860-01-17',
            '1920-01-17',
            'Писарь',
            'avatar20.jpg'
        );

        $this->employeeService->delete($employeeId);
        $this->assertNull($this->employeeService->find($employeeId));
        $this->assertBranchHasNoEmployees($firstBranchId);
        $this->assertBranchHasNoEmployees($secondBranchId);
    }

    private function createTestBranch(string $city, string $address): int
    {
        return $this->branchService->add(new Branch(null, $city, $address));
    }

    private function assertBranchHasEmployee(int $branchId): void
    {
        $this->assertEquals(1, $this->branchService->find($branchId)->getEmployeesNumber(), 'one_employee');
    }

    private function assertBranchHasNoEmployees(int $branchId): void
    {
        $this->assertEquals(0, $this->branchService->find($branchId)->getEmployeesNumber(), 'no_employees');
    }

    private function assertEmployeeFields(
        EmployeeSummary $employeeData,
        int $branchId,
        string $firstName,
        string $lastName,
        ?string $middleName,
        string $jobTitle,
        ?string $phoneNumber,
        ?string $email,
        string $gender,
        string $birthDate,
        string $hireDate,
        ?string $description,
        ?string $avatarPath
    ): void {
        $this->assertEquals($employeeData->getBranchId(), $branchId, 'branch_id');
        $this->assertEquals($employeeData->getFirstName(), $firstName, 'firstname');
        $this->assertEquals($employeeData->getLastName(), $lastName, 'lastname');
        $this->assertEquals($employeeData->getMiddleName(), $middleName, 'middlename');
        $this->assertEquals($employeeData->getJobTitle(), $jobTitle, 'job_title');
        $this->assertEquals($employeeData->getPhoneNumber(), $phoneNumber, 'phone_number');
        $this->assertEquals($employeeData->getEmail(), $email, 'email');
        $this->assertEquals($employeeData->getGender(), $gender, 'gender');
        $this->assertEquals($employeeData->getBirthDate()->format(EmployeeSummary::DATE_FORMAT), $birthDate, 'birth_date');
        $this->assertEquals($employeeData->getHireDate()->format(EmployeeSummary::DATE_FORMAT), $hireDate, 'hire_date');
        $this->assertEquals($employeeData->getDescription(), $description, 'description');
        $this->assertEquals($employeeData->getAvatarPath(), $avatarPath, 'avatar_path');
    }

    private function createTestEmployee(
        int $branchId,
        string $firstName,
        string $lastName,
        ?string $middleName,
        string $jobTitle,
        ?string $phoneNumber,
        ?string $email,
        string $gender,
        string $birthDate,
        string $hireDate,
        ?string $description,
        ?string $avatarPath
    ): int {
        return $this->employeeService->add(new Employee(
                null,
                $branchId,
                $firstName,
                $lastName,
                $middleName,
                $jobTitle,
                $phoneNumber,
                $email,
                $gender,
                new \DateTimeImmutable($birthDate),
                new \DateTimeImmutable($hireDate),
                $description,
                $avatarPath
            )
        );
    }
}