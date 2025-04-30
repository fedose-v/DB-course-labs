<?php
declare(strict_types=1);

use App\Controller\OrganizationAppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$app = OrganizationAppFactory::createApp();
$app->run();
