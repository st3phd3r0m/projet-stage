<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ProductsRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"title", "meta_tag_title", "description", "meta_tag_description"}, flags={"fulltext"})})
 */
class Products
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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $keywords = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meta_tag_title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $meta_tag_description;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $meta_tag_keywords = [];

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $weezeevent;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $pre_tax_price;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $tax_included_price;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="product")
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Languages::class, inversedBy="products")
     */
    private $language;

    /**
     * @ORM\ManyToMany(targetEntity=Categories::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity=Attributes::class, inversedBy="products")
     */
    private $attribute;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="product")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="product", cascade={"persist"})
     */
    private $images;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->attribute = new ArrayCollection();
        $this->category = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getKeywords(): ?array
    {
        return $this->keywords;
    }

    public function setKeywords(?array $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getMetaTagTitle(): ?string
    {
        return $this->meta_tag_title;
    }

    public function setMetaTagTitle(?string $meta_tag_title): self
    {
        $this->meta_tag_title = $meta_tag_title;

        return $this;
    }

    public function getMetaTagDescription(): ?string
    {
        return $this->meta_tag_description;
    }

    public function setMetaTagDescription(?string $meta_tag_description): self
    {
        $this->meta_tag_description = $meta_tag_description;

        return $this;
    }

    public function getMetaTagKeywords(): ?array
    {
        return $this->meta_tag_keywords;
    }

    public function setMetaTagKeywords(?array $meta_tag_keywords): self
    {
        $this->meta_tag_keywords = $meta_tag_keywords;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getWeezeevent(): ?string
    {
        return $this->weezeevent;
    }

    public function setWeezeevent(?string $weezeevent): self
    {
        $this->weezeevent = $weezeevent;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPreTaxPrice(): ?string
    {
        return $this->pre_tax_price;
    }

    public function setPreTaxPrice(?string $pre_tax_price): self
    {
        $this->pre_tax_price = $pre_tax_price;

        return $this;
    }

    public function getTaxIncludedPrice(): ?string
    {
        return $this->tax_included_price;
    }

    public function setTaxIncludedPrice(?string $tax_included_price): self
    {
        $this->tax_included_price = $tax_included_price;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }

    public function getLanguage(): ?Languages
    {
        return $this->language;
    }

    public function setLanguage(?Languages $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Collection|Categories[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Categories $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Categories $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Attributes[]
     */
    public function getAttribute(): Collection
    {
        return $this->attribute;
    }

    public function addAttribute(Attributes $attribute): self
    {
        if (!$this->attribute->contains($attribute)) {
            $this->attribute[] = $attribute;
        }

        return $this;
    }

    public function removeAttribute(Attributes $attribute): self
    {
        if ($this->attribute->contains($attribute)) {
            $this->attribute->removeElement($attribute);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Messages[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setProduct($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getProduct() === $this) {
                $message->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }
}
