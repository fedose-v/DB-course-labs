<?php
declare(strict_types=1);

namespace App\Tests\Common;

use App\Controller\OrganizationAppFactory;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;

class AbstractFunctionalTestCase extends AbstractDatabaseTestCase
{
    private App $slimApp;
    private UriFactory $uriFactory;
    private ServerRequestFactory $serverRequestFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->slimApp = OrganizationAppFactory::createApp();
        $this->uriFactory = new UriFactory();
        $this->serverRequestFactory = new ServerRequestFactory();
    }

    protected function sendGetRequest(string $urlPath, array $queryParams): ResponseInterface
    {
        $urlString = $urlPath . '?' . http_build_query($queryParams);
        return $this->doRequest('GET', $urlString);
    }

    protected function sendPostRequest(string $urlPath, array $requestParams): ResponseInterface
    {
        return $this->doRequest('POST', $urlPath, $requestParams);
    }

    private function doRequest(string $method, string $url, array $body = []): ResponseInterface
    {
        $uri = $this->uriFactory->createUri($url);

        $request = $this->serverRequestFactory
            ->createServerRequest($method, $uri)
            ->withParsedBody($body);

        return $this->slimApp->handle($request);
    }
}