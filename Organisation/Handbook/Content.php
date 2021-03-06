<?php

// src/AppBundle/Entity/Organisation/Handbook/Section.php

namespace AppBundle\Entity\Organisation\Handbook;

use AppBundle\Services\Core\Framework\BaseVoterSupportInterface;
use AppBundle\Services\Core\Framework\ListVoterSupportInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Gedmo\Translatable\Translatable;

/**
 * @Serializer\XmlRoot("content")
 * @Hateoas\Relation("self",
 *  href= @Hateoas\Route(
 *         "get_organisation_handbook_section_content",
 *         parameters = { "organisation" = "expr(object.getSection().getHandbook().getOrganisation().getId())","handbook" = "expr(object.getSection().getHandbook().getId())","section"="expr(object.getSection().getId())","content"="expr(object.getId())"},
 *         absolute = true
 *     ),
 *   attributes = { "method" = {"put","delete"} },
 * )
 * @Hateoas\Relation("translations",
 *  href= @Hateoas\Route(
 *         "get_organisation_handbook_section_content_translations",
 *        parameters = { "organisation" = "expr(object.getSection().getHandbook().getOrganisation().getId())","handbook" = "expr(object.getSection().getHandbook().getId())","section"="expr(object.getSection().getId())","content"="expr(object.getId())"},
 *         absolute = true
 *     ),
 * )
 * @Hateoas\Relation("image_url",
 *  href= @Hateoas\Route(
 *         "get_organisation_handbook_section_content_image_url",
 *         parameters = { "organisation" = "expr(object.getSection().getHandbook().getOrganisation().getId())","handbook" = "expr(object.getSection().getHandbook().getId())","section"="expr(object.getSection().getId())","content"="expr(object.getId())"},
 *         absolute = true
 *     ),
 * )
 * @Hateoas\Relation("image",
 *  href= @Hateoas\Route(
 *         "get_organisation_handbook_section_content_images",
 *         parameters = { "organisation" = "expr(object.getSection().getHandbook().getOrganisation().getId())","handbook" = "expr(object.getSection().getHandbook().getId())","section"="expr(object.getSection().getId())","content"="expr(object.getId())"},
 *         absolute = true
 *     ),
 * )
 * @Hateoas\Relation("pdf_binary",
 *  href= @Hateoas\Route(
 *         "get_organisation_handbook_section_content_pdf_binary",
 *         parameters = { "organisation" = "expr(object.getSection().getHandbook().getOrganisation().getId())","handbook" = "expr(object.getSection().getHandbook().getId())","section"="expr(object.getSection().getId())","content"="expr(object.getId())"},
 *         absolute = true
 *     ),
 * )
 * @Hateoas\Relation("pdf",
 *  href= @Hateoas\Route(
 *         "get_organisation_handbook_section_content_pdfs",
 *         parameters = { "organisation" = "expr(object.getSection().getHandbook().getOrganisation().getId())","handbook" = "expr(object.getSection().getHandbook().getId())","section"="expr(object.getSection().getId())","content"="expr(object.getId())"},
 *         absolute = true
 *     ),
 * )
 * @Hateoas\Relation(
 *  "section",
 *  href= @Hateoas\Route(
 *         "get_organisation_handbook_section",
 *        parameters = { "organisationId" = "expr(object.getSection().getHandbook().getOrganisation().getId())","handbookId" = "expr(object.getSection().getHandbook().getId())","section"="expr(object.getSection().getId())"},
 *         absolute = true
 *     ),
 * )
 * @Hateoas\Relation("children",
 *  href = @Hateoas\Route(
 *         "get_organisation_handbook_section_content_children",
 *         parameters = { "organisation" = "expr(object.getSection().getHandbook().getOrganisation().getId())","handbook" = "expr(object.getSection().getHandbook().getId())","section"="expr(object.getSection().getId())","content"="expr(object.getId())"},
 *         absolute = true
 *     ),
 *  exclusion=@Hateoas\Exclusion(excludeIf="expr(object.getChildren().count() == 0)")
 * )
 * @Hateoas\Relation("parent",
 *  href= @Hateoas\Route(
 *         "get_organisation_handbook_section_content",
 *         parameters = { "organisation" = "expr(object.getSection().getHandbook().getOrganisation().getId())","handbook" = "expr(object.getSection().getHandbook().getId())","section"="expr(object.getSection().getId())","content"="expr(object.getParent().getId())"},
 *         absolute = true
 *     ),
 *   exclusion=@Hateoas\Exclusion(excludeIf="expr(object.getParent() == null)")
 * )
 * @ORM\Entity
 * @ORM\Table(name="organisation__handbook__content")
 * @Gedmo\Loggable()
 * @Gedmo\TranslationEntity(class="AppBundle\TranslationEntity\Organisation\Handbook\ContentTranslation")
 */
class Content implements BaseVoterSupportInterface, ListVoterSupportInterface
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
        $this->children = new ArrayCollection();
        $this->enabled = true;
    }

    /**
     * @var string
     * @ORM\Column(length=50)
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     */
    private $title;

    /**
     * @var bool
     * @ORM\Column(type="boolean",name="enabled",nullable=true,options={"default":true})
     * @Gedmo\Versioned
     */
    private $enabled = true;

    /**
     * We have to do it this way since at the time of coding, Gedmo\Translatable is not supporting Entity Translation
     * @var int
     * @ORM\Column(name="image_id", type="integer", nullable=true)
     * @Gedmo\Translatable
     * @Serializer\Exclude
     */
    private $imageId;


    /**
     * We have to do it this way since at the time of coding, Gedmo\Translatable is not supporting Entity Translation
     * @var int
     * @ORM\Column(name="pdf_id", type="integer", nullable=true)
     * @Gedmo\Translatable
     * @Serializer\Exclude
     */
    private $pdfId;

    /**
     * @var string
     * @ORM\Column(name="html_text", type="text", nullable=true)
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     */
    private $htmlText;

    /**
     * @var Section
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="contents",cascade={"persist"})
     * @ORM\JoinColumn(name="id_section", referencedColumnName="id", onDelete="CASCADE")
     * @Gedmo\Versioned
     * @Serializer\Exclude
     * */
    private $section;

    /**
     * @var Content
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="children")
     * @ORM\JoinColumn(name="id_parent", referencedColumnName="id", onDelete="CASCADE")
     * @Gedmo\Versioned
     * @Serializer\Exclude
     * */
    private $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Content", mappedBy="parent")
     * @Serializer\Exclude
     * */
    private $children;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param Section $section
     * @return $this
     */
    public function addChild(Section $section)
    {
        $this->children->add($section);
        $section->setParent($this);
        return $this;
    }

    /**
     * Remove a child
     *
     * @param Section $child
     */
    public function removeChild(Section $child)
    {
        $this->children->removeElement($child);
        $child->setParent(null);
        return $this;
    }

    /**
     * @var int
     * @ORM\Column(name="ordering", type="integer",nullable=true,options={"default":0})
     */
    private $ordering;

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
     * @return int
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * @param int $imageId
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
    }

    /**
     * @return string
     */
    public function getHtmlText()
    {
        return $this->htmlText;
    }

    /**
     * @param string $htmlText
     */
    public function setHtmlText($htmlText)
    {
        $this->htmlText = $htmlText;
    }

    /**
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param Section $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }

    /**
     * @return Content
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Content $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
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
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return int
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @param int $ordering
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * @return int
     */
    public function getPdfId()
    {
        return $this->pdfId;
    }

    /**
     * @param int $pdfId
     */
    public function setPdfId($pdfId)
    {
        $this->pdfId = $pdfId;
    }
}
