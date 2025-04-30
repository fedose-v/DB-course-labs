<?php
declare(strict_types=1);

namespace App\Model\Data;

readonly class BranchSummary
{
    public const FIELD_ID = 'id';
    public const FIELD_CITY = 'city';
    public const FIELD_ADDRESS = 'address';
    public const FIELD_EMPLOYEES_NUMBER = 'employees_number';
    private const ADDRESS_SEPARATOR = ', ';

    public function __construct(
        private ?int $id,
        private string $city,
        private string $address,
        private int $employeesNumber = 0
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getEmployeesNumber(): ?int
    {
        return $this->employeesNumber;
    }

    public function getFullAddress(): string
    {
        return $this->city . self::ADDRESS_SEPARATOR . $this->address;
    }
}