<?php


namespace App\Service;


use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class InvoiceService
{

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Save invoice
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function saveInvoice($data)
    {
        try {
            $invoice = new Invoice();
            $invoice->setAmount($data['amount']);
            $invoice->setDueDate(new \DateTime($data['due_date']));
            $invoice->setStatus("pending");
            $invoice->setCreatedAt(new \DateTime());
            $this->em->persist($invoice);
            $this->em->flush();

            return [
                "code" => Response::HTTP_CREATED,
                "message" => "Invoice Created Successfully",
                "data" => ["id" => $invoice->getId()]
            ];

        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * Get all invoices
     * @return array
     * @throws \Exception
     */
    public function getAllInvoice()
    {
        try {
            $allInvoiceData = $this->em->getRepository(Invoice::class)->getAllInvoice();

            if (!empty($allInvoiceData)) {
                return [
                    "code" => Response::HTTP_OK,
                    "message" => "Invoices Fetched Successfully",
                    "data" => $allInvoiceData
                ];
            } else {
                return [
                    "code" => Response::HTTP_OK,
                    "message" => "No Invoices Found",
                    "data" => ""
                ];
            }
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * Update paid invoices
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updatePaidInvoice(array $data)
    {
        try {
            $invoiceObject = $this->em->getRepository(Invoice::class)->find($data['invoiceId']);

            if ($invoiceObject instanceof Invoice) {
                if ($data['amount'] > $invoiceObject->getAmount()) {
                    return [
                        "code" => Response::HTTP_BAD_REQUEST,
                        "message" => "Paid amount can not be more than invoiced amount",
                        "data" => ""
                    ];
                } else {
                    $invoiceObject->setPaidAmount($invoiceObject->getPaidAmount() + $data['amount']);
                    $invoiceObject->setUpdatedAt(new \DateTime());
                    if ($invoiceObject->getAmount() == ($invoiceObject->getPaidAmount())) {
                        $invoiceObject->setStatus('paid');
                    }
                    $this->em->persist($invoiceObject);
                    $this->em->flush();

                    return [
                        "code" => Response::HTTP_CREATED,
                        "message" => "Invoice Payment Updated Successfully",
                        "data" => ["id" => $invoiceObject->getId()]
                    ];
                }
            } else {
                return [
                    "code" => Response::HTTP_NOT_FOUND,
                    "message" => "No Invoices Found",
                    "data" => ""
                ];
            }
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * Process overdue invoices
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function processOverdueInvoice(array $data)
    {
        try {

            $invoiceObjects = $this->em->getRepository(Invoice::class)->getAllPendingInvoice();

            if (!empty($invoiceObjects)) {
                foreach ($invoiceObjects as $invoice) {
                    /* @var $invoice Invoice */
                    if (date_diff($invoice->getDueDate(), new \DateTime())->days >= $data['overdue_days']) {
                        if ($invoice->getPaidAmount()) {
                            $invoice->setStatus('paid');
                        } else {
                            $invoice->setStatus('void');
                        }
                        $invoice->setUpdatedAt(new \DateTime());

                        $this->em->persist($invoice);
                        $this->em->flush();

                        // Create new invoices for pending invocies
                        // Add late fees for the remaining amount i.e., actual amount - paid amount + late fees
                        $invoiceObject = new Invoice();
                        $invoiceObject->setAmount(($invoice->getAmount() - $invoice->getPaidAmount()) + $data['late_fee']);
                        $invoiceObject->setLateFee($data['late_fee']);
                        $invoiceObject->setDueDate((new \DateTime())->modify("+ {$data['overdue_days']} day"));
                        $invoiceObject->setStatus("pending");
                        $invoiceObject->setCreatedAt(new \DateTime());
                        $this->em->persist($invoiceObject);
                        $this->em->flush();
                    }
                }
                return [
                    "code" => Response::HTTP_CREATED,
                    "message" => "Pending invoiced processed Successfully",
                    "data" => ""
                ];

            } else {
                return [
                    "code" => Response::HTTP_NOT_FOUND,
                    "message" => "No Invoices Found",
                    "data" => ""
                ];
            }
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }
}