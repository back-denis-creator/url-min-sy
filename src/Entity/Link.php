<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LinkRepository::class)
 */
class Link
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
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
    private $url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $transitions;

    /**
     * @ORM\Column(type="time")
     */
    private $lifetime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    public function __construct()
    {
        $this->createAt = new DateTimeImmutable();
        $this->transitions = 0;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTransitions(): ?int
    {
        return $this->transitions;
    }

    public function setTransitions(?int $transitions): self
    {
        $this->transitions = $transitions;

        return $this;
    }

    public function getLifetime(): ?\DateTimeInterface
    {
        return $this->lifetime;
    }

    public function setLifetime(\DateTimeInterface $lifetime): self
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }
}
