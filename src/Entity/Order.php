<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    public const STATUS_REGISTERED  = 'оформлен';
    public const STATUS_PAID        = 'оплачен';
    public const STATUS_IN_TRANSIT  = 'в пути';
    public const STATUS_DELIVERED   = 'доставлен';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ORM\OneToMany(targetEntity=OrderProduct::class, mappedBy="order")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $courier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $addressTo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deliveryDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $creationDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?UserInterface
    {
        return $this->customer;
    }

    public function setCustomer(?UserInterface $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCourier(): ?UserInterface
    {
        return $this->courier;
    }

    public function setCourier(?UserInterface $courier): self
    {
        $this->courier = $courier;

        return $this;
    }

    public function getAddressTo(): ?string
    {
        return $this->addressTo;
    }

    public function setAddressTo(string $addressTo): self
    {
        $this->addressTo = $addressTo;

        return $this;
    }

    public function getDeliveryDate(): ?string
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(string $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getCreationDate(): ?string
    {
        return $this->creationDate;
    }

    public function setCreationDate(string $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
