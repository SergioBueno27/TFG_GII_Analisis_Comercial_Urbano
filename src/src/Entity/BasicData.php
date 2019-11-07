<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BasicDataRepository")
 */
class BasicData
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
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $txs;

    /**
     * @ORM\Column(type="integer")
     */
    private $merchants;

    /**
     * @ORM\Column(type="float")
     */
    private $min;

    /**
     * @ORM\Column(type="integer")
     */
    private $peak_txs_day;

    /**
     * @ORM\Column(type="integer")
     */
    private $peak_txs_hour;

    /**
     * @ORM\Column(type="float")
     */
    private $std;

    /**
     * @ORM\Column(type="integer")
     */
    private $valley_txs_day;

    /**
     * @ORM\Column(type="integer")
     */
    private $valley_txs_hour;

    /**
     * @ORM\Column(type="float")
     */
    private $max;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zipcode", inversedBy="basicData")
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getPeakTxsDay(): ?int
    {
        return $this->peak_txs_day;
    }

    public function setPeakTxsDay(int $peak_txs_day): self
    {
        $this->peak_txs_day = $peak_txs_day;

        return $this;
    }

    public function getPeakTxsHour(): ?int
    {
        return $this->peak_txs_hour;
    }

    public function setPeakTxsHour(int $peak_txs_hour): self
    {
        $this->peak_txs_hour = $peak_txs_hour;

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

    public function getValleyTxsDay(): ?int
    {
        return $this->valley_txs_day;
    }

    public function setValleyTxsDay(int $valley_txs_day): self
    {
        $this->valley_txs_day = $valley_txs_day;

        return $this;
    }

    public function getValleyTxsHour(): ?int
    {
        return $this->valley_txs_hour;
    }

    public function setValleyTxsHour(int $valley_txs_hour): self
    {
        $this->valley_txs_hour = $valley_txs_hour;

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
