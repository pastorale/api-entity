<?php
// src/AppBundle/Entity/Organisation/Organisation.php

namespace AppBundle\Entity\Organisation;

use AppBundle\Entity\Core\Location\Location;
use AppBundle\Entity\Organisation\Handbook\Handbook;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Model\MediaInterface;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity
 * @ORM\Table(name="organisation")
 *
 * @Serializer\XmlRoot("organisation")
 * @Hateoas\Relation("self", href = @Hateoas\Route(
 *         "get_organisation",
 *         parameters = { "organisation" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *  "handbook",
 *  href= @Hateoas\Route(
 *         "get_organisation_handbook",
 *         parameters = { "organisation" = "expr(object.getId())","handbook" = "expr(object.getHandbook().getId())" },
 *         absolute = true
 *     ),
 *  exclusion=@Hateoas\Exclusion(excludeIf="expr(object.getHandbook() === null)")
 * )
 * @Hateoas\Relation(
 *  "positions",
 *  href= @Hateoas\Route(
 *         "get_organisation_positions",
 *         parameters = { "organisationId" = "expr(object.getId())"},
 *         absolute = true
 *     ),
 *  exclusion=@Hateoas\Exclusion(excludeIf="expr(object.getPositions().count() == 0)")
 * )
 * @Hateoas\Relation(
 *  "sites",
 *  href= @Hateoas\Route(
 *         "get_organisation_sites",
 *         parameters = { "organisationId" = "expr(object.getId())"},
 *         absolute = true
 *     ),
 *  exclusion=@Hateoas\Exclusion(excludeIf="expr(object.getSites().count() == 0)")
 * )
 * @Hateoas\Relation(
 *  "children",
 *  href= @Hateoas\Route(
 *         "get_organisation_children",
 *         parameters = { "organisationId" = "expr(object.getId())"},
 *         absolute = true
 *     ),
 *  exclusion=@Hateoas\Exclusion(excludeIf="expr(object.getChildren().count() == 0)")
 * )
 * @Hateoas\Relation(
 *  "parent",
 *  href= @Hateoas\Route(
 *         "get_organisation_parent",
 *         parameters = { "organisation" = "expr(object.getId())"},
 *         absolute = true
 *     ),
 *  exclusion=@Hateoas\Exclusion(excludeIf="expr(object.getParent() === null)")
 * )
 * @Hateoas\Relation(
 *  "businesses",
 *  href= @Hateoas\Route(
 *         "get_organisation_businesses",
 *         parameters = { "organisationId" = "expr(object.getId())"},
 *         absolute = true
 *     ),
 *  exclusion=@Hateoas\Exclusion(excludeIf="expr(object.getBusinesses().count() == 0)")
 * )
 */
class Organisation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    function __construct()
    {
        $this->children = new ArrayCollection();
        $this->positions = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->businesses = new ArrayCollection();
        $this->sites = new ArrayCollection();
    }
//todo map the following fields
    /**
     * check slide 22
     *
     *
     * regNo, businessType:Tag, headOfficeNo, billingAddress:String, adminUserEmail,
     * reservationEmail, userContactNo, clientSince:Date
     * officeHours:String
     * redemptionPassword:String, merchantCode:String
     * aboutCompany:String
     * Integrate with SonataMediaBundle to store app images along with banner images
     */

    /**
     * @var Handbook
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Organisation\Handbook\Handbook", mappedBy="organisation")
     * @Serializer\Exclude
     **/
    private $handbook;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Organisation\Business", mappedBy="owner", orphanRemoval=true)
     * @Serializer\Exclude
     */
    private $businesses;

    /** @ORM\Column(length=150) */
    private $name;

    //todo map OneToMany with Benefit entity
    /**
     * @var ArrayCollection Benefit
     * @ORM\OneToMany(targetEntity="Benefit", mappedBy="organisation", orphanRemoval=true)
     * @Serializer\Exclude
     */
    private $benefits;

    /** @ORM\Column(length=50) */
    private $code;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Position", mappedBy="employer", orphanRemoval=true)
     * @Serializer\Exclude
     */
    private $positions;

    public function addPosition(Position $position)
    {
        $this->positions->add($position);
        $position->setEmployer($this);
        return $this;
    }
    //todo implement removePosition

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Core\Site", mappedBy="organisation")
     * @Serializer\Exclude
     **/
    private $sites;
    //TODO implement addSite, removeSite

    /**
     * @var integer
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     * @Serializer\Exclude
     */
    private $root;

    /**
     * @var integer
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     * @Serializer\Exclude
     */
    private $lft;

    /**
     * @var integer
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     * @Serializer\Exclude
     */
    private $lvl;

    /**
     * @var integer
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     * @Serializer\Exclude
     */
    private $rgt;

    /**
     * @var Organisation
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Organisation", inversedBy="children")
     * @ORM\JoinColumn(name="id_parent", referencedColumnName="id", onDelete="CASCADE")
     * @Serializer\Exclude
     **/
    private $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Organisation", mappedBy="parent")
     * @Serializer\Exclude
     **/
    private $children;


    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Core\Location\Location")
     * @ORM\JoinColumn(name="id_location", referencedColumnName="id")
     **/
    private $location;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     * @ORM\OneToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY", orphanRemoval=true)
     * @ORM\JoinColumn(name="id_logo", referencedColumnName="id")
     */
    private $logo;

    /**
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Media $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }


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
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getHandbook()
    {
        return $this->handbook;
    }

    /**
     * @param Handbook $handbook
     */
    public function setHandbook(Handbook $handbook)
    {
        $this->handbook = $handbook;
        $handbook->setOrganisation($this);
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
     * @return Organisation
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Organisation $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return ArrayCollection
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @param ArrayCollection $positions
     */
    public function setPositions(ArrayCollection $positions)
    {
        $this->positions = $positions;
    }

    /**
     * @return ArrayCollection
     */
    public function getBusinesses()
    {
        return $this->businesses;
    }

    /**
     * @param ArrayCollection $businesses
     */
    public function setBusinesses(ArrayCollection $businesses)
    {
        $this->businesses = $businesses;
    }

    /**
     * @return ArrayCollection
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * @param ArrayCollection $sites
     */
    public function setSites(ArrayCollection $sites)
    {
        $this->sites = $sites;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     */
    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param mixed $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * @param int $lft
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    }

    /**
     * @return int
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * @param int $lvl
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
    }

    /**
     * @return int
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * @param int $rgt
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }


}