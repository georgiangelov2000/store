<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateOrderRequest
{
    #[Assert\NotBlank(message: "Items field is required.")]
    #[Assert\Type(type: "string", message: "Items must be a string of SKUs.")]
    #[Assert\Regex(pattern: "/^[A-Z]+$/", message: "Items must only contain uppercase letters (SKU codes).")]
    private string $items;

    public function __construct(array $data)
    {
        $this->items = $data['items'] ?? '';
    }

    public function getItems(): string
    {
        return $this->items;
    }

    public function getParsedItems(): array
    {
        return array_count_values(str_split($this->items));
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