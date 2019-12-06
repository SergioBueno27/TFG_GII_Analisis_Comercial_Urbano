<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DestinationRepository")
 */
class Destination
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $avg;

    /**
     * @ORM\Column(type="integer")
     */
    private $cards;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $merchants;

    /**
     * @ORM\Column(type="integer")
     */
    private $txs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zipcode", inversedBy="destinations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $zipcode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DestinationData", mappedBy="destination", orphanRemoval=true)
     */
    private $destinationData;

    public function __construct()
    {
        $this->destinationData = new ArrayCollection();
    }

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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

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

    public function getZipcode(): ?Zipcode
    {
        return $this->zipcode;
    }

    public function setZipcode(?Zipcode $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @return Collection|DestinationData[]
     */
    public function getDestinationData(): Collection
    {
        return $this->destinationData;
    }

    public function addDestinationData(DestinationData $destinationData): self
    {
        if (!$this->destinationData->contains($destinationData)) {
            $this->destinationData[] = $destinationData;
            $destinationData->setDestination($this);
        }

        return $this;
    }

    public function removeDestinationData(DestinationData $destinationData): self
    {
        if ($this->destinationData->contains($destinationData)) {
            $this->destinationData->removeElement($destinationData);
            // set the owning side to null (unless already changed)
            if ($destinationData->getDestination() === $this) {
                $destinationData->setDestination(null);
            }
        }

        return $this;
    }
}
