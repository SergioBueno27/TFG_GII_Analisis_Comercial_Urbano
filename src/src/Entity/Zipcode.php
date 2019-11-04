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


}
