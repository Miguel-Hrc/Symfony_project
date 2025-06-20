<?php

namespace App\Entity;

use App\Repository\SweatshirtRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SweatshirtRepository::class)]
class Sweatshirt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[Assert\File(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png'],
        mimeTypesMessage: 'Veuillez uploader une image JPEG ou PNG.'
    )]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductSize::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $productSizes;

    #[ORM\Column(type: 'boolean')]
    private bool $selection;

    public function __construct()
    {
        $this->productSizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;

    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Collection<int, ProductSize>
     */
    public function getProductSizes(): Collection
    {
        return $this->productSizes;
    }

    public function addProductSize(ProductSize $productSize): self
    {
        if (!$this->productSizes->contains($productSize)) {
            $this->productSizes[] = $productSize;
            $productSize->setProduct($this);
        }
        return $this;
    }

    public function removeProductSize(ProductSize $productSize): self
    {
        if ($this->productSizes->removeElement($productSize)) {
            if ($productSize->getProduct() === $this) {
                $productSize->setProduct(null);
            }
        }
        return $this;
    }

    public function isSelection(): ?bool
    {
        return $this->selection;
    }

    public function setSelection(bool $selection): self
    {
        $this->selection = $selection;
        return $this;
    }
}