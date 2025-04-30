<?php
declare(strict_types=1);

namespace App\Upload;

use App\Environment;
use App\Upload\Exception\NoFileException;

readonly class UploadFiles
{
    private const IMAGE_MAX_WIDTH = 800;
    private const IMAGE_MAX_HEIGHT = 800;
    private const IMAGE_MIN_WIDTH = 200;
    private const IMAGE_MIN_HEIGHT = 200;

    public static function checkAvatarType(string $fileName): ?string
    {
        try
        {
            self::validateUploadedFileError($fileName);
            self::validateUploadedFileSize($fileName);

            $ext = self::getExtToUploadedFile($fileName);
            if (!$ext)
            {
                throw new \RuntimeException('Invalid file format.');
            }

            return sha1_file($_FILES[$fileName]['tmp_name']) . '.' . $ext;
        }
        catch (NoFileException $e)
        {
            return null;
        }
    }

    public static function uploadAvatar(string $fileName, string $avatarName): void
    {
        if (!move_uploaded_file($_FILES[$fileName]['tmp_name'], Environment::getUploadsPath($avatarName)))
        {
            throw new \RuntimeException('Failed to move uploaded file.');
        }
    }

    public static function transformAvatarPath(string $avatarPath, int $id): ?string
    {
        return 'avatar' . $id . '.' . explode('.', $avatarPath)[1];
    }

    private static function getExtToUploadedFile(string $fileName): ?string
    {
        $finfo = new \finfo();
        return array_search(
            $finfo->file($_FILES[$fileName]['tmp_name'], FILEINFO_MIME_TYPE),
            ['jpg' => 'image/jpeg', 'png' => 'image/png',],
            true
        );
    }

    /**
     * @throws NoFileException
     */
    private static function validateUploadedFileError(string $fileName): void
    {
        if (!isset($_FILES[$fileName]['error']) || is_array($_FILES[$fileName]['error']))
        {
            throw new \RuntimeException('Invalid parameters.');
        }

        switch ($_FILES[$fileName]['error'])
        {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new NoFileException();
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException('Exceeded filesize limit.');
            default:
                throw new \RuntimeException('Unknown errors.');
        }
    }

    private static function validateUploadedFileSize(string $fileName): void
    {
        [$width, $height] = getimagesize($_FILES[$fileName]['tmp_name']);
        if (self::IMAGE_MIN_WIDTH >= $width || $width >= self::IMAGE_MAX_WIDTH
            || self::IMAGE_MIN_HEIGHT >= $height || $height >= self::IMAGE_MAX_HEIGHT)
        {
            throw new \RuntimeException('The file is not the right size. Use a file no smaller than 200*200 and no larger than 800*800');
        }
    }
}