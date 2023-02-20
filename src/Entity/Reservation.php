<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\ManyToOne(targetEntity: Apartment::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apartment $apartment = null;

    #[ORM\Column(length: 255)]
    private ?string $clientFirstname = null;

    #[ORM\Column(length: 255)]
    private ?string $clientLastname = null;

    #[ORM\Column(length: 255)]
    private ?string $clientEmail = null;

    #[ORM\Column]
    private ?string $clientPhone = null;

    #[ORM\Column]
    private ?int $nbOfPersons = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    #[ORM\ManyToOne(targetEntity: ReservationState::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReservationState $state = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getApartment(): ?Apartment
    {
        return $this->apartment;
    }

    public function setApartment(Apartment $apartment): self
    {
        $this->apartment = $apartment;

        return $this;
    }

    public function getClientFirstname(): ?string
    {
        return $this->clientFirstname;
    }

    public function setClientFirstname(string $clientFirstname): self
    {
        $this->clientFirstname = $clientFirstname;

        return $this;
    }

    public function getClientLastname(): ?string
    {
        return $this->clientLastname;
    }

    public function setClientLastname(string $clientLastname): self
    {
        $this->clientLastname = $clientLastname;

        return $this;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }
    
    public function setClientEmail(string $clientEmail): self
    {
        $this->clientEmail = $clientEmail;

        return $this;
    }

    public function getClientPhone(): ?string
    {
        return $this->clientPhone;
    }

    public function setClientPhone(string $clientPhone): self
    {
        $this->clientPhone = $clientPhone;

        return $this;
    }

    public function getNbOfPersons(): ?int
    {
        return $this->nbOfPersons;
    }

    public function setNbOfPersons(int $nbOfPersons): self
    {
        $this->nbOfPersons = $nbOfPersons;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getState(): ?ReservationState
    {
        return $this->state;
    }

    public function setState(ReservationState $state): self
    {
        $this->state = $state;

        return $this;
    }
}