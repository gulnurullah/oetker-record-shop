<?php declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Record;
use App\Enum\JsonResponseEnum;
use App\Enum\RequestParametersEnum;
use App\Services\RecordService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;
use Faker\Generator;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provide tests for router controller matching based on incoming url
 */
class RecordApiControllerTest extends WebTestCase
{
    public const BASE_PATH = 'api/record';

    /** @var EntityManagerInterface */
    private $em;

    /** @var RecordService */
    private $recordService;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->recordService = self::$container->get('App\Services\RecordService');
        $this->setupMockDb();
        $this->loadDefaultCartFixtures();
    }

    protected function setupMockDb(): void
    {
        $metaData = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metaData);
    }

    private function loadDefaultCartFixtures(): void
    {
        $generator = Factory::create();
        $this->loadDefaultSource($generator);
    }

    private function loadDefaultSource(Generator $generator): void
    {
        $populator = new Populator($generator, $this->em);
        $faker = Factory::create('de_DE');

        $populator->addEntity(
            Record::class,
            1,
            [
                "name" => 'Test Record',
                "description" => $faker->text,
                "artist" => $faker->name,
                "createdAt" => new DateTime('now'),
                "updatedAt" => null,
                "deletedAt" => null,
            ]
        );
        $populator->execute();
    }

    public function testRecordGetAll(): void
    {
        /** @var JsonResponse $response */
        $response = $this->recordService->getAllRecords(
            RequestParametersEnum::LIMIT,
            RequestParametersEnum::OFFSET
        );
        $responseArray = json_decode($response->getContent(), true);

        self::assertSame(200, $responseArray[JsonResponseEnum::CODE]);
        self::assertSame(1, count($responseArray[JsonResponseEnum::DATA]));
    }

    public function testSearchedAll(): void
    {
        /** @var JsonResponse $response */
        $response = $this->recordService->getResearchedRecords(
            'test',
            RequestParametersEnum::LIMIT,
            RequestParametersEnum::OFFSET
        );
        $responseArray = json_decode($response->getContent(), true);

        self::assertSame(200, $responseArray[JsonResponseEnum::CODE]);
        self::assertSame(1, count($responseArray[JsonResponseEnum::DATA]));
    }

    public function testAddRecord(): void
    {
        $faker = Factory::create('de_DE');
        /** @var JsonResponse $response */
        $response = $this->recordService->addRecord($faker->name, $faker->name, $faker->text);
        $responseArray = json_decode($response->getContent(), true);

        self::assertSame(201, $responseArray[JsonResponseEnum::CODE]);
    }

    public function testUpdateRecord(): void
    {
        $faker = Factory::create('de_DE');
        /** @var JsonResponse $response */
        $response = $this->recordService->updateRecord(1, $faker->name, $faker->name, $faker->text);
        $responseArray = json_decode($response->getContent(), true);

        self::assertSame(200, $responseArray[JsonResponseEnum::CODE]);
    }

    public function testInvalidUpdateRecord(): void
    {
        $faker = Factory::create('de_DE');
        /** @var JsonResponse $response */
        $response = $this->recordService->updateRecord(9999, $faker->name, $faker->name, $faker->text);
        $responseArray = json_decode($response->getContent(), true);

        self::assertSame(400, $responseArray[JsonResponseEnum::CODE]);
        self::assertNotEmpty($responseArray[JsonResponseEnum::ERROR_MESSAGE]);
    }

    public function testDeleteRecord(): void
    {
        /** @var JsonResponse $response */
        $response = $this->recordService->deleteRecord(1);
        $responseArray = json_decode($response->getContent(), true);

        self::assertSame(200, $responseArray[JsonResponseEnum::CODE]);
    }
}
