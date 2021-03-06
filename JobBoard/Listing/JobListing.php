<?php

// src/AppBundle/Entity/JobBoard/Listing.php

namespace AppBundle\Entity\JobBoard\Listing;

use AppBundle\Entity\Accounting\Payroll\Salary;
use AppBundle\Entity\Core\Location\Location;
use AppBundle\Entity\Core\Tag;
use AppBundle\Entity\Core\User\User;
use AppBundle\Entity\JobBoard\Application\JobCandidate;
use AppBundle\Entity\Organisation\Organisation;
use AppBundle\Entity\Organisation\Position;
use AppBundle\Services\Core\Framework\BaseVoterSupportInterface;
use AppBundle\Services\Core\Framework\ListVoterSupportInterface;
use AppBundle\Services\Core\Framework\OwnableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\JobBoard\Listing\InterviewQuestionSet;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="job__listing__listing")
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
 * @Hateoas\Relation("job_candidates", href = @Hateoas\Route(
 *         "get_joblisting_jobcandidates",
 *         parameters = { "listing" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 *
 *
 * @Hateoas\Relation(
 *  "creator",
 *  href= @Hateoas\Route(
 *         "get_organisation_position",
 *         parameters = {"organisationId" = "expr(object.getCreator().getEmployer().getId())","position" = "expr(object.getCreator().getId())"},
 *         absolute = true
 *     ),
 *  attributes = { "method" = {"put","delete"} },
 * )
 * @Hateoas\Relation(
 *  "organisation",
 *  href= @Hateoas\Route(
 *         "get_organisation",
 *         parameters = { "organisation" = "expr(object.getOrganisation().getId())"},
 *         absolute = true
 *     ),
 *  attributes = { "method" = {"put","delete"} },
 * )
 * @Hateoas\Relation(
 *  "location",
 *  href= @Hateoas\Route(
 *         "get_location",
 *         parameters = { "location" = "expr(object.getLocation().getId())"},
 *         absolute = true
 *     ),
 *  attributes = { "method" = {"put","delete"} },
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getLocation() === null)"
 *      )
 * )
 * @Hateoas\Relation(
 *  "salary_from",
 *  href= @Hateoas\Route(
 *         "get_salary",
 *         parameters = { "salary" = "expr(object.getSalaryFrom().getId())"},
 *         absolute = true
 *     ),
 *  attributes = { "method" = {"put","delete"} },
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getSalaryFrom() === null)"
 *      )
 * )
 *  * @Hateoas\Relation(
 *  "salary_to",
 *  href= @Hateoas\Route(
 *         "get_salary",
 *         parameters = { "salary" = "expr(object.getSalaryTo().getId())"},
 *         absolute = true
 *     ),
 *  attributes = { "method" = {"put","delete"} },
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getSalaryTo() === null)"
 *      )
 * )
 */
class JobListing implements BaseVoterSupportInterface, ListVoterSupportInterface, OwnableInterface
{

    const VISIBILITY_LISTED = 'LISTED';
    const VISIBILITY_UNLISTED = 'UNLISTED';
    const VISIBILITY_SECURED = 'SECURED';
    const VISIBILITY_INVITATION_ONLY = 'INVITATION_ONLY';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_PENDING = 'PENDING';
    const STATUS_EXPIRED = 'EXPIRED';
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_CLOSED = 'CLOSED';

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
        $this->types = new ArrayCollection();
        $this->candidates = new ArrayCollection();
        $this->interviewQuestionSets = new ArrayCollection();
        $this->enabled = false;
        $this->interviewTimeLimit = 30000;
        $this->questionReadingTimeLimit = 3000;
        $this->mock = false;
        $this->status = self::STATUS_DRAFT;
        $this->visibility = self::VISIBILITY_LISTED;
        $this->createdDate = new \DateTime();
        $this->questionSetCounter = 0;
    }

    /**
     * @return InterviewQuestionSet
     */
    public function fetchCurrentQuestionSet()
    {
        $listing = $this;
        $criteriaQuestionSet = Criteria::create()
//           ->where(Criteria::expr()->eq('enabled',true))
//           ->andWhere(Criteria::expr()->eq('active',true))
            ->setFirstResult($listing->getQuestionSetCounter())
            ->setMaxResults(1);
        $questionSets = $listing->getInterviewQuestionSets()->matching($criteriaQuestionSet);
        $questionSet = $questionSets->get(0);

        //update the question counter
        if ($listing->getQuestionSetCounter() <= $listing->getInterviewQuestionSets()->count() - 2) {
            $listing->setQuestionSetCounter($listing->getQuestionSetCounter() + 1);
        } else {
            $listing->setQuestionSetCounter(0);
        }
        return $questionSet;
    }

    /** @ORM\Column(name="created_date",type="datetime") */
    private $createdDate;

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true , nullable=true)
     */
    private $slug;

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }


    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organisation\Position")
     * @ORM\JoinColumn(name="id_creator", referencedColumnName="id")
     * @Serializer\Exclude
     * */
    private $creator;

    /**
     * @var Organisation
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organisation\Organisation")
     * @ORM\JoinColumn(name="id_organisation", referencedColumnName="id")
     * @Serializer\Exclude
     * */
    private $organisation;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Core\Location\Location",cascade={"merge","persist"})
     * @ORM\JoinColumn(name="id_location", referencedColumnName="id")
     * @Serializer\Exclude
     * */
    private $location;

    /**
     * @var Salary
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Accounting\Payroll\Salary",cascade={"merge","persist"})
     * @ORM\JoinColumn(name="id_salary_from", referencedColumnName="id")
     * @Serializer\Exclude
     * */
    private $salaryFrom;

    /**
     * @var Salary
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Accounting\Payroll\Salary",cascade={"merge","persist"})
     * @ORM\JoinColumn(name="id_salary_to", referencedColumnName="id")
     * @Serializer\Exclude
     * */
    private $salaryTo;

    /**
     * @var ArrayCollection Tag $types
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Core\Classification\Tag",cascade={"merge","persist"})
     * @ORM\JoinTable(name="job__listing__listings_types",
     *      joinColumns={@ORM\JoinColumn(name="id_listing", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_tag", referencedColumnName="id")}
     *      )
     * @Serializer\Exclude
     */
    private $types;

    public function addType($type)
    {
        $this->types->add($type);
        return $this;
    }

    public function removeType($type)
    {
        $this->types->removeElement($type);
        return $this;
    }

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Core\Classification\Tag",cascade={"merge","persist"})
     * @ORM\JoinTable(name="job__listing__listings_tags",
     *      joinColumns={@ORM\JoinColumn(name="id_listing", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_tag", referencedColumnName="id")}
     *      )
     * @Serializer\Exclude
     */
    private $tags;

    public function addTag($tag)
    {
        $this->tags->add($tag);
        return $this;
    }

    public function removeTag($tag)
    {
        $this->tags->removeElement($tag);
        return $this;
    }


    /**
     * @var ArrayCollection InterviewQuestionSet
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\JobBoard\Listing\InterviewQuestionSet", mappedBy="listing",orphanRemoval=true,cascade={"merge","persist","remove"})
     * @Serializer\Exclude
     */
    private $interviewQuestionSets;

    public function addInterviewQuestionSet(InterviewQuestionSet $questionSet)
    {
        $this->interviewQuestionSets->add($questionSet);
        $questionSet->setListing($this);
        return $this;
    }

    public function removeInterviewQuestionSet(InterviewQuestionSet $questionSet)
    {
        $this->interviewQuestionSets->removeElement($questionSet);
        $questionSet->setListing(null);
        return $this;
    }

    /**
     * @var bool
     * @ORM\Column(name="interview_required", type="boolean", options={"default":false}, nullable=true)
     */
    private $interviewRequired;

    /**
     * @var bool
     * @ORM\Column(name="introduction_required", type="boolean", options={"default":false}, nullable=true)
     */
    private $introductionRequired;

    /**
     * @return boolean
     */
    public function isInterviewRequired()
    {
        return $this->interviewRequired;
    }

    /**
     * @param boolean $interviewRequired
     */
    public function setInterviewRequired($interviewRequired)
    {
        $this->interviewRequired = $interviewRequired;
    }

    /**
     * @return boolean
     */
    public function isIntroductionRequired()
    {
        return $this->introductionRequired;
    }

    /**
     * @param boolean $introductionRequired
     */
    public function setIntroductionRequired($introductionRequired)
    {
        $this->introductionRequired = $introductionRequired;
    }

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\JobBoard\Application\JobCandidate", mappedBy="listing",orphanRemoval=true,cascade={"merge","persist","remove"})
     * @Serializer\Exclude
     */
    private $candidates;

    /**
     * @param JobCandidate $candidate
     * @return JobListing
     */
    public function addCandidate($candidate)
    {
        $this->candidates->add($candidate);
        $candidate->setListing($this);
        return $this;
    }

    /**
     * @param JobCandidate $candidate
     * @return JobListing
     */
    public function removeCandidate($candidate)
    {
        $this->candidates->removeElement($candidate);
        $candidate->setListing(null);
        return $this;
    }

    /**
     * @var \DateTime
     * @ORM\Column(name="expiry_date",type="datetime",nullable=true)
     */
    private $expiryDate;

    /**
     * This value is then copied to individual CandidateInterview objects and
     * this is where to hold the actual value of interviewDeadline
     * @var \DateTime
     * @ORM\Column(name="interview_deadline",type="datetime",nullable=true)
     */
    private $interviewDeadline;

    /**
     * @var bool
     * @ORM\Column(name="enabled", type="boolean", options={"default":false}, nullable=true)
     */
    private $enabled;

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
     * var int
     * Column(name="number_of_sets", type="integer", options={"default":0}, nullable=true)
     */
//    private $numberOfSets;

    /**
     * Time limit in mili seconds
     * @var int
     * @ORM\Column(name="interview_time_limit", type="integer", options={"default":300}, nullable=true)
     */
    private $interviewTimeLimit;

    /**
     * @var int
     * @ORM\Column(name="question_reading_time_limit", type="integer", options={"default":30}, nullable=true)
     */
    private $questionReadingTimeLimit;

    /**
     * @var int
     * @ORM\Column(name="question_set_counter", type="integer", options={"default":0}, nullable=true)
     */
    private $questionSetCounter;

    /**
     * @return int
     */
    public function getQuestionSetCounter()
    {
        return $this->questionSetCounter;
    }

    /**
     * @param int $questionSetCounter
     */
    public function setQuestionSetCounter($questionSetCounter)
    {
        $this->questionSetCounter = $questionSetCounter;
    }


    /**
     * @var string
     * @ORM\Column(length=120, name="visibility",type="string",nullable=true)
     */
    private $visibility;

    /**
     * @var string
     * @ORM\Column(length=120, name="code",type="string",nullable=true)
     */
    private $code;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }


    /**
     * @var string
     * @ORM\Column(length=120, name="status",type="string",nullable=true)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(length=120, name="title",type="string",nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(length=500, name="role",type="string",nullable=true)
     */
    private $role;

    /**
     * @var string
     * @ORM\Column(name="description",type="text",nullable=true)
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
    public function setTags($tags)
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
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility)
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
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
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
    public function getCandidates()
    {
        return $this->candidates;
    }

    /**
     * @param ArrayCollection $candidates
     */
    public function setCandidates($candidates)
    {
        $this->candidates = $candidates;
    }

    /**
     * @return Position
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param Position $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="mock", options={"default":false})
     */
    private $mock;

    /**
     * @return bool
     */
    public function isMock()
    {
        return $this->mock;
    }

    /**
     * @param bool $mock
     */
    public function setMock($mock)
    {
        $this->mock = $mock;
    }

    /**
     * @return ArrayCollection
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param ArrayCollection $types
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }

    /**
     * @return ArrayCollection InterviewQuestionSet
     */
    public function getInterviewQuestionSets()
    {
        return $this->interviewQuestionSets;
    }

    /**
     * @param ArrayCollection InterviewQuestionSet $interviewQuestionSets
     */
    public function setInterviewQuestionSets($interviewQuestionSets)
    {
        $this->interviewQuestionSets = $interviewQuestionSets;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUserOwner($user)
    {
        // TODO: Implement setUserOwner() method.
    }

    /**
     * @return User
     */
    public function getUserOwner()
    {
        return $this->creator->getEmployee();
    }

    /**
     * @param Position $position
     * @return $this
     */
    public function setPositionOwner($position)
    {
        // TODO: Implement setPositionOwner() method.
    }

    /**
     * @return Position
     */
    public function getPositionOwner()
    {
        return $this->creator;
    }

    /**
     * @param Organisation $organisation
     * @return $this
     */
    public
    function setOrganisationOwner($organisation)
    {
        // TODO: Implement setOrganisationOwner() method.
    }

    /**
     * @return Organisation
     */
    public
    function getOrganisationOwner()
    {
        return $this->organisation;
    }

    public function setSalary($salary)
    {
        $this->setSalaryFrom($salary['salary_from']);
        $this->setSalaryTo($salary['salary_to']);
    }

    public function getSalary()
    {
        return ['salary_from' => $this->getSalaryFrom(), 'salary_to' => $this->getSalaryTo()];
    }
}
