<?php
// src/AppBundle/Entity/Organisation/Position.php
namespace AppBundle\Entity\Organisation;

use AppBundle\Entity\Core\User\User;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity
 * @ORM\Table(name="position")
 */
class Position
{


    function __construct(User $employee, Organisation $employer)
    {
        $employee->addPosition($this);
        $employer->addPosition($this);
    }

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="active", options={"default":false})
     */
    private $active;

    // e.g: Business Development, Telesales
    /** @ORM\Column(length=50, name="title",type="string",nullable=true) */
    private $title;

    /**
     * @var \AppBundle\Entity\Core\User\User
     * @ORM\Id @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Core\User\User",inversedBy="positions",cascade={"persist","merge","remove"})
     * @ORM\JoinColumn(name="id_employee", referencedColumnName="id")
     */
    private $employee;

    /**
     * @var \AppBundle\Entity\Organisation\Organisation
     * @ORM\Id @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Organisation\Organisation",inversedBy="positions",cascade={"persist","merge","remove"})
     * @ORM\JoinColumn(name="id_employer", referencedColumnName="id")
     */
    private $employer;



    /**
     * @return \AppBundle\Entity\Core\User\User
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param \AppBundle\Entity\Core\User\User $employee
     */
    public function setEmployee(User $employee)
    {
        $this->employee = $employee;
    }

    /**
     * @return Organisation
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * @param Organisation $employer
     */
    public function setEmployer(Organisation $employer)
    {
        $this->employer = $employer;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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