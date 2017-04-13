<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\ArticleComment;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\Category;

/**
 * @Gedmo\Loggable
 * 
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 * @ORM\Table(name="article")
 */
class Article
{
    const DRAFT_STATUS = 0;
    const PUBLISHED_STATUS = 1;

    /**
     * @var number
     * 
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * 
     * @Gedmo\Blameable(on="create")
     * 
     * @ORM\ManyToOne(targetEntity="User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     * 
     * @Gedmo\Versioned
     * 
     * @ORM\Column(type="string")
     * 
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * 
     * @ORM\Column(type="string")
     * 
     * @Assert\NotBlank(groups={"Create"})
     * Assert\Image()
     */
    private $image;

    /**
     * @var string
     * 
     * @Gedmo\Versioned
     * 
     * @ORM\Column(type="text")
     * 
     * @Assert\NotBlank()
     */
    private $summary;

    /**
     * @var string
     * 
     * @Gedmo\Versioned
     * 
     * @ORM\Column(type="text")
     * 
     * @Assert\NotBlank()
     */
    private $article;

    /**
     * @var ArticleCategory
     * 
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(nullable=true)
     * 
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @var ArticleComment
     * 
     * @ORM\OneToMany(targetEntity="ArticleComment", mappedBy="article")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $articleComments;

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
     * @var \DateTime
     * 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var boolean
     * 
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @var string
     * 
     * Gedmo\Slug permet de créer facilement des URLs SEO-Friendly.
     * Ici on met updatable à false, cela indiquera que le slug ne sera jamais mis à jour une fois créé, même si "title" est changé.
     * cf. stof_doctrine_extensions dans config.yml
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * 
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;


    public function __construct()
    {
        $this->articleComments = new ArrayCollection();
    }

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return ArrayCollection
     */
    public function getArticleComments()
    {
        return $this->articleComments;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @param string $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }

    /**
     * @param ArrayCollection $articleComments
     */
    public function setArticleComments(ArrayCollection $articleComments)
    {
        $this->articleComments = $articleComments;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param \DateTime $publishedAt
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
}