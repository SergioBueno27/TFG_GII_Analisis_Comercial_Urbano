<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OriginDataRepository")
 */
class OriginData
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cards;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $originZipcode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $merchants;

    /**
     * @ORM\Column(type="integer")
     */
    private $txs;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zipcode", inversedBy="originData")
     * @ORM\JoinColumn(nullable=false)
     */
    private $zipcode;

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

    public function getOriginZipcode(): ?string
    {
        return $this->originZipcode;
    }

    public function setOriginZipcode(string $originZipcode): self
    {
        $this->originZipcode = $originZipcode;

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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getZipcode(): ?Zipcode
    {
        return $this->zipcode;
    }

    public function setZipcode(?Zipcode $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }
}
