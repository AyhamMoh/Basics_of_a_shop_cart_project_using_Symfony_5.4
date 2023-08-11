<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $productName;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $productPrice;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $productImage;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $shortDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $longDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $productBrand;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $productOrigin;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Offer::class, inversedBy="products")
     */
    private $offer;

    /**
     * @ORM\OneToMany(targetEntity=CartItem::class, mappedBy="Product")
     */
    private $cartItems;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductPrice(): ?float
    {
        return $this->productPrice;
    }

    public function setProductPrice(?float $productPrice): self
    {
        $this->productPrice = $productPrice;

        return $this;
    }


    public function getProductImage(): ?string
    {
        return $this->productImage;
    }

    public function setProductImage(?string $productImage): self
    {
        $this->productImage = $productImage;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): self
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    public function getProductBrand(): ?string
    {
        return $this->productBrand;
    }

    public function setProductBrand(?string $productBrand): self
    {
        $this->productBrand = $productBrand;

        return $this;
    }

    public function getProductOrigin(): ?string
    {
        return $this->productOrigin;
    }

    public function setProductOrigin(?string $productOrigin): self
    {
        $this->productOrigin = $productOrigin;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function __toString()
    {
        if (is_null($this->productName)) {
            return 'NULL';
        }
        return $this->productName;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    public function addCartItem(CartItem $cartItem): self
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems[] = $cartItem;
            $cartItem->setProduct($this);
        }

        return $this;
    }

    public function removeCartItem(CartItem $cartItem): self
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // set the owning side to null (unless already changed)
            if ($cartItem->getProduct() === $this) {
                $cartItem->setProduct(null);
            }
        }

        return $this;
    }

    public function getPriceAfterDiscount(int $quantity = 1): ?float
    {
        if (!$this->offer) {
            echo "No offer applied.";
            return $this->productPrice * $quantity;
        }

        $discountValue = $this->offer->getValue();
        //echo "Discount Value: " . $discountValue;

        switch ($this->offer->getType()) {

            case "10% Discount":
                //echo "Applying 10% Discount";
                $finalPrice = $this->productPrice * (1 - $discountValue) * $quantity;
                //echo "Final Price after 10% Discount: " . $finalPrice;
                return $finalPrice;

            case "5% Discount":
                //echo "Applying 5% Discount";
                $finalPrice = $this->productPrice * (1 - $discountValue) * $quantity;
                //echo "Final Price after 5% Discount: " . $finalPrice;
                return $finalPrice;

            case "15% Discount":
                //echo "Applying 15% Discount";
                $finalPrice = $this->productPrice * (1 - $discountValue) * $quantity;
                //echo "Final Price after 15% Discount: " . $finalPrice;
                return $finalPrice;

            default:
                //echo "No specific discount applied. Using default product price.";
                return $this->productPrice * $quantity;
        }
        
    }
}
