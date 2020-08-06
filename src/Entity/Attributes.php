<?php

namespace App\Entity;

use App\Repository\AttributesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AttributesRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"name", "value"}, flags={"fulltext"})})
 */
class Attributes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $value;

    /**
     * @ORM\ManyToMany(targetEntity=Products::class, mappedBy="attribute")
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity=AttributeGroups::class, inversedBy="attributes")
     */
    private $attribute_group;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection|Products[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Products $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addAttribute($this);
        }

        return $this;
    }

    public function removeProduct(Products $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeAttribute($this);
        }

        return $this;
    }

    public function getAttributeGroup(): ?AttributeGroups
    {
        return $this->attribute_group;
    }

    public function setAttributeGroup(?AttributeGroups $attribute_group): self
    {
        $this->attribute_group = $attribute_group;

        return $this;
    }
}
