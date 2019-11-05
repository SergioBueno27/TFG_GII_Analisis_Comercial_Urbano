<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Zipcode
 *
 * @ORM\Table(name="zipcode")
 * @ORM\Entity
 */
class Zipcode
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="zipcode", type="integer", nullable=true)
     */
    private $zipcode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="locality", type="integer", nullable=true)
     */
    private $locality;

    /**
     * @var int|null
     *
     * @ORM\Column(name="subregion", type="integer", nullable=true)
     */
    private $subregion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="region", type="integer", nullable=true)
     */
    private $region;



    /**
     * Get the value of id
     *
     * @return  int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  int  $id
     *
     * @return  self
     */ 
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of zipcode
     *
     * @return  int|null
     */ 
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set the value of zipcode
     *
     * @param  int|null  $zipcode
     *
     * @return  self
     */ 
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get the value of locality
     *
     * @return  int|null
     */ 
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set the value of locality
     *
     * @param  int|null  $locality
     *
     * @return  self
     */ 
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get the value of subregion
     *
     * @return  int|null
     */ 
    public function getSubregion()
    {
        return $this->subregion;
    }

    /**
     * Set the value of subregion
     *
     * @param  int|null  $subregion
     *
     * @return  self
     */ 
    public function setSubregion($subregion)
    {
        $this->subregion = $subregion;

        return $this;
    }

    /**
     * Get the value of region
     *
     * @return  int|null
     */ 
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set the value of region
     *
     * @param  int|null  $region
     *
     * @return  self
     */ 
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }
}
