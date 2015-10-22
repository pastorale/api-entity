<?php
// src/AppBundle/Entity/JobBoard/Listing.php

namespace AppBundle\Entity\JobBoard\Listing;

use AppBundle\Entity\Accounting\Payroll\Salary;
use AppBundle\Entity\Core\Location\Location;
use AppBundle\Entity\Core\Tag;
use AppBundle\Entity\Core\User\User;
use AppBundle\Entity\Organisation\Organisation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="job_listing")
 *
 * @Serializer\XmlRoot("joblisting")
 * @Hateoas\Relation(
 *  "self",
 *  href= @Hateoas\Route(
 *         "get_joblisting",
 *         parameters = { "listing" = "expr(object.getId())"},
 *         absolute = true
 *     ),
 *  attributes = { "method" = {"put","delete"} },
 * )
 *
 */
class JobListing
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
        $this->tags = new ArrayCollection();
    }

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Core\User\User")
     * @ORM\JoinColumn(name="id_owner", referencedColumnName="id")
     **/
    private $owner;

    /**
     * @var Organisation
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organisation\Organisation")
     * @ORM\JoinColumn(name="id_organisation", referencedColumnName="id")
     **/
    private $organisation;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Core\Location\Location")
     * @ORM\JoinColumn(name="id_location", referencedColumnName="id")
     **/
    private $location;

    /**
     * @var Salary
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Accounting\Payroll\Salary")
     * @ORM\JoinColumn(name="id_salary_from", referencedColumnName="id")
     **/
    private $salaryFrom;

    /**
     * @var Salary
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Accounting\Payroll\Salary")
     * @ORM\JoinColumn(name="id_salary_to", referencedColumnName="id")
     **/
    private $salaryTo;

    /**
     * @var ListingType
     * @ORM\ManyToOne(targetEntity="ListingType")
     * @ORM\JoinColumn(name="id_listing_type", referencedColumnName="id")
     */
    private $listingType;

    /**
     * @var ListingVisibility
     * @ORM\ManyToOne(targetEntity="ListingVisibility")
     * @ORM\JoinColumn(name="id_listing_visibility", referencedColumnName="id")
     */
    private $visibility; // Referenced from Visibility.php

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="InterviewQuestionSet", mappedBy="listing", orphanRemoval=true)
     * @Serializer\Exclude
     */
    private $interviewQuestionSets;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Core\Core\Tag")
     * @ORM\JoinTable(name="job_listings_tags",
     *      joinColumns={@ORM\JoinColumn(name="id_listing", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_tag", referencedColumnName="id")}
     *      )
     **/
    private $tags;

    /**
     * @var \DateTime
     * @ORM\Column(name="expiry_date",type="datetime",nullable=true)
     */
    private $expiryDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="interview_deadline",type="datetime",nullable=true)
     */
    private $interviewDeadline;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean", options={"default":false}, nullable=true)
     */
    private $active;

    /**
     * @var bool
     * @ORM\Column(name="video_interview", type="boolean", options={"default":true}, nullable=true)
     */
    private $videoInterview;

    /**
     * @var bool
     * @ORM\Column(name="multiple_set", type="boolean", options={"default":true}, nullable=true)
     */
    private $multipleSet;


    /**
     * @var int
     * @ORM\Column(name="number_of_set_questions", type="integer", options={"default":0}, nullable=true)
     */
    private $numberOfSetQuestions;

    /**
     * @var int
     * @ORM\Column(name="number_of_sets", type="integer", options={"default":0}, nullable=true)
     */
    private $numberOfSets;

    /**
     * @var int
     * @ORM\Column(name="interview_time_limit", type="integer", options={"default":0}, nullable=true)
     */
    private $interviewTimeLimit;

    /**
     * @var int
     * @ORM\Column(name="question_reading_time_limit", type="integer", options={"default":0}, nullable=true)
     */
    private $questionReadingTimeLimit;

    /**
     * @var string
     * @ORM\Column(length=120, name="title",type="string",nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(length=250, name="description",type="string",nullable=true)
     */
    private $description;


    /** @ORM\Column(length=250, name="qr_code_url",type="string",nullable=true) */
    private $qrCodeURL;

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
    }


    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getQrCodeURL()
    {
        return $this->qrCodeURL;
    }

    /**
     * @param mixed $qrCodeURL
     */
    public function setQrCodeURL($qrCodeURL)
    {
        $this->qrCodeURL = $qrCodeURL;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;
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
     * @return ListingType
     */
    public function getListingType()
    {
        return $this->listingType;
    }

    /**
     * @param ListingType $ListingType
     */
    public function setListingType(ListingType $listingType)
    {
        $this->listingType = $listingType;
    }

    /**
     * @return ListingVisibility
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param ListingVisibility $visibility
     */
    public function setVisibility(ListingVisibility $visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Salary
     */
    public function getSalaryFrom()
    {
        return $this->salaryFrom;
    }

    /**
     * @param Salary $salaryFrom
     */
    public function setSalaryFrom($salaryFrom)
    {
        $this->salaryFrom = $salaryFrom;
    }

    /**
     * @return Salary
     */
    public function getSalaryTo()
    {
        return $this->salaryTo;
    }

    /**
     * @param Salary $salaryTo
     */
    public function setSalaryTo($salaryTo)
    {
        $this->salaryTo = $salaryTo;
    }

    /**
     * @return \DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * @param \DateTime $expiryDate
     */
    public function setExpiryDate(\DateTime $expiryDate)
    {
        $this->expiryDate = $expiryDate;
    }

    /**
     * @return Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param Organisation $organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return \DateTime
     */
    public function getInterviewDeadline()
    {
        return $this->interviewDeadline;
    }

    /**
     * @param \DateTime $interviewDeadline
     */
    public function setInterviewDeadline($interviewDeadline)
    {
        $this->interviewDeadline = $interviewDeadline;
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

    /**
     * @return boolean
     */
    public function isVideoInterview()
    {
        return $this->videoInterview;
    }

    /**
     * @param boolean $videoInterview
     */
    public function setVideoInterview($videoInterview)
    {
        $this->videoInterview = $videoInterview;
    }

    /**
     * @return boolean
     */
    public function isMultipleSet()
    {
        return $this->multipleSet;
    }

    /**
     * @param boolean $multipleSet
     */
    public function setMultipleSet($multipleSet)
    {
        $this->multipleSet = $multipleSet;
    }

    /**
     * @return int
     */
    public function getNumberOfSetQuestions()
    {
        return $this->numberOfSetQuestions;
    }

    /**
     * @param int $numberOfSetQuestions
     */
    public function setNumberOfSetQuestions($numberOfSetQuestions)
    {
        $this->numberOfSetQuestions = $numberOfSetQuestions;
    }

    /**
     * @return int
     */
    public function getNumberOfSets()
    {
        return $this->numberOfSets;
    }

    /**
     * @param int $numberOfSets
     */
    public function setNumberOfSets($numberOfSets)
    {
        $this->numberOfSets = $numberOfSets;
    }

    /**
     * @return int
     */
    public function getInterviewTimeLimit()
    {
        return $this->interviewTimeLimit;
    }

    /**
     * @param int $interviewTimeLimit
     */
    public function setInterviewTimeLimit($interviewTimeLimit)
    {
        $this->interviewTimeLimit = $interviewTimeLimit;
    }

    /**
     * @return int
     */
    public function getQuestionReadingTimeLimit()
    {
        return $this->questionReadingTimeLimit;
    }

    /**
     * @param int $questionReadingTimeLimit
     */
    public function setQuestionReadingTimeLimit($questionReadingTimeLimit)
    {
        $this->questionReadingTimeLimit = $questionReadingTimeLimit;
    }

    /**
     * @return ArrayCollection
     */
    public function getInterviewQuestionSets()
    {
        return $this->interviewQuestionSets;
    }

    /**
     * @param ArrayCollection $interviewQuestionSets
     */
    public function setInterviewQuestionSets($interviewQuestionSets)
    {
        $this->interviewQuestionSets = $interviewQuestionSets;
    }



}