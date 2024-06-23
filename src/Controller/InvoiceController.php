<?php

namespace App\Controller;

use App\DTO\InvoiceCreateDTO;
use App\DTO\ProcessOverdueInvoiceDTO;
use App\DTO\UpdatePaidInvoiceDTO;
use App\Service\InvoiceService;
use App\Service\ValidationService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceController extends AbstractController
{

    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var ValidationService
     */
    private $validationService;
    private $logger;

    public function __construct(ValidationService $validationService,LoggerInterface $logger, InvoiceService $invoiceService)
    {
        $this->validationService = $validationService;
        $this->invoiceService = $invoiceService;
        $this->logger = $logger;
    }

    #[Route('/invoices', name: 'save_invoice', methods: ['POST'])]
    public function saveInvoice(Request $request, ValidatorInterface $validator): JsonResponse
    {
        try {
            $error = $this->validationService->validateRequestPayload($request, InvoiceCreateDTO::class);

            if (!empty($error)) {
                return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
            }

            $jsonRequest = $request->getContent();
            $requestArray = json_decode($jsonRequest, true);

            $response = $this->invoiceService->saveInvoice($requestArray);

            return new JsonResponse($response, Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    #[Route('/invoices', name: 'get_all_invoices', methods: ['GET'])]
    public function getAllInvoice(Request $request): JsonResponse
    {
        try {
            $response = $this->invoiceService->getAllInvoice();

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    #[Route('/invoices/{invoiceId}/payments', name: 'updated_paid_invoice', methods: ['POST'])]
    public function updatedPaidInvoice(Request $request, ValidatorInterface $validator): JsonResponse
    {
        try {
            $error = $this->validationService->validateRequestPayload($request, UpdatePaidInvoiceDTO::class);

            if (!empty($error)) {
                return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
            }

            $jsonRequest = $request->getContent();
            $requestArray = json_decode($jsonRequest, true);
            $requestArray['invoiceId'] = $request->get('invoiceId');

            $response = $this->invoiceService->updatePaidInvoice($requestArray);

            return new JsonResponse($response, Response::HTTP_CREATED);

        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    #[Route('/invoices/process-overdue', name: 'process_overdue_invoice', methods: ['POST'])]
    public function processOverdueInvoice(Request $request, ValidatorInterface $validator): JsonResponse
    {
        try {
            $error = $this->validationService->validateRequestPayload($request, ProcessOverdueInvoiceDTO::class);

            if (!empty($error)) {
                return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
            }

            $jsonRequest = $request->getContent();
            $requestArray = json_decode($jsonRequest, true);

            $response = $this->invoiceService->processOverdueInvoice($requestArray);

            return new JsonResponse($response, Response::HTTP_CREATED);

        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }
}
