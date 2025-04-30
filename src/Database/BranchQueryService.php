<?php
declare(strict_types=1);

namespace App\Database;

use App\Model\Branch;
use App\Model\Data\BranchSummary;

readonly class BranchQueryService
{
    public function __construct(
        private \PDO $connection
    ) {
    }

    public function find(int $id): ?BranchSummary
    {
        $query = <<<SQL
            SELECT
              b.id AS id,
              city,
              address,
              COUNT(e.id) AS employees_number
            FROM branch b
              LEFT JOIN employee e ON e.branch_id = b.id AND e.is_deleted = 0
            WHERE b.is_deleted = 0
              AND b.id = $id
            GROUP BY b.id
            SQL;

        $statement = $this->connection->query($query);
        if ($row = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            return $this->createBranchDataFromRow($row);
        }
        return null;
    }

    public function add(Branch $branch): int
    {
        $query = <<<SQL
            INSERT INTO branch
                (address, city)
            VALUES 
                (:address, :city)
            SQL;
        $statement = $this->connection->prepare($query);
        try
        {
            $statement->execute([
                ':address' => $branch->getAddress(),
                ':city' => $branch->getCity(),
            ]);
        }
        catch (\PDOException $err)
        {
            throw new \PDOException("Database Error: The subsidiary could not be able added: {$err->getMessage()}");
        }
        catch (\Exception $err)
        {
            throw new \Exception("General Error: The subsidiary could not be able added: {$err->getMessage()}");
        }

        return (int) $this->connection->lastInsertId();
    }

    public function save(Branch $branch): int
    {
        $query = <<<SQL
            UPDATE branch
            SET 
                city = :city,
                address = :address
            WHERE id = :id
            SQL;
        $statement = $this->connection->prepare($query);
        try
        {
            $statement->execute([
                ':id' => $branch->getId(),
                ':city' => $branch->getCity(),
                ':address' => $branch->getAddress(),
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
            UPDATE branch
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

    /** @return BranchSummary[] */
    public function getBranchList(): array
    {
        $branchList = [];
        $query = <<<SQL
            SELECT
              b.id AS id,
              city,
              address,
              COUNT(e.id) AS employees_number
            FROM branch b
              LEFT JOIN employee e ON e.branch_id = b.id AND e.is_deleted = 0
            WHERE b.is_deleted = 0
            GROUP BY b.id
            ORDER BY employees_number DESC
            SQL;
        $statement = $this->connection->query($query);
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row)
        {
            $branchList[] = $this->createBranchDataFromRow($row);
        }
        return $branchList;
    }

    private function createBranchDataFromRow(array $row): BranchSummary
    {
        return new BranchSummary(
            (int) $row[BranchSummary::FIELD_ID],
            $row[BranchSummary::FIELD_CITY],
            $row[BranchSummary::FIELD_ADDRESS],
            (int) $row[BranchSummary::FIELD_EMPLOYEES_NUMBER]
        );
    }
}