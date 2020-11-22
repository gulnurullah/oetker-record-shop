<?php declare(strict_types=1);

namespace App\Services;

use App\Entity\Record;
use App\Repository\RecordRepository;
use App\Traits\Response as ResponseTrait;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RecordService
{
    use ResponseTrait;

    /** @var LoggerInterface  */
    private $logger;

    /**
     * @var RecordRepository
     */
    protected $recordRepository;

    public function __construct(
        LoggerInterface $logger,
        RecordRepository $recordRepository
    ) {
        $this->logger = $logger;
        $this->recordRepository = $recordRepository;
    }

    public function getAllRecords(int $limit, int $offset): JsonResponse {

        try {
            $recordList = $this->recordRepository->findByAsArray(
                [],
                ['id' => 'DESC'],
                $limit,
                $offset
            );

            return $this->jsonResponse(true,Response::HTTP_OK,null, $recordList);
        } catch (Throwable $e) {
            $this->logger->error('Error while get records item from database', [
                'dump' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            ]);

            return $this->jsonResponse(false, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getResearchedRecords(string $query): JsonResponse {

        try {
            $recordList = $this->recordRepository->searchRecords(
                $query
            );

            return $this->jsonResponse(true,Response::HTTP_OK,null, $recordList);
        } catch (Throwable $e) {
            $this->logger->error('Error while search records on database', [
                'dump' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            ]);

            return $this->jsonResponse(false, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addRecord(string $name, string $artist, string $description): JsonResponse {

        try {
            $this->recordRepository->saveRecord($name, $artist,  $description);

            return $this->jsonResponse(
                true,
                Response::HTTP_CREATED
            );

        } catch (Throwable $e) {
            $this->logger->error('Error while add record item to database', [
                'dump' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            ]);

            return $this->jsonResponse(false, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateRecord(int $id, string $name, string $artist, string $description): JsonResponse {

        try {
            /** @var Record $record */
            $record = $this->recordRepository->find($id);

            if (empty($record)) {
                return $this->jsonResponse(
                    false,
                    Response::HTTP_BAD_REQUEST,
                    'Requested record could not find'
                );
            }

            $record->setName($name);
            $record->setDescription($description);
            $record->setArtist($artist);
            $record->setUpdatedAt(new DateTime('now'));
            // Update record with new info
            $this->recordRepository->update($record);

            return $this->jsonResponse(true, Response::HTTP_OK);
        } catch (Throwable $e) {

            $this->logger->error('Error while update record in database', [
                'dump' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            ]);

            return $this->jsonResponse(false, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteRecord(int $id): JsonResponse {

        try {
            /** @var Record $record */
            $record = $this->recordRepository->find($id);

            if (empty($record)) {
                return $this->jsonResponse(
                    false,
                    Response::HTTP_BAD_REQUEST,
                    'Requested record could not find'
                );
            }

            $record->setDeletedAt(new DateTime('now'));
            // Delete record with new info
            $this->recordRepository->update($record);

            return $this->jsonResponse(true, Response::HTTP_OK);
        } catch (Throwable $e) {
            $this->logger->error('Error while delete record in database', [
                'dump' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            ]);

            return $this->jsonResponse(false, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
