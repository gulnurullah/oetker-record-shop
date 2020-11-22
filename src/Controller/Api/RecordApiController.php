<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Enum\RequestParametersEnum;
use App\Repository\RecordRepository;
use App\Services\RecordService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Traits\Response as ResponseTrait;

/** @Route("/api", name="api_") */
class RecordApiController extends AbstractController
{
    use ResponseTrait;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /** @var RecordService */
    protected $recordService;

    public function __construct(
        LoggerInterface $logger,
        RecordService $recordService
    ) {
        $this->logger = $logger;
        $this->recordService = $recordService;
    }

    /**
     * Get all record list
     *
     *  ### Example request endpoint ###
     *
     * ```
     * /api/record
     * ```
     *
     * @Route("/record",
     *     methods={"GET"},
     *     options={},
     *     name="record_get"
     * )
     *
     * @SWG\Tag(name="Default")
     *
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="The list limit",
     *     required=false
     * )
     *
     * @SWG\Parameter(
     *     name="offset",
     *     in="query",
     *     type="integer",
     *     description="The list offset",
     *     required=false
     * )
     *
     * @SWG\Response(
     *     response="500",
     *     description="Server Error",
     * )
     *
     * @SWG\Response(
     *     response="200",
     *     description="The data returned succesfully",
     * )
     *
     * @SWG\Response(
     *     response="400",
     *     description="Bad Request",
     * )
     *
     */
    public function getRecords(Request $request): JsonResponse {

        $limit = (int) $request->get('limit', RequestParametersEnum::LIMIT);
        $offset = (int) $request->get('offset', RequestParametersEnum::OFFSET);

        if ($limit <= 0 || $offset < 0) {
            return $this->jsonResponse(
                false,
                Response::HTTP_BAD_REQUEST,
                'Limit and offset should be numeric number.'
            );
        }

        return $this->recordService->getAllRecords($limit, $offset);
    }

    /**
     * Get searched record list
     *
     *  ### Example request endpoint ###
     *
     * ```
     * /api/record/search
     * ```
     *
     * @Route("/record/search",
     *     methods={"GET"},
     *     options={},
     *     name="record_search"
     * )
     *
     * @SWG\Tag(name="Default")
     *
     * @SWG\Parameter(
     *     name="query",
     *     in="query",
     *     type="string",
     *     description="The search words",
     *     required=true
     * )
     *
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="The list limit",
     *     required=false
     * )
     *
     * @SWG\Parameter(
     *     name="offset",
     *     in="query",
     *     type="integer",
     *     description="The list offset",
     *     required=false
     * )
     *
     * @SWG\Response(
     *     response="500",
     *     description="Server Error",
     * )
     *
     * @SWG\Response(
     *     response="200",
     *     description="The data returned succesfully",
     * )
     *
     * @SWG\Response(
     *     response="400",
     *     description="Bad Request",
     * )
     *
     */
    public function getSearchedRecords(Request $request): JsonResponse {

        $query = $request->get('query', null);
        $limit = (int) $request->get('limit', RequestParametersEnum::LIMIT);
        $offset = (int) $request->get('offset', RequestParametersEnum::OFFSET);

        if ($limit <= 0 || $offset < 0) {
            return $this->jsonResponse(
                false,
                Response::HTTP_BAD_REQUEST,
                'Limit and offset should be numeric number.'
            );
        }

        if ($query === null) {
            return $this->jsonResponse(
                false,
                Response::HTTP_BAD_REQUEST,
                'In order to make search you should give some words'
            );
        }

        return $this->recordService->getResearchedRecords($query, $limit, $offset);
    }

    /**
     * Add new record on the store
     *
     *  ### Example request endpoint ###
     *
     * ```
     * /api/record
     * ```
     *
     * @Route("/record",
     *     methods={"POST"},
     *     options={},
     *     name="record_add"
     * )
     *
     * @SWG\Tag(name="Default")
     *
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     type="string",
     *     description="The record name",
     *     required=true,
     *     @SWG\Schema(
     *      type="object",
     *      @SWG\Property(property="name", type="string", example="Record name"),
     *      @SWG\Property(property="artist", type="string", example="Record artist name"),
     *      @SWG\Property(property="description", type="string", example="The record description")
     *     ),
     * )
     *
     * @SWG\Response(
     *     response="500",
     *     description="Server Error",
     * )
     *
     * @SWG\Response(
     *     response="200",
     *     description="The data returned succesfully",
     * )
     *
     * @SWG\Response(
     *     response="400",
     *     description="Bad Request",
     * )
     *
     */
    public function addRecords(Request $request, RecordRepository $recordRepository): JsonResponse {

        $body = json_decode($request->getContent(), true);

        $recordName = $body['name'] ?? null;
        $recordArtist = $body['artist'] ?? null;
        $recordDescription = $body['description'] ?? null;

        if ($recordName === null || $recordDescription === null || $recordArtist === null) {
            return $this->jsonResponse(
                false,
                Response::HTTP_BAD_REQUEST,
                'Record name and description should be defined.'
            );
        }

        return $this->recordService->addRecord($recordName, $recordArtist, $recordDescription);
    }

    /**
     * Update certain record
     *
     *  ### Example request endpoint ###
     *
     * ```
     * /api/record
     * ```
     *
     * @Route("/record/{id}",
     *     methods={"PATCH"},
     *     options={},
     *     name="record_patch"
     * )
     *
     * @SWG\Tag(name="Default")
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The record id",
     *     required=true
     * )
     *
     * @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     type="string",
     *     description="The record name",
     *     required=true,
     *     @SWG\Schema(
     *      type="object",
     *      @SWG\Property(property="name", type="string", example="Record name"),
     *      @SWG\Property(property="artist", type="string", example="Record artist"),
     *      @SWG\Property(property="description", type="string", example="Record description")
     *     ),
     * )
     *
     * @SWG\Response(
     *     response="500",
     *     description="Server Error",
     * )
     *
     * @SWG\Response(
     *     response="200",
     *     description="The data returned succesfully",
     * )
     *
     * @SWG\Response(
     *     response="400",
     *     description="Bad Request",
     * )
     *
     */
    public function updateRecord(Request $request, int $id): JsonResponse {

        $body = json_decode($request->getContent(), true);

        $recordName = $body['name'] ?? null;
        $recordArtist = $body['artist'] ?? null;
        $recordDescription = $body['description'] ?? null;

        if (!$id || $recordName === null || $recordArtist === null || $recordDescription === null) {
            return $this->jsonResponse(
                false,
                Response::HTTP_BAD_REQUEST,
                'Record name and description should be defined.'
            );
        }

        return $this->recordService->updateRecord($id, $recordName, $recordArtist, $recordDescription);
    }

    /**
     * Update certain record
     *
     *  ### Example request endpoint ###
     *
     * ```
     * /api/record
     * ```
     *
     * @Route("/record/{id}",
     *     methods={"DELETE"},
     *     options={},
     *     name="record_delete"
     * )
     *
     * @SWG\Tag(name="Default")
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The record id",
     *     required=true
     * )
     *
     * @SWG\Response(
     *     response="500",
     *     description="Server Error",
     * )
     *
     * @SWG\Response(
     *     response="200",
     *     description="The data returned succesfully",
     * )
     *
     * @SWG\Response(
     *     response="400",
     *     description="Bad Request",
     * )
     *
     */
    public function deleteRecord(int $id): JsonResponse {

        if (!is_numeric($id)) {
            return $this->jsonResponse(
                false,
                Response::HTTP_BAD_REQUEST,
                'Record id should be numeric number.'
            );
        }

        return $this->recordService->deleteRecord($id);
    }
}
