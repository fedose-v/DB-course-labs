<?php
declare(strict_types=1);

namespace App\Model;

readonly class Branch
{
    public const FIELD_ID = 'id';
    public const FIELD_CITY = 'city';
    public const FIELD_ADDRESS = 'address';

    public function __construct(
        private ?int $id,
        private string $city,
        private string $address,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}