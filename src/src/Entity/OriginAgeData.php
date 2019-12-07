<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OriginAgeDataRepository")
 */
class OriginAgeData
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
     * @ORM\Column(type="integer")
     */
    private $cards;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $age;

    /**
     * @ORM\Column(type="integer")
     */
    private $merchants;

    /**
     * @ORM\Column(type="integer")
     */
    private $txs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OriginGenderData", mappedBy="originAgeData", orphanRemoval=true)
     */
    private $genders;

    public function __construct()
    {
        $this->genders = new ArrayCollection();
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

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(string $age): self
    {
        $this->age = $age;

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

    /**
     * @return Collection|OriginGenderData[]
     */
    public function getGenders(): Collection
    {
        return $this->genders;
    }

    public function addGender(OriginGenderData $gender): self
    {
        if (!$this->genders->contains($gender)) {
            $this->genders[] = $gender;
            $gender->setOriginAgeData($this);
        }

        return $this;
    }

    public function removeGender(OriginGenderData $gender): self
    {
        if ($this->genders->contains($gender)) {
            $this->genders->removeElement($gender);
            // set the owning side to null (unless already changed)
            if ($gender->getOriginAgeData() === $this) {
                $gender->setOriginAgeData(null);
            }
        }

        return $this;
    }
}
