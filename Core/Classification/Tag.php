<?php
// src/AppBundle/Entity/Core/Classification/Tag.php
namespace AppBundle\Entity\Core\Classification;

use AppBundle\Services\Core\Framework\BaseVoterSupportInterface;
use AppBundle\Services\Core\Framework\ListVoterSupportInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\ClassificationBundle\Entity\BaseTag;

/**
 * @ORM\Entity
 * @ORM\Table(name="core__classification__tag")
 */
class Tag extends BaseTag implements BaseVoterSupportInterface, ListVoterSupportInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    function __construct()
    {
        $this->slug = "";

        $this->enabled = false;
    }

//    /**
//     * @var ArrayCollection Position $employeeClassPositions
//     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Organisation\Position", inversedBy="employeeClasses")
//     * @ORM\JoinTable(name="organisation__positions_classes",
//     *      joinColumns={@ORM\JoinColumn(name="id_tag", referencedColumnName="id")},
//     *      inverseJoinColumns={@ORM\JoinColumn(name="id_position", referencedColumnName="id")}
//     *      )
//     * @Serializer\Exclude
//     */
//    private $employeeClassPositions;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Core\Core\Site")
     * @ORM\JoinColumn(name="id_site", referencedColumnName="id")
     **/
    private $site;

    /**
     * var ArrayCollection
     * ORM\ManyToMany(targetEntity="AppBundle\Entity\Organisation\Business\Business", mappedBy="tags")
     **/
//    private $businesses;


    /**
     * @var bool
     * @ORM\Column(name="job_type",type="boolean",nullable=true,options={"default":false})
     */
    private $jobType = false;

    /**
     * @var bool
     * @ORM\Column(name="industry",type="boolean",nullable=true,options={"default":false})
     */
    private $industry = false;

    /**
     * @var bool
     * @ORM\Column(name="business_type",type="boolean",nullable=true,options={"default":false})
     */
    private $businessType = false;

    /**
     * @var bool
     * @ORM\Column(name="business_category",type="boolean",nullable=true,options={"default":false})
     */
    private $businessCategory = false;

    /**
     * @var bool
     * @ORM\Column(name="system",type="boolean",nullable=true,options={"default":false})
     */
    private $system = false;

    /**
     * @var bool
     * @ORM\Column(name="employee_class",type="boolean",nullable=true,options={"default":false})
     */
    private $employeeClass = false;

    /**
     * @var bool
     * @ORM\Column(name="employee_function",type="boolean",nullable=true,options={"default":false})
     */
    private $employeeFunction = false;


    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $sites
     */
    public function setSite($site)
    {
        $this->sites = $site;
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
     * @return boolean
     */
    public function isEmployeeClass()
    {
        return $this->employeeClass;
    }

    /**
     * @param boolean $employeeClass
     */
    public function setEmployeeClass($employeeClass)
    {
        $this->employeeClass = $employeeClass;
    }

    /**
     * @return boolean
     */
    public function isEmployeeFunction()
    {
        return $this->employeeFunction;
    }

    /**
     * @param boolean $employeeFunction
     */
    public function setEmployeeFunction($employeeFunction)
    {
        $this->employeeFunction = $employeeFunction;
    }

    /**
     * @return boolean
     */
    public function isSystem()
    {
        return $this->system;
    }

    /**
     * @param boolean $system
     */
    public function setSystem($system)
    {
        $this->system = $system;
    }

    /**
     * @return boolean
     */
    public function isBusinessType()
    {
        return $this->businessType;
    }

    /**
     * @param boolean $businessType
     */
    public function setBusinessType($businessType)
    {
        $this->businessType = $businessType;
    }

    /**
     * @return boolean
     */
    public function isBusinessCategory()
    {
        return $this->businessCategory;
    }

    /**
     * @param boolean $businessCategory
     */
    public function setBusinessCategory($businessCategory)
    {
        $this->businessCategory = $businessCategory;
    }

    /**
     * @return boolean
     */
    public function isIndustry()
    {
        return $this->industry;
    }

    /**
     * @param boolean $industry
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;
    }

    /**
     * @return boolean
     */
    public function isJobType()
    {
        return $this->jobType;
    }

    /**
     * @param boolean $jobType
     */
    public function setJobType($jobType)
    {
        $this->jobType = $jobType;
    }
}