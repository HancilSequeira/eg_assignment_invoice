<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProcessOverdueInvoiceDTO
{
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private $late_fee;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private $overdue_days;

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