<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePaidInvoiceDTO
{
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private $amount;

    #[Assert\NotBlank]
    private $invoiceId;

    private $errors = '';

    public function getErrors(): string
    {
        return $this->errors;
    }

    public function setErrors(string $errors)
    {
        $this->errors = $errors;
    }
}