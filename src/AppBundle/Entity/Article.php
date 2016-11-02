<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\ArticleComment;
use ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesDefaults;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 * @ORM\Table(name="article")
 */
class Article
{
    const DRAFT_STATUS = 0;
    const PUBLISHED_STATUS = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * @ORM\Column(type="text")
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity="ArticleSubCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $articleSubCategory;

    /**
     * @ORM\ManyToOne(targetEntity="ArticleCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $articleCategory;

    /**
     * @ORM\OneToMany(targetEntity="ArticleComment", mappedBy="article")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $articleComments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;


    public function __construct()
    {
        $this->articleComments = new ArrayCollection();
    }

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

    public function getTitle()
    {
        return $this->title;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getSummary()
    {
    	return $this->summary;
    }

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

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    public function setArticle($article)
    {
        $this->article = $article;
    }

    public function setArticleComments(ArrayCollection $articleComments)
    {
        $this->articleComments = $articleComments;
    }

    public function setArticleSubCategory(ArticleSubCategory $articleSubCategory)
    {
        $this->articleSubCategory = $articleSubCategory;
    }

    public function setArticleCategory(ArticleCategory $articleCategory)
    {
    	$this->articleCategory = $articleCategory;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}