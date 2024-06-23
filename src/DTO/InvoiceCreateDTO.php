<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class InvoiceCreateDTO
{
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private $amount;

    #[Assert\NotBlank]
    #[Assert\Date]
    private $due_date;

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