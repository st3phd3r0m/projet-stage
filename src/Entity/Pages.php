<?php

namespace App\Entity;

use App\Repository\PagesRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass=PagesRepository::class)
 * @ORM\Table(indexes={@ORM\Index(columns={"title", "content", "meta_tag_title", "meta_tag_description"}, flags={"fulltext"})})
 */
class Pages
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
    private $content;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $image = [];

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
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="pages")
     */
    private $user;

    
    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(?array $image): self
    {
        $this->image = $image;

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

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

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
}
