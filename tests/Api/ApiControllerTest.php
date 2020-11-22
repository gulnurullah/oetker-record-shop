<?php declare(strict_types=1);

namespace App\Tests\Controller;

use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provide tests for router controller matching based on incoming url
 */
class ApiControllerTest extends WebTestCase
{
    /** @return string[][] */
    public function dataRouteController(): array
    {
        return [
            ['/api/docu'],
        ];
    }

    /** @return string[][] */
    public function baseRouteController(): array
    {
        return [
            ['/api/record'],
        ];
    }

    /**
     * @dataProvider dataRouteController
     */
    public function testApiDocumentation(string $path): void
    {
        $client = self::createClient();

        $client->request(
            'GET',
            $path,
            [],
            [],
            [
                'PHP_AUTH_USER' => 'test',
                'PHP_AUTH_PW'   => 'test',
            ]
        );

        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @dataProvider baseRouteController
     */
    public function testApiFailAuthentication(string $path): void
    {
        $client = self::createClient();

        $faker = Factory::create('de_DE');

        $fakePassword = $faker->password;

        $client->request(
            'GET',
            $path,
            [],
            [],
            [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW'   => $fakePassword,
            ]
        ) ;

        /** @var Response $response */
        $response = $client->getResponse();

        $this->assertSame(401, $response->getStatusCode());
    }
}
