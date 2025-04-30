<?php
declare(strict_types=1);

namespace App\Controller\Request;

use App\Model\Branch;
use App\Model\Employee;

class OrganizationApiRequestParser
{
    public static function parseBranchParams(array $parameters): Branch
    {
        return new Branch(
            self::parseIntegerOrReturnNull($parameters, Branch::FIELD_ID),
            self::parseString($parameters, Branch::FIELD_CITY),
            self::parseString($parameters, Branch::FIELD_ADDRESS)
        );
    }

    public static function parseEmployeeParams(array $parameters): Employee
    {
        return new Employee(
            self::parseIntegerOrReturnNull($parameters, Employee::FIELD_ID),
            self::parseInteger($parameters, Employee::FIELD_BRANCH_ID),
            self::parseString($parameters, Employee::FIELD_FIRST_NAME),
            self::parseString($parameters, Employee::FIELD_LAST_NAME),
            self::parseStringOrReturnNull($parameters, Employee::FIELD_MIDDLE_NAME),
            self::parseString($parameters, Employee::FIELD_JOB_TITLE),
            self::parseStringOrReturnNull($parameters, Employee::FIELD_PHONE_NUMBER),
            self::parseStringOrReturnNull($parameters, Employee::FIELD_EMAIL),
            self::parseString($parameters, Employee::FIELD_GENDER),
            self::parseDate($parameters, Employee::FIELD_BIRTH_DATE),
            self::parseDate($parameters, Employee::FIELD_HIRE_DATE),
            self::parseStringOrReturnNull($parameters, Employee::FIELD_DESCRIPTION),
            null
        );
    }

    public static function parseInteger(array $requestParams, string $name): int
    {
        $value = $requestParams[$name] ?? null;
        if (!self::isIntegerValue($value))
        {
            throw new \RuntimeException('Invalid integer value');
        }
        return (int) $value;
    }

    public static function parseString(array $parameters, string $name, ?int $maxLength = null): string
    {
        $value = $parameters[$name] ?? null;
        if (!is_string($value))
        {
            throw new \RuntimeException('Invalid string value');
        }
        if ($maxLength !== null && mb_strlen($value) > $maxLength)
        {
            throw new \RuntimeException("String value too long (exceeds $maxLength characters)");
        }
        return $value;
    }

    public static function parseIntegerOrReturnNull(array $requestParams, string $name): ?int
    {
        $value = $requestParams[$name] ?? null;
        if (!self::isIntegerValue($value))
        {
            return null;
        }
        return (int) $value;
    }

    public static function parseStringOrReturnNull(array $parameters, string $name, ?int $maxLength = null): ?string
    {
        $value = $parameters[$name] ?? null;
        if (!is_string($value))
        {
            return null;
        }
        if ($maxLength !== null && mb_strlen($value) > $maxLength)
        {
            throw new \RuntimeException("String value too long (exceeds $maxLength characters)");
        }
        return $value;
    }

    public static function parseDate(array $parameters, string $name): \DateTimeImmutable
    {
        try
        {
            $date = new \DateTimeImmutable(self::parseString($parameters, $name));
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException('Invalid date value');
        }
        return $date;
    }

    private static function isIntegerValue(mixed $value): bool
    {
        return is_numeric($value) && (is_int($value) || ctype_digit($value));
    }
}