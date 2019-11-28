<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HourDataRepository")
 */
class HourData
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
     * @ORM\Column(type="string", length=2)
     */
    private $hour;

    /**
     * @ORM\Column(type="float")
     */
    private $max;

    /**
     * @ORM\Column(type="integer")
     */
    private $merchants;

    /**
     * @ORM\Column(type="float")
     */
    private $min;

    /**
     * @ORM\Column(type="float")
     */
    private $mode;

    /**
     * @ORM\Column(type="float")
     */
    private $std;

    /**
     * @ORM\Column(type="integer")
     */
    private $txs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DayData", inversedBy="hourData")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dayData;

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

    public function getHour(): ?string
    {
        return $this->hour;
    }

    public function setHour(string $hour): self
    {
        $this->hour = $hour;

        return $this;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }

    public function setMax(float $max): self
    {
        $this->max = $max;

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

    public function getMin(): ?float
    {
        return $this->min;
    }

    public function setMin(float $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getMode(): ?float
    {
        return $this->mode;
    }

    public function setMode(float $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getStd(): ?float
    {
        return $this->std;
    }

    public function setStd(float $std): self
    {
        $this->std = $std;

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

    public function getDayData(): ?DayData
    {
        return $this->dayData;
    }

    public function setDayData(?DayData $dayData): self
    {
        $this->dayData = $dayData;

        return $this;
    }
}
