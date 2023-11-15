<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    #[Assert\NotBlank(message: "El nombre no puede estar en blanco")]
    private string $name;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La dirección no puede estar en blanco")]
    private string $address;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La provincia no puede estar en blanco")]
    private string $province;

    #[ORM\Column]
    #[Assert\NotBlank(message: "El CIF no puede estar en blanco")]
    private string $cif;

    #[ORM\Column]
    private ?string $customerId = null;

    private const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public function __construct()
    {
        $this->customerId = $this->generateRandomId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getProvince(): string
    {
        return $this->province;
    }

    public function setProvince(string $province): void
    {
        $this->province = $province;
    }

    public function getCif(): string
    {
        return $this->cif;
    }

    public function setCif(string $cif): void
    {
        $this->cif = $cif;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'province' => $this->province,
            'cif' => $this->cif
        ];
    }

    private function generateRandomId(int $length = 5): string
    {
        return substr(str_shuffle(self::CHARACTERS), 0, $length);
    }
}
