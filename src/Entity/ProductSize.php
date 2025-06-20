<?php

namespace App\Entity;

use App\Repository\ProductSizeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductSizeRepository::class)]
class ProductSize
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Sweatshirt::class, inversedBy: 'productSizes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sweatshirt $product = null;

    #[ORM\ManyToOne(targetEntity: Size::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Size $size = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Sweatshirt
    {
        return $this->product;
    }

    public function setProduct(?Sweatshirt $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function setSize(?Size $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}