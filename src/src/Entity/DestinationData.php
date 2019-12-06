<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DestinationDataRepository")
 */
class DestinationData
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Destination", inversedBy="destinationData")
     * @ORM\JoinColumn(nullable=false)
     */
    private $destination;

    /**
     * @ORM\Column(type="integer")
     */
    private $txs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $merchants;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $destinationZipcode;

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

    public function getDestination(): ?Destination
    {
        return $this->destination;
    }

    public function setDestination(?Destination $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getTxs(): ?int
    {
        return $this->txs;
    }

    public function setTxs(?int $txs): self
    {
        $this->txs = $txs;

        return $this;
    }

    public function getMerchants(): ?int
    {
        return $this->merchants;
    }

    public function setMerchants(?int $merchants): self
    {
        $this->merchants = $merchants;

        return $this;
    }

    public function getDestinationZipcode(): ?string
    {
        return $this->destinationZipcode;
    }

    public function setDestinationZipcode(string $destinationZipcode): self
    {
        $this->destinationZipcode = $destinationZipcode;

        return $this;
    }
}
