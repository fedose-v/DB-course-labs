<?php
declare(strict_types=1);

namespace App\Model;

readonly class Employee
{
    public const DATE_FORMAT = 'Y-m-d';
    public const FIELD_ID = 'id';
    public const FIELD_BRANCH_ID = 'branch_id';
    public const FIELD_FIRST_NAME = 'first_name';
    public const FIELD_LAST_NAME = 'last_name';
    public const FIELD_MIDDLE_NAME = 'middle_name';
    public const FIELD_JOB_TITLE = 'job_title';
    public const FIELD_PHONE_NUMBER = 'phone_number';
    public const FIELD_EMAIL = 'email';
    public const FIELD_GENDER = 'gender';
    public const FIELD_BIRTH_DATE = 'birth_date';
    public const FIELD_HIRE_DATE = 'hire_date';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_AVATAR_PATH = 'avatar_path';

    public function __construct(
        private ?int $id,
        private int $branchId,
        private string $firstName,
        private string $lastName,
        private ?string $middleName,
        private string $jobTitle,
        private ?string $phoneNumber,
        private ?string $email,
        private string $gender,
        private \DateTimeImmutable $birthDate,
        private \DateTimeImmutable $hireDate,
        private ?string $description,
        private ?string $avatarPath
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBranchId(): int
    {
        return $this->branchId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getJobTitle(): string
    {
        return $this->jobTitle;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function getHireDate(): \DateTimeImmutable
    {
        return $this->hireDate;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }
}