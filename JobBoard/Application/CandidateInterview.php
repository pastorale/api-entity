<?php
namespace AppBundle\Entity\JobBoard\Application;

use AppBundle\Services\Core\Framework\BaseVoterSupportInterface;
use AppBundle\Services\Core\Framework\ListVoterSupportInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity
 * @ORM\Table(name="job__application__interview")
 */
class CandidateInterview implements BaseVoterSupportInterface, ListVoterSupportInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var JobCandidate
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\JobBoard\Application\JobCandidate", inversedBy="interviews")
     * @ORM\JoinColumn(name="id_candidate", referencedColumnName="id")
     * @Serializer\Exclude
     */
    private $candidate;

    /**
     * @var ArrayCollection CandidateAnswer
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\JobBoard\Application\CandidateAnswer", mappedBy="interview")
     * @Serializer\Exclude
     */
    private $answers;

    /**
     * @var \DateTime
     */
    private $deadline;


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
     * @return JobCandidate
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * @param JobCandidate $candidate
     */
    public function setCandidate($candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param \DateTime $deadline
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }



}