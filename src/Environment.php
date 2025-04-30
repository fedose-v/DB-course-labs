<?php
declare(strict_types=1);

namespace App;

class Environment
{
    private const URL_HOME_PAGE = '/';
    private const HTTP_STATUS_303_SEE_OTHER = 303;
    private const CONFIG_DIR_NAME = 'config';
    private const PUBLIC_DIR_NAME = 'public';
    private const UPLOADS_DIR_NAME = 'uploads';
    private const APP_DIR_NAME = 'src';
    private const VIEWS_DIR_NAME = 'View';

    public static function joinPath(string ...$components): string
    {
        return implode(DIRECTORY_SEPARATOR, array_filter($components));
    }

    public static function getConfigPath(string $configFileName): string
    {
        return self::joinPath(self::getProjectRootPath(), self::CONFIG_DIR_NAME, $configFileName);
    }

    public static function getUploadsPath(string $uploadsFileName): string
    {
        return self::joinPath(self::getProjectRootPath(), self::PUBLIC_DIR_NAME, self::UPLOADS_DIR_NAME, $uploadsFileName);
    }

    public static function getViewPath(): string
    {
        return self::joinPath(self::getProjectRootPath(), self::APP_DIR_NAME, self::VIEWS_DIR_NAME);
    }

    public static function writeRedirectSeeOther(string $url): void
    {
        header('Location: ' . $url, true, self::HTTP_STATUS_303_SEE_OTHER);
    }

    public static function writeRedirectHomePage(): void
    {
        self::writeRedirectSeeOther(self::URL_HOME_PAGE);
    }

    private static function getProjectRootPath(): string
    {
        return dirname(__DIR__, 1);
    }
}