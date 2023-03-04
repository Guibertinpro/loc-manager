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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\ManyToOne(targetEntity: Apartment::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Apartment $apartment = null;

    #[ORM\Column]
    private ?int $nbOfAdults = null;

    #[ORM\Column]
    private ?int $nbOfChildren = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    #[ORM\ManyToOne(targetEntity: ReservationState::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReservationState $state = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(length: 50)]
    private ?string $arrhes = null;

    #[ORM\Column(length: 50)]
    private ?string $leftToPay = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateLeftToPay = null;

    #[ORM\OneToOne(mappedBy: 'reservation')]
    private ?ContractFile $contractFile = null;

    #[ORM\Column]
    private ?bool $cautionValidated = false;

    #[ORM\Column]
    private ?bool $arrhesValidated = false;

    #[ORM\Column]
    private ?bool $soldeValidated = false;

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

    public function getNbOfAdults(): ?int
    {
        return $this->nbOfAdults;
    }

    public function setNbOfAdults(int $nbOfAdults): self
    {
        $this->nbOfAdults = $nbOfAdults;

        return $this;
    }

    public function getNbOfChildren(): ?int
    {
        return $this->nbOfChildren;
    }

    public function setNbOfChildren(int $nbOfChildren): self
    {
        $this->nbOfChildren = $nbOfChildren;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getArrhes(): ?string
    {
        return $this->arrhes;
    }

    public function setArrhes(string $arrhes): self
    {
        $this->arrhes = $arrhes;

        return $this;
    }

    public function getLeftToPay(): ?string
    {
        return $this->leftToPay;
    }

    public function setLeftToPay(string $leftToPay): self
    {
        $this->leftToPay = $leftToPay;

        return $this;
    }

    public function getDateLeftToPay(): ?\DateTimeInterface
    {
        return $this->dateLeftToPay;
    }

    public function setDateLeftToPay(?\DateTimeInterface $dateLeftToPay): self
    {
        $this->dateLeftToPay = $dateLeftToPay;

        return $this;
    }

    public function getContractFile(): ?ContractFile
    {
        return $this->contractFile;
    }

    public function setContractFile(?ContractFile $contractFile): self
    {
        // unset the owning side of the relation if necessary
        if ($contractFile === null && $this->contractFile !== null) {
            $this->contractFile->setReservation(null);
        }

        // set the owning side of the relation if necessary
        if ($contractFile !== null && $contractFile->getReservation() !== $this) {
            $contractFile->setReservation($this);
        }

        $this->contractFile = $contractFile;

        return $this;
    }

    public function isCautionValidated(): ?bool
    {
        return $this->cautionValidated;
    }

    public function setCautionValidated(bool $cautionValidated): self
    {
        $this->cautionValidated = $cautionValidated;

        return $this;
    }

    public function isArrhesValidated(): ?bool
    {
        return $this->arrhesValidated;
    }

    public function setArrhesValidated(bool $arrhesValidated): self
    {
        $this->arrhesValidated = $arrhesValidated;

        return $this;
    }

    public function isSoldeValidated(): ?bool
    {
        return $this->soldeValidated;
    }

    public function setSoldeValidated(bool $soldeValidated): self
    {
        $this->soldeValidated = $soldeValidated;

        return $this;
    }
}