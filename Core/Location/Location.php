<?php
namespace AppBundle\Entity\Core\Location;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="location")
 */
class Location
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Address", mappedBy="location")
     **/
    private $addresses;
    //todo addAddress / removeAddress

    /**
     * @var boolean
     * @ORM\Column(length=125, type="string",options={"default":true})
     */
    private $active;

    /**
     * @var string
     * @ORM\Column(length=125, type="string",nullable=false) */
    private $name;

    /**
     * @var float
     * @ORM\Column(name="latitude ",type="float",nullable=true) */
    private $geoLat;
    /**
     * @var float
     * @ORM\Column(name="longitude ",type="float",nullable=true) */
    private $geoLng;

    /**
     * @return mixed
     */
    public function getGeoLng()
    {
        return $this->geoLng;
    }

    /**
     * @param mixed $geoLng
     */
    public function setGeoLng($geoLng)
    {
        $this->geoLng = $geoLng;
    }

    /**
     * @return float
     */
    public function getGeoLat()
    {
        return $this->geoLat;
    }

    /**
     * @param float $geoLat
     */
    public function setGeoLat($geoLat)
    {
        $this->geoLat = $geoLat;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param ArrayCollection $addresses
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

}