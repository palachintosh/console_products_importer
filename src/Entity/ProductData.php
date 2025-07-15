<?php

namespace App\Entity;

use App\Repository\ProductDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductDataRepository::class)]
##[UniqueEntity(fields: ['strProductCode'], message: 'The Product Code must be unique')]
class ProductData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "intProductDataId")]
    private ?int $id = null;

    #[ORM\Column(name: "strProductName", type: "string", length: 50)]
    private ?string $strProductName = null;

    #[ORM\Column(name: "strProductDesc", type: "string", length: 255)]
    private ?string $strProductDesc = null;

    #[ORM\Column(name: "strProductCode", type: "string", length: 10, unique: true)]
    private ?string $strProductCode = null;

    #[ORM\Column(name: "dmtAdded", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dmtAdded = null;

    #[ORM\Column(name: "dmtDiscontinued", type: "date", nullable: true)]
    private ?\DateTimeInterface $dmtDiscontinued = null;

    #[ORM\Column(name: "stmTimestamp", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $stmTimestamp = null;

    // Additional required fields stocks and price
    #[ORM\Column(name: "intProductStock", type: "integer", options: ["default" => 0])]
    private ?int $intProductStock = null;

    #[ORM\Column(name: "decPrice", type: "decimal", precision: 10, scale: 2, options: ["default" => 0.00])]
    private ?float $decPrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStrProductName(): ?string
    {
        return $this->strProductName;
    }

    public function setStrProductName(string $strProductName): static
    {
        $this->strProductName = $strProductName;

        return $this;
    }

    public function getStrProductDesc(): ?string
    {
        return $this->strProductDesc;
    }

    public function setStrProductDesc(string $strProductDesc): static
    {
        $this->strProductDesc = $strProductDesc;

        return $this;
    }

    public function getStrProductCode(): ?string
    {
        return $this->strProductCode;
    }

    public function setStrProductCode(string $strProductCode): static
    {
        $this->strProductCode = $strProductCode;

        return $this;
    }

    public function getDmtAdded(): ?\DateTimeInterface
    {
        return $this->dmtAdded;
    }

    public function setDmtAdded(?\DateTimeInterface $dmtAdded): static
    {
        $this->dmtAdded = $dmtAdded;

        return $this;
    }

    public function getDmtDiscontinued(): ?\DateTimeInterface
    {
        return $this->dmtDiscontinued;
    }

    public function setDmtDiscontinued(?\DateTimeInterface $dmtDiscontinued): static
    {
        $this->dmtDiscontinued = $dmtDiscontinued;

        return $this;
    }

    public function getStmTimestamp(): ?\DateTimeInterface
    {
        return $this->stmTimestamp;
    }

    public function setStmTimestamp(?\DateTimeInterface $stmTimestamp): static
    {
        $this->stmTimestamp = $stmTimestamp;

        return $this;
    }

    public function getIntProductStock(): ?int
    {
        return $this->intProductStock;
    }
    
    public function setIntProductStock(int $intProductStock): static
    {
        $this->intProductStock = $intProductStock;

        return $this;
    }

    public function getDecPrice(): ?float
    {
        return $this->decPrice;
    }

    public function setDecPrice(float $decPrice): static
    {
        $this->decPrice = $decPrice;

        return $this;
    }
}