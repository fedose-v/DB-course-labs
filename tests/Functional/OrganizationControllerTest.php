<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Common\AbstractFunctionalTestCase;
use Psr\Http\Message\ResponseInterface;

class OrganizationControllerTest extends AbstractFunctionalTestCase
{
    public function testControllerMethods(): void
    {
        $branchId = $this->doCreateBranch();
    }

    private function doCreateBranch(): int
    {
        $response = $this->sendPostRequest(
            '/branch/add',
            [
                'city' => 'Москва',
                'address' => 'ул.Арбат 89'
            ]
        );
        $this->assertStatusCode(200, $response);

        $this->sendGetRequest(
            '/branch/edit',
            []
        );

        return 1;
    }

    private function assertStatusCode(int $statusCode, ResponseInterface $response): void
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), "status code must be $statusCode");
    }
}