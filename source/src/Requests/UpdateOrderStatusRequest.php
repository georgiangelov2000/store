<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Order;

class UpdateOrderStatusRequest
{
    #[Assert\NotBlank(message: "Status field is required.")]
    #[Assert\Type(type: "integer", message: "Status must be an integer.")]
    #[Assert\Choice(choices: [Order::STATUS_COMPLETED, Order::STATUS_CANCELED], message: "Invalid status value.")]
    private int $status;

    public function __construct(array $data)
    {
        $this->status = $data['status'] ?? null;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function validate(ValidatorInterface $validator): array
    {
        $violations = $validator->validate($this);
        $errors = [];

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
        }

        return $errors;
    }
}