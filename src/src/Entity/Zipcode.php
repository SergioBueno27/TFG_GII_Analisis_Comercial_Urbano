<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ZipcodeRepository")
 */
class Zipcode
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $zipcode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BasicData", mappedBy="zipcode", orphanRemoval=true)
     */
    private $basicData;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CategoryData", mappedBy="zipcode")
     */
    private $categoryData;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DayData", mappedBy="zipcode")
     */
    private $dayData;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Destination", mappedBy="zipcode", orphanRemoval=true)
     */
    private $destinations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OriginData", mappedBy="zipcode", orphanRemoval=true)
     */
    private $originData;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OriginAgeData", mappedBy="zipcode", orphanRemoval=true)
     */
    private $originAgeData;

    public function __construct()
    {
        $this->categoryData = new ArrayCollection();
        $this->dayData = new ArrayCollection();
        $this->destinations = new ArrayCollection();
        $this->originData = new ArrayCollection();
        $this->originAgeData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(int $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @return Collection|BasicData[]
     */
    public function getBasicData(): Collection
    {
        return $this->basicData;
    }

    public function addBasicData(BasicData $basicData): self
    {
        if (!$this->basicData->contains($basicData)) {
            $this->basicData[] = $basicData;
            $basicData->setZipcode($this);
        }

        return $this;
    }

    public function removeBasicData(BasicData $basicData): self
    {
        if ($this->basicData->contains($basicData)) {
            $this->basicData->removeElement($basicData);
            // set the owning side to null (unless already changed)
            if ($basicData->getZipcode() === $this) {
                $basicData->setZipcode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CategoryData[]
     */
    public function getCategoryData(): Collection
    {
        return $this->categoryData;
    }

    public function addCategoryData(CategoryData $categoryData): self
    {
        if (!$this->categoryData->contains($categoryData)) {
            $this->categoryData[] = $categoryData;
            $categoryData->setZipcode($this);
        }

        return $this;
    }

    public function removeCategoryData(CategoryData $categoryData): self
    {
        if ($this->categoryData->contains($categoryData)) {
            $this->categoryData->removeElement($categoryData);
            // set the owning side to null (unless already changed)
            if ($categoryData->getZipcode() === $this) {
                $categoryData->setZipcode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DayData[]
     */
    public function getDayData(): Collection
    {
        return $this->dayData;
    }

    public function addDayData(DayData $dayData): self
    {
        if (!$this->dayData->contains($dayData)) {
            $this->dayData[] = $dayData;
            $dayData->setZipcode($this);
        }

        return $this;
    }

    public function removeDayData(DayData $dayData): self
    {
        if ($this->dayData->contains($dayData)) {
            $this->dayData->removeElement($dayData);
            // set the owning side to null (unless already changed)
            if ($dayData->getZipcode() === $this) {
                $dayData->setZipcode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Destination[]
     */
    public function getDestinations(): Collection
    {
        return $this->destinations;
    }

    public function addDestination(Destination $destination): self
    {
        if (!$this->destinations->contains($destination)) {
            $this->destinations[] = $destination;
            $destination->setZipcode($this);
        }

        return $this;
    }

    public function removeDestination(Destination $destination): self
    {
        if ($this->destinations->contains($destination)) {
            $this->destinations->removeElement($destination);
            // set the owning side to null (unless already changed)
            if ($destination->getZipcode() === $this) {
                $destination->setZipcode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OriginData[]
     */
    public function getOriginData(): Collection
    {
        return $this->originData;
    }

    public function addOriginData(OriginData $originData): self
    {
        if (!$this->originData->contains($originData)) {
            $this->originData[] = $originData;
            $originData->setZipcode($this);
        }

        return $this;
    }

    public function removeOriginData(OriginData $originData): self
    {
        if ($this->originData->contains($originData)) {
            $this->originData->removeElement($originData);
            // set the owning side to null (unless already changed)
            if ($originData->getZipcode() === $this) {
                $originData->setZipcode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OriginAgeData[]
     */
    public function getOriginAgeData(): Collection
    {
        return $this->originAgeData;
    }

    public function addOriginAgeData(OriginAgeData $originAgeData): self
    {
        if (!$this->originAgeData->contains($originAgeData)) {
            $this->originAgeData[] = $originAgeData;
            $originAgeData->setZipcode($this);
        }

        return $this;
    }

    public function removeOriginAgeData(OriginAgeData $originAgeData): self
    {
        if ($this->originAgeData->contains($originAgeData)) {
            $this->originAgeData->removeElement($originAgeData);
            // set the owning side to null (unless already changed)
            if ($originAgeData->getZipcode() === $this) {
                $originAgeData->setZipcode(null);
            }
        }

        return $this;
    }
}
