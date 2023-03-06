<?php

namespace App\Entity;

use App\Repository\ApartmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApartmentRepository::class)]
class Apartment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complementAddress = null;

    #[ORM\Column(length: 5)]
    private ?int $postcode = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column(length: 10)]
    private ?string $capacity = null;

    #[ORM\Column(length: 255)]
    private ?string $surface = null;

    #[ORM\Column(length: 255)]
    private ?string $pets = null;

    #[ORM\Column(length: 255)]
    private ?string $numberOfRooms = null;

    #[ORM\Column(length: 255)]
    private ?string $numberOfBeds = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localisationDescription = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $firstCode = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $secondCode = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $thirdCode = null;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getComplementAddress(): ?string
    {
        return $this->complementAddress;
    }

    public function setComplementAddress(string $complementAddress): self
    {
        $this->complementAddress = $complementAddress;

        return $this;
    }

    public function getPostcode(): ?int
    {
        return $this->postcode;
    }

    public function setPostcode(int $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCapacity(): ?string
    {
        return $this->capacity;
    }

    public function setCapacity(string $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getSurface(): ?string
    {
        return $this->surface;
    }

    public function setSurface(string $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getPets(): ?string
    {
        return $this->pets;
    }

    public function setPets(string $pets): self
    {
        $this->pets = $pets;

        return $this;
    }

    public function getNumberOfRooms(): ?string
    {
        return $this->numberOfRooms;
    }

    public function setNumberOfRooms(string $numberOfRooms): self
    {
        $this->numberOfRooms = $numberOfRooms;

        return $this;
    }

    public function getNumberOfBeds(): ?string
    {
        return $this->numberOfBeds;
    }

    public function setNumberOfBeds(string $numberOfBeds): self
    {
        $this->numberOfBeds = $numberOfBeds;

        return $this;
    }

    public function getLocalisationDescription(): ?string
    {
        return $this->localisationDescription;
    }

    public function setLocalisationDescription(?string $localisationDescription): self
    {
        $this->localisationDescription = $localisationDescription;

        return $this;
    }

    public function getFirstCode(): ?string
    {
        return $this->firstCode;
    }

    public function setFirstCode(?string $firstCode): self
    {
        $this->firstCode = $firstCode;

        return $this;
    }

    public function getSecondCode(): ?string
    {
        return $this->secondCode;
    }

    public function setSecondCode(?string $secondCode): self
    {
        $this->secondCode = $secondCode;

        return $this;
    }

    public function getThirdCode(): ?string
    {
        return $this->thirdCode;
    }

    public function setThirdCode(?string $thirdCode): self
    {
        $this->thirdCode = $thirdCode;

        return $this;
    }
}
