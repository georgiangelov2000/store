<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\OrderItem;

#[ORM\Entity]
#[ORM\Table(name: "orders")]
class Order
{
    public const STATUS_CREATED = 1;
    public const STATUS_COMPLETED = 2;
    public const STATUS_CANCELED = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer", options: ["default" => 1])]
    private int $status = self::STATUS_CREATED;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, options: ["default" => 0.00])]
    private float $totalPrice = 0.00;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTime $createdAt;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "on update" => "CURRENT_TIMESTAMP"])]
    private DateTime $updatedAt;

    #[ORM\OneToMany(mappedBy: "order", targetEntity: OrderItem::class, cascade: ["persist", "remove"])]
    private Collection $items;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->items = new ArrayCollection();
    }

    // GETTERS

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_CREATED => "Created",
            self::STATUS_COMPLETED => "Completed",
            self::STATUS_CANCELED => "Canceled",
            default => "Unknown",
        };
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    // SETTERS

    public function setStatus(int $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Order Items Methods

    public function addItem(OrderItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }
        return $this;
    }

    public function removeItem(OrderItem $item): static
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->setOrder(null);
        }
        return $this;
    }

    public static function getAllStatuses(): array
    {
        return [
            self::STATUS_CREATED => "Created",
            self::STATUS_COMPLETED => "Completed",
            self::STATUS_CANCELED => "Canceled",
        ];
    }

    public function updateStatus(int $newStatus): static
    {
        if ($this->status !== self::STATUS_CREATED) {
            throw new \Exception("Order status can only be updated from 'created'.");
        }

        if (!in_array($newStatus, [self::STATUS_COMPLETED, self::STATUS_CANCELED])) {
            throw new \Exception("Invalid status. Order can only be 'completed' or 'canceled'.");
        }

        $this->status = $newStatus;
        $this->updatedAt = new DateTime();

        return $this;
    }

}