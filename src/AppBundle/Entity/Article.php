<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\ArticleComment;
use Symfony\Component\HttpFoundation\File\File;

/**
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var string
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
     * @Assert\Image()
     */
    private $image;

    /**
     * @var string
     * 
     * @ORM\Column(type="text")
     * 
     * @Assert\NotBlank()
     */
    private $summary;

    /**
     * @var string
     * 
     * @ORM\Column(type="text")
     * 
     * @Assert\NotBlank()
     */
    private $article;

    /**
     * @var ArticleSubCategory
     * 
     * @ORM\ManyToOne(targetEntity="ArticleSubCategory")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Assert\NotBlank()
     */
    private $articleSubCategory;

    /**
     * @var ArticleCategory
     * 
     * @ORM\ManyToOne(targetEntity="ArticleCategory")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Assert\NotBlank()
     */
    private $articleCategory;

    /**
     * @var ArticleComment
     * 
     * @ORM\OneToMany(targetEntity="ArticleComment", mappedBy="article", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $articleComments;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
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
     * @return ArticleSubCategory
     */
    public function getArticleSubCategory()
    {
        return $this->articleSubCategory;
    }

    /**
     * @return ArticleCategory
     */
    public function getArticleCategory()
    {
        return $this->articleCategory;
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
     * @param ArticleSubCategory $articleSubCategory
     */
    public function setArticleSubCategory(ArticleSubCategory $articleSubCategory)
    {
        $this->articleSubCategory = $articleSubCategory;
    }

    /**
     * @param ArticleCategory $articleCategory
     */
    public function setArticleCategory(ArticleCategory $articleCategory)
    {
        $this->articleCategory = $articleCategory;
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
}