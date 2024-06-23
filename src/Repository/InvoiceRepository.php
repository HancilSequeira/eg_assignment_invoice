<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Invoice::class);
        $this->logger = $logger;
    }

    /**
     * @return Invoice[] Returns an array of Invoice objects
     */
    public function getAllInvoice(): array
    {
        try {

            $conn = $this->getEntityManager()->getConnection();

            $sql = "SELECT i.id, i.amount, i.paid_amount, DATE_FORMAT(i.due_date,'%Y-%m-%d')  AS due_Date, i.status
            FROM INVOICE as i
            ORDER BY i.id ASC ";
            return $conn->fetchAllAssociative($sql);

        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * Query to fetch all pending and overdue invoices
     * @return mixed
     * @throws \Exception
     */
    public function getAllPendingInvoice()
    {
        try {
            $query = $this->createQueryBuilder('i')
                ->where('i.status = :status')
                ->setParameter('status', "pending");
            return $query->getQuery()->getResult();
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }
}
