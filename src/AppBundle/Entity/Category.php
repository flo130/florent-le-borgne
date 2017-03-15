<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Categorie
 * 
 * @Gedmo\Tree(type="nested")
 * @Gedmo\Loggable
 * 
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var int
     * 
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * 
     * @Gedmo\Versioned
     * 
     * @ORM\Column(length=64)
     * 
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * Gedmo\Slug permet de créer facilement des URLs SEO-Friendly.
     * Ici on met updatable à false, cela indiquera que le slug ne sera jamais mis à jour une fois créé, même si "title" est changé.
     * cf. stof_doctrine_extensions dans config.yml
     * @Gedmo\Slug(fields={"title"}, updatable=true)
     *
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @var int
     * 
     * @Gedmo\TreeLeft
     * 
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @var int
     * 
     * @Gedmo\TreeLevel
     * 
     * @ORM\Column(type="integer")
     */
    private $lvl;

    /**
     * @var int
     * 
     * @Gedmo\TreeRight
     * 
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @var int
     * 
     * @Gedmo\TreeRoot
     * 
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @var int
     * 
     * @Gedmo\TreeParent
     * 
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var int
     * 
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var \DateTime
     * 
     * @Gedmo\Timestampable(on="create")
     * 
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * 
     * @Gedmo\Timestampable(on="update")
     * 
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;


    /**
     * Utile juste pour le redu d'une select 
     * 
     * @return string
     */
    public function getIndentedTitle() {
        return str_repeat('----', $this->lvl) . ' ' . $this->title;
    }

    /**
     * @return number
     */
    public function getId() {
        return $this->id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * @return number
     */
    public function getRoot() {
        return $this->root;
    }

    /**
     * @return number
     */
    public function getLvl() {
        return $this->lvl;
    }

    /**
     * @param Category $parent
     */
    public function setParent(Category $parent = null) {
        $this->parent = $parent;
    }

    /**
     * @return Category
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}