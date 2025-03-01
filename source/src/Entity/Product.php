<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "products")]
class Product
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: "string", length: 10, unique: true)]
    private string $sku;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $unitPrice;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $specialQuantity = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true)]
    private ?float $specialPrice = null;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "on update" => "CURRENT_TIMESTAMP"])]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }
    // Getters

    public function getId(): int {
        return $this->id;
    }
    public function getSku(): ?string
    {
        return $this->sku;
    }
    public function getName(): string {
        return $this->name;
    }
    public function getUnitPrice(): float {
        return $this->unitPrice;
    }
    public function getSpecialQuantity(): ?int {
        return $this->specialQuantity;
    }

    public function getSpecialPrice(): ?float {
        return $this->specialPrice;
    }
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    //Setters
    public function setSku(string $sku): static {
        $this->sku = $sku;
        return $this;
    }

    public function setName(string $name): static {
        $this->name = $name;
        return $this;
    }

    public function setUnitPrice(float $unitPrice): static {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function setSpecialQuantity(?int $specialQuantity): static {
        $this->specialQuantity = $specialQuantity;
        return $this;
    }

    public function setSpecialPrice(?float $specialPrice): static {
        $this->specialPrice = $specialPrice;
        return $this;
    }

    public function setCreatedAt(DateTime $createdAt): static {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(DateTime $updatedAt): static {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}