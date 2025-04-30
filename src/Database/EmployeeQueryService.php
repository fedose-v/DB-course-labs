<?php
declare(strict_types=1);

namespace App\Database;

use App\Model\Employee;
use App\Model\Data\EmployeeSummary;

readonly class EmployeeQueryService
{
    private const MYSQL_DATETIME_FORMAT = 'Y-m-d';

    public function __construct(
        private \PDO $connection
    ) {
    }

    public function find(int $id): ?EmployeeSummary
    {
        $query = <<<SQL
            SELECT 
                id,
                branch_id,
                first_name,
                last_name,
                middle_name,
                job_title,
                phone_number,
                email,
                gender,
                birth_date,
                hire_date,
                description,
                avatar_path
            FROM employee
            WHERE id = $id
                AND is_deleted = 0
            SQL;
        $statement = $this->connection->query($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            return $this->createEmployeeSummaryFromRow($row);
        }
        return null;
    }

    /**
     * @throws \Exception
     */
    public function add(Employee $employee): int
    {
        $query = <<<SQL
            INSERT INTO employee 
                (branch_id, first_name, last_name, middle_name, job_title, phone_number, email, gender, birth_date, hire_date, description)
            VALUES 
                (:branch_id, :first_name, :last_name, :middle_name, :job_title, :phone_number, :email, :gender, :birth_date, :hire_date, :description)
            SQL;
        $statement = $this->connection->prepare($query);
        try
        {
            $statement->execute([
                ':branch_id' => $employee->getBranchId(),
                ':first_name' => $employee->getFirstName(),
                ':last_name' => $employee->getLastName(),
                ':middle_name' => $employee->getMiddleName(),
                ':job_title' => $employee->getJobTitle(),
                ':phone_number' => $employee->getPhoneNumber(),
                ':email' => $employee->getEmail(),
                ':gender' => $employee->getGender(),
                ':birth_date' => $employee->getBirthDate()->format(self::MYSQL_DATETIME_FORMAT),
                ':hire_date' => $employee->getHireDate()->format(self::MYSQL_DATETIME_FORMAT),
                ':description' => $employee->getDescription(),
            ]);
        }
        catch (\PDOException $err)
        {
            throw new \PDOException("Database Error: The employee could not be able added: {$err->getMessage()}");
        }
        catch (\Exception $err)
        {
            throw new \Exception("General Error: The employee could not be able added: {$err->getMessage()}");
        }

        return (int) $this->connection->lastInsertId();
    }

    /**
     * @throws \Exception
     */
    public function save(Employee $employee): int
    {
        $query = <<<SQL
            UPDATE employee
            SET 
              branch_id = :branch_id,
              first_name = :first_name,
              last_name = :last_name,
              middle_name = :middle_name,
              job_title = :job_title,
              phone_number = :phone_number,
              email = :email,
              gender = :gender,
              birth_date = :birth_date,
              hire_date = :hire_date,
              description = :description
            WHERE id = :id
            SQL;
        $statement = $this->connection->prepare($query);
        try
        {
            $statement->execute([
                ':id' => $employee->getId(),
                ':branch_id' => $employee->getBranchId(),
                ':first_name' => $employee->getFirstName(),
                ':last_name' => $employee->getLastName(),
                ':middle_name' => $employee->getMiddleName(),
                ':job_title' => $employee->getJobTitle(),
                ':phone_number' => $employee->getPhoneNumber(),
                ':email' => $employee->getEmail(),
                ':gender' => $employee->getGender(),
                ':birth_date' => $employee->getBirthDate()->format(self::MYSQL_DATETIME_FORMAT),
                ':hire_date' => $employee->getHireDate()->format(self::MYSQL_DATETIME_FORMAT),
                ':description' => $employee->getDescription(),
            ]);
        }
        catch (\PDOException $err)
        {
            throw new \PDOException("Database Error: The employee could not be able added: {$err->getMessage()}");
        }
        catch (\Exception $err)
        {
            throw new \Exception("General Error: The employee could not be able added: {$err->getMessage()}");
        }

        return (int) $this->connection->lastInsertId();
    }

    public function delete(int $id): void
    {
        $query = <<<SQL
            UPDATE employee
            SET
              is_deleted = 1
            WHERE id = $id
            SQL;
        try
        {
            $this->connection->exec($query);
        }
        catch (\PDOException $err)
        {
            throw new \PDOException("Database Error: The employee could not be able able delete: {$err->getMessage()}");
        }
    }

    /** @return EmployeeSummary[] */
    public function getEmployeeListByBranchId(int $branchId): array
    {
        $employeeList = [];
        $query = <<<SQL
            SELECT 
                id,
                branch_id,
                first_name,
                last_name,
                middle_name,
                job_title,
                phone_number,
                email,
                gender,
                birth_date,
                hire_date,
                description,
                avatar_path
            FROM employee
            WHERE branch_id = $branchId
                AND is_deleted = 0
            SQL;
        $statement = $this->connection->query($query);
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row)
        {
            $employeeList[] = $this->createEmployeeSummaryFromRow($row);
        }
        return $employeeList;
    }

    /**
     * @throws \Exception
     */
    public function updateAvatarPath(?string $path, int $employeeId): void
    {
        $query = <<<SQL
            UPDATE employee
            SET avatar_path = :avatar_path
            WHERE id = :user_id
        SQL;

        $statement = $this->connection->prepare($query);
        try
        {
            $statement->execute([
                ':avatar_path' => $path,
                ':user_id' => $employeeId,
            ]);
        }
        catch (\PDOException $err)
        {
            throw new \PDOException("Database Error: The employee's avatar could not be able added: {$err->getMessage()}");
        }
        catch (\Exception $err)
        {
            throw new \Exception("General Error: The employee's avatar could not be able added: {$err->getMessage()}");
        }
    }

    private function createEmployeeSummaryFromRow(array $row): EmployeeSummary
    {
        return new EmployeeSummary(
            (int) $row[EmployeeSummary::FIELD_ID],
            (int) $row[EmployeeSummary::FIELD_BRANCH_ID],
            $row[EmployeeSummary::FIELD_FIRST_NAME],
            $row[EmployeeSummary::FIELD_LAST_NAME],
            $row[EmployeeSummary::FIELD_MIDDLE_NAME],
            $row[EmployeeSummary::FIELD_JOB_TITLE],
            $row[EmployeeSummary::FIELD_PHONE_NUMBER],
            $row[EmployeeSummary::FIELD_EMAIL],
            $row[EmployeeSummary::FIELD_GENDER],
            $this->parseDateTime($row[EmployeeSummary::FIELD_BIRTH_DATE]),
            $this->parseDateTime($row[EmployeeSummary::FIELD_HIRE_DATE]),
            $row[EmployeeSummary::FIELD_DESCRIPTION],
            $row[EmployeeSummary::FIELD_AVATAR_PATH],
        );
    }

    private function parseDateTime(string $value): \DateTimeImmutable
    {
        $result = \DateTimeImmutable::createFromFormat(self::MYSQL_DATETIME_FORMAT, $value);
        if (!$result)
        {
            throw new \InvalidArgumentException("Invalid datetime value '$value'");
        }
        return $result;
    }
}