<?php
// src/AppBundle/Entity/Organisation/Handbook/Handbook.php

namespace AppBundle\Entity\Organisation\Handbook;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity
 * @ORM\Table(name="handbook")
 * @Gedmo\Loggable()
 */
class Handbook
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(length=10, nullable=true)
     * @Gedmo\Versioned
     */
    private $version;

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        foreach ($this->sections as $section) {
//            $section = new Section();
            $section->setVersion($version);
        }
        $this->version = $version;
    }


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Organisation\Organisation", inversedBy="handbook")
     * @ORM\JoinColumn(name="id_organisation", referencedColumnName="id")
     **/
    private $organisation;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Organisation\Handbook\Section", orphanRemoval=true, mappedBy="handbook", cascade={"persist", "remove", "merge"})
     **/
    private $sections;

    public function addSection(Section $section)
    {
        $this->sections->add($section);
        $section->setHandbook($this);
    }

    /**
     * @param Section $section
     */
    public function removeChild(Section $section)
    {
        $this->children->removeElement($section);
        $section->setHandbook(null);
    }

    /**
     * @var string
     * @ORM\Column(length=50)
     */
    private $title;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param mixed $organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return ArrayCollection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param ArrayCollection $sections
     */
    public function setSections($sections)
    {
        $this->sections = $sections;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

}