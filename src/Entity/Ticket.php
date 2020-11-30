<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
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
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $done;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Severity::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $severity;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    public function __construct(string $title = null, int $severity = null)
    {
        $this->createdAt = new DateTime();
        $this->done = false;

        $this->title = $title;
        $this->severity = $severity;
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSeverity(): ?Severity
    {
        return $this->severity;
    }

    public function setSeverity(Severity $severity): self
    {
        $this->severity = $severity;

        return $this;
    }

    public function getDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(?bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSeverityClass() {
        $classes = ['info', 'primary', 'success', 'warning', 'danger'];
        return $classes[$this->severity->getId() - 1];   
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}