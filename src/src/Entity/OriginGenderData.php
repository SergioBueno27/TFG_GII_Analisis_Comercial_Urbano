<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OriginGenderDataRepository")
 */
class OriginGenderData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $avg;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $cards;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gender;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $merchants;

    /**
     * @ORM\Column(type="integer")
     */
    private $txs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OriginAgeData", inversedBy="genders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $originAgeData;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvg(): ?float
    {
        return $this->avg;
    }

    public function setAvg(float $avg): self
    {
        $this->avg = $avg;

        return $this;
    }

    public function getCards(): ?int
    {
        return $this->cards;
    }

    public function setCards(int $cards): self
    {
        $this->cards = $cards;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getMerchants(): ?int
    {
        return $this->merchants;
    }

    public function setMerchants(int $merchants): self
    {
        $this->merchants = $merchants;

        return $this;
    }

    public function getTxs(): ?int
    {
        return $this->txs;
    }

    public function setTxs(int $txs): self
    {
        $this->txs = $txs;

        return $this;
    }

    public function getOriginAgeData(): ?OriginAgeData
    {
        return $this->originAgeData;
    }

    public function setOriginAgeData(?OriginAgeData $originAgeData): self
    {
        $this->originAgeData = $originAgeData;

        return $this;
    }
}
