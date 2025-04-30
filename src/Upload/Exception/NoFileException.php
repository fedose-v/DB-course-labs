<?php

namespace App\Upload\Exception;

class NoFileException extends \Exception
{
    private const EXCEPTION_MESSAGE = 'The file was not sent.';
    public function __construct()
    {
        parent::__construct(self::EXCEPTION_MESSAGE);
    }
}